<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_session_id'       => ['required', 'integer', 'exists:table_sessions,id'],
            'staff_id'               => ['nullable', 'integer', 'exists:users,id'],
            'discount'               => ['nullable', 'numeric', 'min:0'],
            'payment_method'         => ['required', 'string', 'in:CASH,BANKING'],
            'items'                  => ['nullable', 'array'],
            'items.*.product_id'     => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1'],
            'items.*.unit_price'     => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'table_session_id.required'   => 'Vui lòng chọn phiên chơi.',
            'table_session_id.exists'     => 'Phiên chơi không tồn tại.',
            'payment_method.required'     => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in'           => 'Phương thức thanh toán không hợp lệ.',
            'items.*.product_id.required' => 'Vui lòng chọn sản phẩm.',
            'items.*.quantity.required'   => 'Vui lòng nhập số lượng.',
            'items.*.quantity.min'        => 'Số lượng tối thiểu là 1.',
        ];
    }
}
