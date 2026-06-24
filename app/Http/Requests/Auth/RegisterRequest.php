<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'phone'                 => ['nullable', 'digits:10'],
            'password'              => ['required', 'string', 'min:8'],
            'password_confirmation' => ['required_with:password', 'same:password'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required'                      => 'Vui lòng nhập họ tên.',
            'email.required'                     => 'Vui lòng nhập email.',
            'email.email'                        => 'Email không đúng định dạng.',
            'email.unique'                       => 'Email này đã được đăng ký.',
            'password.required'                  => 'Vui lòng nhập mật khẩu.',
            'password.min'                       => 'Mật khẩu phải dài ít nhất 8 ký tự.',
            'password_confirmation.required_with'=> 'Vui lòng xác nhận lại mật khẩu.',
            'password_confirmation.same'         => 'Mật khẩu xác nhận không khớp.',
            'phone.digits'                       => 'Số điện thoại phải bao gồm đúng 10 chữ số.',
        ];
    }
}
