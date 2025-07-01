<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Services\TeacherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    protected $teacherService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeacherService $teacherService)
    {
        $this->middleware(['auth', 'role:teacher', 'approved']);
        $this->teacherService = $teacherService;
    }

    /**
     * Display a listing of messages for the teacher.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        
        // Base query for messages to the teacher
        $messagesQuery = Message::where('recipient_id', Auth::id())
            ->where('sender_type', 'student');
        
        // Apply filter
        switch ($filter) {
            case 'unread':
                $messagesQuery->where('is_read', false);
                break;
            case 'read':
                $messagesQuery->where('is_read', true);
                break;
            case 'class':
                $messagesQuery->whereNotNull('class_room_id');
                break;
            case 'personal':
                $messagesQuery->whereNull('class_room_id');
                break;
        }
        
        $messages = $messagesQuery->orderBy('created_at', 'desc')->paginate(15);
        
        // Also get messages sent by teacher for display, but group broadcast messages
        $sentMessagesQuery = Message::where('sender_id', Auth::id())
            ->where('sender_type', 'teacher');
            
        // Apply same filter
        switch ($filter) {
            case 'class':
                $sentMessagesQuery->whereNotNull('class_room_id');
                break;
            case 'personal':
                $sentMessagesQuery->whereNull('class_room_id');
                break;
        }
        
        $sentMessages = $sentMessagesQuery->orderBy('created_at', 'desc')->get();
        
        // Group broadcast messages (class messages) by subject, content, class_room_id and created_at
        $groupedSentMessages = collect();
        $seenBroadcasts = [];
        
        foreach ($sentMessages as $message) {
            if ($message->type === 'class' && $message->class_room_id) {
                // Create a unique key for this broadcast message
                $key = $message->class_room_id . '|' . $message->subject . '|' . 
                       md5($message->content) . '|' . $message->created_at->format('Y-m-d H:i:s');
                
                // Only add the first occurrence of each broadcast message
                if (!isset($seenBroadcasts[$key])) {
                    $seenBroadcasts[$key] = true;
                    $groupedSentMessages->push($message);
                }
            } else {
                // For personal messages, add them all
                $groupedSentMessages->push($message);
            }
        }
        
        // Combine both collections
        $allMessages = $messages->getCollection()->merge($groupedSentMessages);
        
        // Sort by creation date (newest first)
        $allMessages = $allMessages->sortByDesc('created_at');
        
        // Manually paginate the collection
        $perPage = 15;
        $currentPage = request()->input('page', 1);
        $currentPageItems = $allMessages->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $messages = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $allMessages->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        return view('teacher.messages.index', compact('messages', 'filter'));
    }

    /**
     * Show the form for creating a new message.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classRooms = $this->teacherService->getAccessibleClassrooms();
        
        // Get all students from the teacher's accessible classes
        $studentIds = [];
        foreach ($classRooms as $classroom) {
            $studentIds = array_merge($studentIds, $classroom->students->pluck('id')->toArray());
        }
        
        $students = Student::whereIn('id', $studentIds)->get();
        
        return view('teacher.messages.create', compact('classRooms', 'students'));
    }

    /**
     * Store a newly created message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:personal,class',
            'student_id' => 'required_if:type,personal|nullable|exists:students,id',
            'class_room_id' => 'required_if:type,class|nullable|exists:class_rooms,id',
        ]);

        if ($request->type === 'personal') {
            // Verify that the student is in one of the teacher's accessible classes
            $student = Student::findOrFail($request->student_id);
            $accessibleClassrooms = $this->teacherService->getAccessibleClassrooms();
            $classRoomIds = $accessibleClassrooms->pluck('id');
            
            $isStudentInAccessibleClass = $student->classRooms()
                ->whereIn('class_rooms.id', $classRoomIds)
                ->exists();
            
            if (!$isStudentInAccessibleClass) {
                return back()->with('error', 'الطالب ليس في أي من فصولك');
            }
            
            // Create personal message
            Message::create([
                'subject' => $request->subject,
                'content' => $request->content,
                'type' => 'personal',
                'sender_id' => Auth::id(),
                'sender_type' => 'teacher',
                'student_id' => $request->student_id,
                'is_read' => false,
            ]);
            
            return redirect()->route('teacher.messages')->with('success', 'تم إرسال الرسالة بنجاح');
        } else {
            // Verify that the teacher has access to the classroom
            $classroom = ClassRoom::findOrFail($request->class_room_id);
            
            if (!$this->teacherService->hasAccessToClassroom($classroom->id)) {
                abort(403, 'غير مصرح لك بالوصول إلى هذا الفصل');
            }
            
            // Get all students in the class
            $students = $classroom->students;
            
            if ($students->isEmpty()) {
                return back()->with('error', 'لا يوجد طلاب في هذا الفصل');
            }
            
            // Create a message for each student in the class
            foreach ($students as $student) {
                Message::create([
                    'subject' => $request->subject,
                    'content' => $request->content,
                    'type' => 'class',
                    'sender_id' => Auth::id(),
                    'sender_type' => 'teacher',
                    'student_id' => $student->id,
                    'class_room_id' => $request->class_room_id,
                    'is_read' => false,
                ]);
            }
            
            return redirect()->route('teacher.messages')->with('success', "تم إرسال الإعلان إلى {$students->count()} طالب في الفصل بنجاح");
        }
    }
    
    /**
     * Show form to reply to a message.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reply($id)
    {
        $message = Message::findOrFail($id);
        
        // Only allow replying to messages from students
        if ($message->sender_type != 'student' || $message->recipient_id != Auth::id()) {
            abort(403, 'غير مصرح لك بعرض هذه الرسالة');
        }
        
        return view('teacher.messages.reply', compact('message'));
    }
    
    /**
     * Send a reply to a message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendReply(Request $request, $id)
    {
        $originalMessage = Message::findOrFail($id);
        
        // Only allow replying to messages from students
        if ($originalMessage->sender_type != 'student' || $originalMessage->recipient_id != Auth::id()) {
            abort(403, 'غير مصرح لك بالرد على هذه الرسالة');
        }
        
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        // Create the reply message
        Message::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'type' => 'personal',
            'sender_id' => Auth::id(),
            'sender_type' => 'teacher',
            'student_id' => $originalMessage->sender_id,
            'is_read' => false,
        ]);
        
        // Mark the original message as read
        $originalMessage->is_read = true;
        $originalMessage->read_at = now();
        $originalMessage->save();
        
        return redirect()->route('teacher.messages')
            ->with('success', 'تم إرسال الرد بنجاح');
    }
    
    /**
     * Mark a message as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);
        
        // Ensure the message is directed to this teacher
        if ($message->recipient_id != Auth::id()) {
            abort(403, 'غير مصرح لك بقراءة هذه الرسالة');
        }
        
        // Only update if the message is not already marked as read
        if (!$message->is_read) {
            $message->is_read = true;
            $message->read_at = now();
            $message->save();
        }
        
        return request()->ajax()
            ? response()->json(['success' => true, 'message' => 'تم تمييز الرسالة كمقروءة'])
            : redirect()->back()->with('success', 'تم تمييز الرسالة كمقروءة');
    }
}
