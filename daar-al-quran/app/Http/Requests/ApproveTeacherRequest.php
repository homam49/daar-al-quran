<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class ApproveTeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Check if user is admin
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            return false;
        }
        
        // Check if school belongs to this admin
        $school = School::where('id', $this->school_id)
            ->where('admin_id', Auth::id())
            ->first();
            
        return $school != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'school_id' => 'required|exists:schools,id',
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required' => 'معرف المعلم مطلوب',
            'user_id.exists' => 'المعلم غير موجود',
            'school_id.required' => 'معرف المدرسة مطلوب',
            'school_id.exists' => 'المدرسة غير موجودة',
        ];
    }
} 