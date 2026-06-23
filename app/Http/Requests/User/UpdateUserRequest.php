<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userParam = $this->route('user') ?? $this->route('staff') ?? $this->route('customer') ?? $this->route('id');
        $userId = $userParam instanceof \App\Models\User ? $userParam->id : $userParam;

        return [
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone'   => ['nullable', 'string', 'max:20'],
            'avatar'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status'  => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique'   => 'Email đã tồn tại trong hệ thống.',
            'avatar.image'   => 'Avatar phải là file ảnh.',
            'avatar.max'     => 'Avatar không được vượt quá 2MB.',
        ];
    }
}
