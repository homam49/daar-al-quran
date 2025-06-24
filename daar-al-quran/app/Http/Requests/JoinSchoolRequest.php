<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class JoinSchoolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user() && Auth::user()->hasRole('teacher');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'school_code' => 'required|string|exists:schools,code',
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
            'school_code.required' => 'رمز المدرسة مطلوب',
            'school_code.string' => 'رمز المدرسة يجب أن يكون نصًا',
            'school_code.exists' => 'رمز المدرسة غير صحيح',
        ];
    }
} 