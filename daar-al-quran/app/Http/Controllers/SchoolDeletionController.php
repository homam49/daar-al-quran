<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolDeletionController extends Controller
{
    /**
     * Show the form for deleting a school.
     *
     * @return \Illuminate\Http\Response
     */
    public function showForm()
    {
        // Get the current admin's school
        $school = \App\Models\School::where('admin_id', auth()->id())->first();
        
        if (!$school) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'لا يوجد لديك مدرسة لحذفها');
        }
        
        return view('admin.school-deletion', compact('school'));
    }

    /**
     * Confirm school deletion with code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirmDeletion(Request $request)
    {
        // Validate request
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'deletion_code' => 'required|string',
        ], [
            'school_id.required' => 'رمز المدرسة مطلوب',
            'school_id.exists' => 'رمز المدرسة غير صحيح',
            'deletion_code.required' => 'رمز الحذف مطلوب',
        ]);
        
        // Find the school
        $school = School::find($request->school_id);
        
        if (!$school) {
            return back()->with('error', 'المدرسة غير موجودة');
        }

        // Check if deletion_code is null or empty in the database
        if (empty($school->deletion_code)) {
            // Delete the school (will cascade to related models)
            $schoolName = $school->name;
            $school->delete();
            return redirect()->route('admin.dashboard')->with('success', "تم حذف المدرسة '$schoolName' بنجاح");
        }

        if ($school->deletion_code != $request->deletion_code) {
            return back()->with('error', 'رمز الحذف غير صحيح');
        }

        // Get school name before deletion for confirmation message
        $schoolName = $school->name;

        // Delete the school (will cascade to related models)
        $school->delete();

        return redirect()->route('admin.dashboard')->with('success', "تم حذف المدرسة '$schoolName' بنجاح");
    }

    /**
     * Handle the deletion action from the form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deletionAction(Request $request)
    {
        $result = $this->confirmDeletion($request);
        
        if ($request->ajax()) {
            if ($result->getSession()->has('error')) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result->getSession()->get('error')
                ], 422);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => $result->getSession()->get('success'),
                'redirect' => route('admin.dashboard')
            ]);
        }
        
        return $result;
    }

    /**
     * Truncate all tables except moderator accounts.
     * 
     * This is an administrative function that should be used with caution.
     *
     * @return \Illuminate\Http\Response
     */
    public function truncateDatabase()
    {
        // This requires confirmation with a separate form and possibly admin credentials
        if (auth()->user()->type != 'moderator') {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }

        // Delete all schools (which will cascade to classrooms, students, etc.)
        School::query()->delete();

        // Delete all users except moderators
        \App\Models\User::where('type', '!=', 'moderator')->delete();

        return redirect()->route('moderator.dashboard')->with('success', 'تم مسح قاعدة البيانات بنجاح');
    }
} 