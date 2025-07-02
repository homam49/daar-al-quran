<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class MemorizationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Check if user is a teacher and has access to the student
        if (!$this->user() || !$this->user()->hasRole('teacher')) {
            return false;
        }

        $student = $this->route('student');
        if (!$student) {
            return false;
        }

        // Check if teacher has access to this student through any classroom in schools they belong to
        $teacherId = $this->user()->id;
        
        // Get schools where teacher is approved
        $approvedSchoolIds = DB::table('school_teacher')
            ->where('user_id', $teacherId)
            ->where('is_approved', true)
            ->pluck('school_id')
            ->toArray();
        
        // Get schools where teacher has created classrooms (legacy support)
        $classroomSchoolIds = \App\Models\ClassRoom::where('user_id', $teacherId)
            ->pluck('school_id')
            ->toArray();
        
        // Combine and get unique school IDs
        $allSchoolIds = array_unique(array_merge($approvedSchoolIds, $classroomSchoolIds));
        
        // Get all classrooms in these schools
        $accessibleClassroomIds = \App\Models\ClassRoom::whereIn('school_id', $allSchoolIds)
            ->pluck('id')
            ->toArray();
        
        return $student->classRooms()->whereIn('class_room_id', $accessibleClassroomIds)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|in:page,surah,juz',
            'page_number' => 'nullable|integer|between:1,581',
            'surah_number' => 'nullable|integer|between:78,114',
            'juz_number' => 'nullable|integer|between:1,30',
            'status' => 'required|in:not_started,in_progress,memorized,previously_memorized',
            'notes' => 'nullable|string|max:1000'
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
            'type.required' => 'نوع المحتوى مطلوب',
            'type.in' => 'نوع المحتوى يجب أن يكون صفحة أو سورة أو جزء',
            'page_number.integer' => 'رقم الصفحة يجب أن يكون رقم صحيح',
            'page_number.between' => 'رقم الصفحة يجب أن يكون بين 1 و 581',
            'surah_number.integer' => 'رقم السورة يجب أن يكون رقم صحيح',
            'surah_number.between' => 'رقم السورة يجب أن يكون بين 78 و 114',
            'juz_number.integer' => 'رقم الجزء يجب أن يكون رقم صحيح',
            'juz_number.between' => 'رقم الجزء يجب أن يكون بين 1 و 30',
            'status.required' => 'حالة الحفظ مطلوبة',
            'status.in' => 'حالة الحفظ غير صحيحة',
            'notes.max' => 'الملاحظات يجب أن تكون أقل من 1000 حرف'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            
            if ($type === 'page' && !$this->has('page_number')) {
                $validator->errors()->add('page_number', 'رقم الصفحة مطلوب عند اختيار نوع صفحة');
            }
            
            if ($type === 'surah' && !$this->has('surah_number')) {
                $validator->errors()->add('surah_number', 'رقم السورة مطلوب عند اختيار نوع سورة');
            }
            
            if ($type === 'juz' && !$this->has('juz_number')) {
                $validator->errors()->add('juz_number', 'رقم الجزء مطلوب عند اختيار نوع جزء');
            }
        });
    }
}
