<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateStudentRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_year' => 'required|numeric|digits:4|min:1900|max:' . date('Y'),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email|unique:students,email,' . $this->route('student')->id,
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
            'first_name.required' => 'الاسم الأول مطلوب',
            'first_name.max' => 'الاسم الأول يجب أن يكون أقل من 255 حرف',
            'last_name.required' => 'اسم العائلة مطلوب',
            'last_name.max' => 'اسم العائلة يجب أن يكون أقل من 255 حرف',
            'birth_year.required' => 'سنة الميلاد مطلوبة',
            'birth_year.digits' => 'سنة الميلاد يجب أن تكون 4 أرقام',
            'birth_year.min' => 'سنة الميلاد غير صحيحة',
            'birth_year.max' => 'سنة الميلاد لا يمكن أن تكون في المستقبل',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.max' => 'رقم الهاتف يجب أن يكون أقل من 20 رقم',
            'address.max' => 'العنوان يجب أن يكون أقل من 500 حرف',
        ];
    }
}
