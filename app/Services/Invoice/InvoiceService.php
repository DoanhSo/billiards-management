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

/**
 * Lớp InvoiceService
 * 
 * Xử lý nghiệp vụ thanh toán và xuất hóa đơn (Invoice).
 * Chịu trách nhiệm tổng hợp tiền bàn từ TableSession, tiền sản phẩm (đồ ăn, nước uống),
 * tính toán giảm giá, trừ tồn kho và ghi nhận doanh thu vào hệ thống.
 */
class InvoiceService
{
    /**
     * Lấy danh sách tất cả hóa đơn (có phân trang, lọc theo trạng thái thanh toán).
     *
     * @param string $status Trạng thái thanh toán ('PAID' hoặc 'UNPAID')
     * @param int $perPage Số bản ghi trên trang
     * @return LengthAwarePaginator
     */
    public function getAllInvoices(string $status = '', int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::with(['tableSession.billiardTable', 'staff', 'invoiceDetails.product'])
            ->when($status, fn(Builder $query): Builder => $query->where('payment_status', $status))
            ->latest() // Đưa hóa đơn mới nhất lên đầu
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin chi tiết một hóa đơn theo ID.
     * 
     * Load sẵn các quan hệ để hiển thị chi tiết (bàn, nhân viên lập, danh sách món).
     *
     * @param int $id ID hóa đơn
     * @return Invoice
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getInvoiceById(int $id): Invoice
    {
        return Invoice::with(['tableSession.billiardTable', 'staff', 'invoiceDetails.product'])
            ->findOrFail($id);
    }

    /**
     * Chuẩn bị dữ liệu để đổ ra giao diện lập hóa đơn (Create Invoice).
     * 
     * Kiểm tra phiên chơi đã đóng hay chưa và lấy danh sách sản phẩm còn tồn kho
     * để nhân viên chọn thêm vào hóa đơn.
     *
     * @param int $sessionId ID của phiên chơi
     * @return array<string, mixed> Mảng chứa thông tin 'session' và 'products'
     * @throws ValidationException
     */
    public function prepareInvoice(int $sessionId): array
    {
        $session = TableSession::with(['billiardTable', 'customer', 'invoice'])
            ->findOrFail($sessionId);

        // Bắt buộc phiên chơi phải kết thúc (đã chốt giờ) mới được lên hóa đơn
        if ($session->status !== 'FINISHED') {
            throw ValidationException::withMessages([
                'table_session_id' => ['Phiên chơi chưa kết thúc. Vui lòng kết thúc phiên trước khi lập hóa đơn.'],
            ]);
        }

        // Chống lặp hóa đơn: Mỗi phiên chỉ xuất được 1 hóa đơn
        if ($session->invoice) {
            throw ValidationException::withMessages([
                'table_session_id' => ['Phiên chơi này đã có hóa đơn.'],
            ]);
        }

        // Chỉ lấy những sản phẩm đang được bán (active) và còn hàng trong kho (inStock)
        $products = Product::active()->inStock()->with('category')->get();

        return compact('session', 'products');
    }

    /**
     * Tạo mới hóa đơn và lưu các chi tiết sản phẩm.
     * 
     * Sử dụng DB Transaction để đảm bảo tính toàn vẹn dữ liệu:
     * Nếu có lỗi xảy ra ở bước lưu sản phẩm hoặc trừ tồn kho, toàn bộ quá trình sẽ bị rollback (hủy bỏ).
     *
     * @param array<string, mixed> $data Dữ liệu form lập hóa đơn (discount, items...)
     * @return Invoice Trả về hóa đơn vừa lập
     * @throws ValidationException
     */
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data): Invoice {
            $session = TableSession::with('billiardTable')->findOrFail($data['table_session_id']);

            // Lấy danh sách món ăn/nước uống khách đã gọi
            $items           = $data['items'] ?? [];
            
            // Tính tổng tiền (Tiền bàn + Tiền các món ăn)
            // Ép kiểu float cẩn thận để tránh lỗi với giá trị null
            $subtotal        = (float) $session->table_price + $this->calculateSubtotal($items);
            
            // Xử lý giảm giá (theo % trên tổng bill)
            $discountPercent = (float) ($data['discount_percent'] ?? 0);
            $discount        = round(($subtotal * $discountPercent) / 100, 2);
            
            // Số tiền cuối cùng khách phải trả
            $total           = $this->calculateTotalAmount($subtotal, $discount);

            // 1. Tạo bản ghi hóa đơn chính (Invoice)
            $invoice = Invoice::create([
                'table_session_id' => $session->id,
                'staff_id'         => $data['staff_id'] ?? Auth::id(), // Ai đang thao tác thì ghi tên người đó
                'subtotal'         => $subtotal,
                'discount_percent' => $discountPercent,
                'discount'         => $discount,
                'total_amount'     => $total,
                'payment_method'   => $data['payment_method'],
                'payment_status'   => 'PAID', // Hiện tại mặc định lập xong là Đã thanh toán
            ]);

            // 2. Lưu chi tiết từng sản phẩm và trừ tồn kho
            foreach ($items as $item) {
                $unitPrice  = (float) $item['unit_price'];
                $quantity   = (int) $item['quantity'];
                $totalPrice = $unitPrice * $quantity;

                // Tạo bản ghi chi tiết (InvoiceDetail)
                InvoiceDetail::create([
                    'invoice_id'  => $invoice->id,
                    'product_id'  => $item['product_id'],
                    'quantity'    => $quantity,
                    'unit_price'  => $unitPrice,
                    'total_price' => $totalPrice,
                ]);

                // 3. Trừ số lượng hàng trong kho để phản ánh đúng thực tế
                Product::where('id', $item['product_id'])->decrement('quantity', $quantity);
            }

            return $invoice->load(['tableSession.billiardTable', 'staff', 'invoiceDetails.product']);
        });
    }

    /**
     * Tính tổng tiền của các sản phẩm (Đồ ăn, đồ uống).
     *
     * @param array<int, array{product_id: int, quantity: int, unit_price: float}> $items Danh sách sản phẩm
     * @return float Tổng tiền sản phẩm
     */
    public function calculateSubtotal(array $items): float
    {
        return (float) collect($items)->sum(function (array $item): float {
            return (float) $item['unit_price'] * (int) $item['quantity'];
        });
    }

    /**
     * Tính tổng tiền cuối cùng mà khách phải thanh toán (sau khi trừ giảm giá).
     * Đảm bảo không bao giờ trả về số âm.
     *
     * @param float $subtotal Tổng tiền trước giảm
     * @param float $discount Số tiền được giảm
     * @return float Thành tiền cuối cùng
     */
    public function calculateTotalAmount(float $subtotal, float $discount): float
    {
        return max(0.0, round($subtotal - $discount, 2));
    }

    /**
     * Lấy lịch sử hóa đơn trong một khoảng thời gian nhất định (Từ ngày ... Đến ngày ...).
     *
     * @param string $from Ngày bắt đầu (Format: Y-m-d)
     * @param string $to Ngày kết thúc (Format: Y-m-d)
     * @param int $perPage Số lượng hóa đơn trên 1 trang
     * @return LengthAwarePaginator
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
     * Tính tổng doanh thu hóa đơn trong một khoảng thời gian.
     * Phục vụ cho tính năng Báo cáo / Dashboard.
     *
     * @param string $from Ngày bắt đầu
     * @param string $to Ngày kết thúc
     * @return float Tổng số tiền thu được
     */
    public function getInvoiceRevenue(string $from = '', string $to = ''): float
    {
        return (float) Invoice::when($from, fn(Builder $query): Builder => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn(Builder $query): Builder => $query->whereDate('created_at', '<=', $to))
            ->sum('total_amount');
    }


    /**
     * Lấy 10 hóa đơn mới được lập gần đây nhất.
     * Thường dùng để hiển thị trên màn hình Dashboard tổng quan.
     *
     * @return Collection<int, Invoice>
     */
    public function getRecentInvoices(): Collection
    {
        return Invoice::with(['tableSession.billiardTable', 'staff'])
            ->latest()
            ->limit(10) // Chỉ lấy 10 dòng
            ->get();
    }
}
