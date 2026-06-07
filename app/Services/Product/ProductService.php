<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * Lấy danh sách sản phẩm (phân trang, tìm kiếm, lọc danh mục, lọc trạng thái).
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
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Lấy thông tin sản phẩm theo ID.
     */
    public function getProductById(int $id): Product
    {
        return Product::with('category')->findOrFail($id);
    }

    /**
     * Tạo sản phẩm mới (xử lý upload ảnh).
     *
     * @param array<string, mixed> $data
     */
    public function createProduct(array $data): Product
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('products', 'public');
        }

        $data['status']   = $data['status'] ?? true;
        $data['quantity'] = $data['quantity'] ?? 0;

        return Product::create($data);
    }

    /**
     * Cập nhật thông tin sản phẩm (xử lý upload ảnh mới).
     *
     * @param array<string, mixed> $data
     */
    public function updateProduct(int $id, array $data): Product
    {
        $product = $this->getProductById($id);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Xóa ảnh cũ nếu tồn tại
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $data['image']->store('products', 'public');
        }

        $product->update($data);

        return $product->fresh('category');
    }

    /**
     * Xóa sản phẩm (chỉ xóa khi không có trong hóa đơn, xóa kèm ảnh).
     */
    public function deleteProduct(int $id): bool
    {
        $product = $this->getProductById($id);

        abort_if(
            $product->invoiceDetails()->exists(),
            422,
            'Không thể xóa sản phẩm đã có trong hóa đơn.'
        );

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return (bool) $product->delete();
    }

    /**
     * Thay đổi trạng thái bán của sản phẩm.
     */
    public function toggleProductStatus(int $id): Product
    {
        $product = $this->getProductById($id);
        $product->update(['status' => !$product->status]);

        return $product;
    }
}
