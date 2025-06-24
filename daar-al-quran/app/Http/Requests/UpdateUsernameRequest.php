<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUsernameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|string|min:4|max:255|unique:users,username,' . Auth::id(),
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
            'username.required' => 'اسم المستخدم مطلوب',
            'username.string' => 'اسم المستخدم يجب أن يكون نصًا',
            'username.min' => 'اسم المستخدم يجب أن يكون 4 أحرف على الأقل',
            'username.max' => 'اسم المستخدم يجب أن يكون أقل من 255 حرفًا',
            'username.unique' => 'اسم المستخدم مُستخدم بالفعل',
        ];
    }
} 