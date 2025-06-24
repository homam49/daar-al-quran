<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdateNameRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'current_password' => 'required',
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
            if (!Hash::check($this->current_password, Auth::user()->password)) {
                $validator->errors()->add('current_password', 'كلمة المرور الحالية غير صحيحة');
            }
        });
    }

    /**
     * Get custom error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name.string' => 'الاسم يجب أن يكون نصًا',
            'name.max' => 'الاسم يجب أن يكون أقل من 255 حرفًا',
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
        ];
    }
} 