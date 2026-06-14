<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\TableSession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    /**
     * Lấy danh sách tất cả hóa đơn (phân trang, lọc trạng thái thanh toán).
     */
    public function getAllInvoices(string $status = '', int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::with(['tableSession.billiardTable', 'staff', 'invoiceDetails.product'])
            ->when($status, fn(Builder $query): Builder => $query->where('payment_status', $status))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin hóa đơn theo ID.
     */
    public function getInvoiceById(int $id): Invoice
    {
        return Invoice::with(['tableSession.billiardTable', 'staff', 'invoiceDetails.product'])
            ->findOrFail($id);
    }

    /**
     * Chuẩn bị dữ liệu tạo hóa đơn từ phiên chơi.
     *
     * @return array<string, mixed>
     * @throws ValidationException
     */
    public function prepareInvoice(int $sessionId): array
    {
        $session = TableSession::with(['billiardTable', 'customer', 'invoice'])
            ->findOrFail($sessionId);

        if ($session->status !== 'FINISHED') {
            throw ValidationException::withMessages([
                'table_session_id' => ['Phiên chơi chưa kết thúc. Vui lòng kết thúc phiên trước khi lập hóa đơn.'],
            ]);
        }

        if ($session->invoice) {
            throw ValidationException::withMessages([
                'table_session_id' => ['Phiên chơi này đã có hóa đơn.'],
            ]);
        }

        $products = Product::active()->inStock()->with('category')->get();

        return compact('session', 'products');
    }

    /**
     * Tạo hóa đơn và lưu chi tiết sản phẩm.
     *
     * @param array<string, mixed> $data
     * @throws ValidationException
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data): Invoice {
            $session = TableSession::with('billiardTable')->findOrFail($data['table_session_id']);

            // Tính tiền sản phẩm — cast table_price sang float để tránh lỗi decimal|null
            $items    = $data['items'] ?? [];
            $subtotal = (float) $session->table_price + $this->calculateSubtotal($items);
            $discount = (float) ($data['discount'] ?? 0);
            $total    = $this->calculateTotalAmount($subtotal, $discount);

            $invoice = Invoice::create([
                'table_session_id' => $session->id,
                'staff_id'         => $data['staff_id'] ?? Auth::id(),
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'total_amount'     => $total,
                'payment_method'   => $data['payment_method'],
                'payment_status'   => 'PAID',
            ]);

            // Lưu chi tiết sản phẩm trong hóa đơn
            foreach ($items as $item) {
                $unitPrice  = (float) $item['unit_price'];
                $quantity   = (int) $item['quantity'];
                $totalPrice = $unitPrice * $quantity;

                InvoiceDetail::create([
                    'invoice_id'  => $invoice->id,
                    'product_id'  => $item['product_id'],
                    'quantity'    => $quantity,
                    'unit_price'  => $unitPrice,
                    'total_price' => $totalPrice,
                ]);

                // Trừ tồn kho sản phẩm
                Product::where('id', $item['product_id'])->decrement('quantity', $quantity);
            }

            return $invoice->load(['tableSession.billiardTable', 'staff', 'invoiceDetails.product']);
        });
    }

    /**
     * Tính tổng tiền hàng (tiền sản phẩm, chưa giảm giá).
     *
     * @param array<int, array{product_id: int, quantity: int, unit_price: float}> $items
     */
    public function calculateSubtotal(array $items): float
    {
        return (float) collect($items)->sum(function (array $item): float {
            return (float) $item['unit_price'] * (int) $item['quantity'];
        });
    }

    /**
     * Tính tổng tiền thanh toán sau khi giảm giá.
     */
    public function calculateTotalAmount(float $subtotal, float $discount): float
    {
        return max(0.0, round($subtotal - $discount, 2));
    }

    /**
     * Lấy lịch sử hóa đơn theo khoảng thời gian.
     */
    public function getInvoiceHistory(string $from = '', string $to = '', int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::with(['tableSession.billiardTable', 'staff'])
            ->when($from, fn(Builder $query): Builder => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn(Builder $query): Builder => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Tính tổng doanh thu hóa đơn theo khoảng thời gian.
     */
    public function getInvoiceRevenue(string $from = '', string $to = ''): float
    {
        return (float) Invoice::when($from, fn(Builder $query): Builder => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn(Builder $query): Builder => $query->whereDate('created_at', '<=', $to))
            ->sum('total_amount');
    }


    /**
     * Lấy 10 hóa đơn gần nhất.
     *
     * @return Collection<int, Invoice>
     */
    public function getRecentInvoices(): Collection
    {
        return Invoice::with(['tableSession.billiardTable', 'staff'])
            ->latest()
            ->limit(10)
            ->get();
    }
}
