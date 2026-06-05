<?php

namespace App\Http\Requests\Table;

use Illuminate\Foundation\Http\FormRequest;

class StoreTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_number'   => ['required', 'string', 'max:50', 'unique:billiard_tables,table_number'],
            'table_type'     => ['required', 'string', 'in:POOL,SNOOKER,CAROM'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'status'         => ['nullable', 'string', 'in:AVAILABLE,RESERVED,PLAYING,MAINTENANCE'],
            'description'    => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'table_number.required' => 'Vui lòng nhập số bàn.',
            'table_number.unique'   => 'Số bàn đã tồn tại.',
            'table_type.required'   => 'Vui lòng chọn loại bàn.',
            'table_type.in'         => 'Loại bàn không hợp lệ.',
            'price_per_hour.required' => 'Vui lòng nhập giá theo giờ.',
            'price_per_hour.numeric'  => 'Giá theo giờ phải là số.',
            'price_per_hour.min'      => 'Giá theo giờ không được âm.',
        ];
    }
}
