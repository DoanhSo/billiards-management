<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'          => ['required', 'integer', 'exists:users,id'],
            'billiard_table_id'=> ['required', 'integer', 'exists:billiard_tables,id'],
            'booking_date'     => ['required', 'date'],
            'start_time'       => ['required', 'date_format:Y-m-d H:i:s'],
            'end_time'         => ['required', 'date_format:Y-m-d H:i:s', 'after:start_time'],
            'status'           => ['nullable', 'string', 'in:PENDING,CONFIRMED,CANCELLED,COMPLETED'],
            'note'             => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'           => 'Vui lòng chọn khách hàng.',
            'billiard_table_id.required' => 'Vui lòng chọn bàn.',
            'booking_date.required'      => 'Vui lòng chọn ngày đặt.',
            'start_time.required'        => 'Vui lòng nhập giờ bắt đầu.',
            'end_time.required'          => 'Vui lòng nhập giờ kết thúc.',
            'end_time.after'             => 'Giờ kết thúc phải sau giờ bắt đầu.',
            'status.in'                  => 'Trạng thái không hợp lệ.',
        ];
    }
}
