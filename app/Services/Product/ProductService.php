<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

/**
 * Lớp ProductService
 * 
 * Quản lý các nghiệp vụ liên quan đến sản phẩm (Đồ ăn, đồ uống, phụ kiện...).
 * Bao gồm: tạo, sửa, xóa (kèm quản lý file ảnh), tìm kiếm và quản lý trạng thái bán.
 */
class ProductService
{
    /**
     * Lấy danh sách sản phẩm (có phân trang, tìm kiếm, lọc danh mục, lọc trạng thái).
     *
     * @param string $search Từ khóa tìm kiếm theo tên
     * @param int $categoryId ID danh mục (0 = Lấy tất cả)
     * @param string $status Trạng thái bán ('1' = đang bán, '0' = ngừng bán)
     * @param int $perPage Số lượng bản ghi trên một trang
     * @return LengthAwarePaginator
     */
    public function getAllProducts(
        string $search = '',
        int $categoryId = 0,
        string $status = '',
        int $perPage = 15
    ): LengthAwarePaginator {
        return Product::with('category')
            ->when($search, fn(Builder $query): Builder => $query->where('name', 'like', "%{$search}%"))
            ->when($categoryId, fn(Builder $query): Builder => $query->where('category_id', $categoryId))
            ->when($status !== '', fn(Builder $query): Builder => $query->where('status', (bool) $status))
            ->latest() // Đưa sản phẩm mới nhất lên đầu
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin chi tiết một sản phẩm theo ID.
     * 
     * Kèm theo thông tin của danh mục (category) chứa sản phẩm đó.
     *
     * @param int $id ID của sản phẩm
     * @return Product
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getProductById(int $id): Product
    {
        return Product::with('category')->findOrFail($id);
    }

    /**
     * Khởi tạo một sản phẩm mới.
     * 
     * Hỗ trợ lưu trữ file ảnh đại diện của sản phẩm vào thư mục 'storage/app/public/products'.
     *
     * @param array<string, mixed> $data Dữ liệu form nhập (name, price, category_id, image...)
     * @return Product Trả về đối tượng sản phẩm vừa tạo
     */
    public function createProduct(array $data): Product
    {
        // Kiểm tra và lưu file ảnh nếu có
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('products', 'public');
        }

        // Đặt giá trị mặc định nếu người dùng không truyền lên
        $data['status']   = $data['status'] ?? true;
        $data['quantity'] = $data['quantity'] ?? 0;

        return Product::create($data);
    }

    /**
     * Cập nhật thông tin của sản phẩm.
     * 
     * Nếu có ảnh mới upload lên, hệ thống sẽ tự động xóa file ảnh cũ trên ổ cứng
     * để tiết kiệm dung lượng, sau đó mới lưu ảnh mới.
     *
     * @param int $id ID của sản phẩm cần cập nhật
     * @param array<string, mixed> $data Dữ liệu cập nhật
     * @return Product Trả về đối tượng sản phẩm sau khi đã update
     */
    public function updateProduct(int $id, array $data): Product
    {
        $product = $this->getProductById($id);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Xóa file ảnh cũ khỏi đĩa nếu sản phẩm này trước đó đã có ảnh
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Lưu ảnh mới
            $data['image'] = $data['image']->store('products', 'public');
        }

        $product->update($data);

        return $product->fresh('category');
    }

    /**
     * Xóa hoàn toàn một sản phẩm khỏi hệ thống.
     * 
     * Chú ý an toàn dữ liệu: Không được phép xóa sản phẩm nếu nó đã từng nằm
     * trong bất kỳ hóa đơn nào (để bảo vệ lịch sử thống kê doanh thu).
     *
     * @param int $id ID sản phẩm cần xóa
     * @return bool True nếu xóa thành công
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 422 nếu sản phẩm đang kẹt trong hóa đơn
     */
    public function deleteProduct(int $id): bool
    {
        $product = $this->getProductById($id);

        // Kiểm tra ràng buộc khóa ngoại (Foreign key) với bảng invoice_details
        abort_if(
            $product->invoiceDetails()->exists(),
            422,
            'Không thể xóa sản phẩm đã có trong hóa đơn.'
        );

        // Xóa file ảnh vật lý để giải phóng dung lượng
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return (bool) $product->delete();
    }

    /**
     * Đảo ngược trạng thái kinh doanh của sản phẩm.
     * (Từ đang bán -> Ngừng bán, và ngược lại).
     *
     * @param int $id ID sản phẩm
     * @return Product Đối tượng sau khi đổi trạng thái
     */
    public function toggleProductStatus(int $id): Product
    {
        $product = $this->getProductById($id);
        $product->update(['status' => !$product->status]);

        return $product;
    }
}
