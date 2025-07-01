<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreStudentRequest extends FormRequest
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
        $rules = [];

        // If adding existing student
        if ($this->has('existing_student_id')) {
            $rules['existing_student_id'] = [
                'required',
                'exists:students,id',
                function ($attribute, $value, $fail) {
                    $student = \App\Models\Student::find($value);
                    $classroom = $this->route('classroom');
                    
                    // Check if student is in same school
                    if ($student && $student->school_id != $classroom->school_id) {
                        $fail('الطالب ليس من نفس المدرسة');
                    }
                    
                    // Check if student is already in class
                    if ($student && $classroom->students->contains($student->id)) {
                        $fail('الطالب موجود بالفعل في هذا الفصل');
                    }
                }
            ];
        } else {
            // If creating new student
            $rules = [
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'birth_year' => 'required|numeric|digits:4|min:1900|max:' . date('Y'),
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'email' => 'nullable|email|unique:students,email',
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'اسم العائلة مطلوب',
            'birth_year.required' => 'سنة الميلاد مطلوبة',
            'birth_year.digits' => 'سنة الميلاد يجب أن تكون 4 أرقام',
            'birth_year.min' => 'سنة الميلاد غير صحيحة',
            'birth_year.max' => 'سنة الميلاد لا يمكن أن تكون في المستقبل',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'existing_student_id.required' => 'يجب اختيار طالب',
            'existing_student_id.exists' => 'الطالب المختار غير موجود',
        ];
    }
}
