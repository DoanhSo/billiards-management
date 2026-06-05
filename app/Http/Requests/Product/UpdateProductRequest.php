<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status'      => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists'   => 'Danh mục không tồn tại.',
            'name.required'        => 'Vui lòng nhập tên sản phẩm.',
            'price.required'       => 'Vui lòng nhập giá sản phẩm.',
            'price.numeric'        => 'Giá sản phẩm phải là số.',
            'price.min'            => 'Giá sản phẩm không được âm.',
            'quantity.required'    => 'Vui lòng nhập số lượng.',
            'quantity.integer'     => 'Số lượng phải là số nguyên.',
            'quantity.min'         => 'Số lượng không được âm.',
            'image.image'          => 'Ảnh sản phẩm không hợp lệ.',
            'image.max'            => 'Ảnh sản phẩm không được vượt quá 2MB.',
        ];
    }
}
