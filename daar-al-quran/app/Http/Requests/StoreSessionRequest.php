<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $classroom = $this->route('classroom');
        $user = $this->user();
        
        if (!$user || !$user->hasRole('teacher') || !$classroom) {
            return false;
        }
        
        // Check if teacher has access to the classroom's school
        $teacherId = $user->id;
        
        // Check if teacher is approved for this school
        $isApprovedForSchool = DB::table('school_teacher')
            ->where('user_id', $teacherId)
            ->where('school_id', $classroom->school_id)
            ->where('is_approved', true)
            ->exists();

        if ($isApprovedForSchool) {
            return true;
        }

        // Also check if teacher has any classroom in this school (legacy support)
        return \App\Models\ClassRoom::where('user_id', $teacherId)
            ->where('school_id', $classroom->school_id)
            ->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'description' => 'nullable|string|max:1000',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'attendance' => 'nullable|array',
            'attendance.*' => 'nullable|in:present,absent,late',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:255',
            'send_message' => 'nullable|boolean',
            'message_title' => 'nullable|string|max:255',
            'message_content' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'topic.required' => 'موضوع الجلسة مطلوب',
            'topic.max' => 'موضوع الجلسة يجب أن يكون أقل من 255 حرف',
            'description.max' => 'وصف الجلسة يجب أن يكون أقل من 1000 حرف',
            'session_date.required' => 'تاريخ الجلسة مطلوب',
            'session_date.date' => 'تاريخ الجلسة غير صحيح',
            'start_time.required' => 'وقت بداية الجلسة مطلوب',
            'start_time.date_format' => 'وقت بداية الجلسة يجب أن يكون بصيغة HH:MM',
            'end_time.required' => 'وقت نهاية الجلسة مطلوب',
            'end_time.date_format' => 'وقت نهاية الجلسة يجب أن يكون بصيغة HH:MM',
            'end_time.after' => 'وقت نهاية الجلسة يجب أن يكون بعد وقت البداية',
            'attendance.*.in' => 'حالة الحضور يجب أن تكون: حاضر، غائب، أو متأخر',
            'notes.*.max' => 'ملاحظة الطالب يجب أن تكون أقل من 255 حرف',
            'message_title.max' => 'عنوان الرسالة يجب أن يكون أقل من 255 حرف',
            'message_content.max' => 'محتوى الرسالة يجب أن يكون أقل من 1000 حرف',
        ];
    }
} 