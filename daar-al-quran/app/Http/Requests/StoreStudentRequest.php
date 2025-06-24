<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Check if user is a teacher and owns the classroom
        $classroom = $this->route('classroom');
        return $this->user() && 
               $this->user()->hasRole('teacher') && 
               $classroom && 
               $classroom->user_id === $this->user()->id;
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
            // Creating new student
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
            'first_name.max' => 'الاسم الأول يجب أن يكون أقل من 255 حرف',
            'last_name.required' => 'اسم العائلة مطلوب',
            'last_name.max' => 'اسم العائلة يجب أن يكون أقل من 255 حرف',
            'birth_year.required' => 'سنة الميلاد مطلوبة',
            'birth_year.digits' => 'سنة الميلاد يجب أن تكون 4 أرقام',
            'birth_year.min' => 'سنة الميلاد غير صحيحة',
            'birth_year.max' => 'سنة الميلاد لا يمكن أن تكون في المستقبل',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'phone.max' => 'رقم الهاتف يجب أن يكون أقل من 20 رقم',
            'address.max' => 'العنوان يجب أن يكون أقل من 500 حرف',
            'existing_student_id.required' => 'يجب اختيار طالب',
            'existing_student_id.exists' => 'الطالب المحدد غير موجود',
        ];
    }
}
