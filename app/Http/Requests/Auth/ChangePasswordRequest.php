<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password'          => ['required', 'string', 'current_password'],
            'new_password'              => ['required', 'string', 'min:8', 'different:current_password'],
            'new_password_confirmation' => ['required_with:new_password', 'same:new_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'               => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password'      => 'Mật khẩu hiện tại không đúng.',
            'new_password.required'                   => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min'                        => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.different'                  => 'Mật khẩu mới không được trùng với mật khẩu hiện tại.',
            'new_password_confirmation.required_with' => 'Vui lòng xác nhận lại mật khẩu mới.',
            'new_password_confirmation.same'          => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}
