<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name'   => ['required', 'string', 'max:255'],
            'phone'  => ['nullable', 'digits:10'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Vui lòng nhập họ tên.',
            'name.max'       => 'Họ tên không được quá 255 ký tự.',
            'phone.digits'   => 'Số điện thoại phải bao gồm đúng 10 chữ số.',
            'avatar.image'   => 'File tải lên phải là hình ảnh.',
            'avatar.mimes'   => 'Ảnh đại diện chỉ hỗ trợ định dạng: JPG, JPEG, PNG, WEBP.',
            'avatar.max'     => 'Ảnh đại diện không được vượt quá 2MB.',
        ];
    }
}
