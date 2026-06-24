<?php

namespace App\Services\Product;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

/**
 * Lớp CategoryService
 * 
 * Quản lý các nghiệp vụ liên quan đến Danh mục sản phẩm.
 * Bao gồm các thao tác CRUD cơ bản và logic ràng buộc khi xóa danh mục.
 */
class CategoryService
{
    /**
     * Lấy danh sách toàn bộ danh mục sản phẩm đang có trong hệ thống.
     * 
     * Tự động đếm luôn số lượng sản phẩm thuộc từng danh mục để hiển thị ra bảng.
     * Danh sách được sắp xếp theo tên danh mục (A-Z).
     *
     * @return Collection Danh sách các Category (kèm cột products_count)
     */
    public function getAllCategories(): Collection
    {
        return Category::withCount('products')->orderBy('name')->get();
    }

    /**
     * Lấy thông tin chi tiết một danh mục theo ID.
     *
     * @param int $id ID danh mục
     * @return Category
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getCategoryById(int $id): Category
    {
        return Category::withCount('products')->findOrFail($id);
    }

    /**
     * Tạo một danh mục sản phẩm mới.
     *
     * @param array<string, mixed> $data Dữ liệu tên, mô tả danh mục
     * @return Category Trả về đối tượng danh mục vừa tạo
     */
    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Cập nhật thông tin danh mục.
     *
     * @param int $id ID danh mục cần sửa
     * @param array<string, mixed> $data Dữ liệu mới
     * @return Category Trả về đối tượng danh mục sau khi lưu
     */
    public function updateCategory(int $id, array $data): Category
    {
        $category = $this->getCategoryById($id);
        $category->update($data);

        return $category->fresh();
    }

    /**
     * Xóa danh mục khỏi cơ sở dữ liệu.
     * 
     * Ràng buộc an toàn: Chỉ cho phép xóa nếu danh mục này đang trống (không chứa bất kỳ sản phẩm nào).
     * Điều này ngăn ngừa lỗi "mồ côi" dữ liệu khi sản phẩm bị mất danh mục cha.
     *
     * @param int $id ID danh mục cần xóa
     * @return bool True nếu xóa thành công
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException 422 nếu danh mục đang có sản phẩm
     */
    public function deleteCategory(int $id): bool
    {
        $category = $this->getCategoryById($id);

        // Kiểm tra số lượng sản phẩm con, nếu > 0 thì ném lỗi, bắt người dùng phải xóa/chuyển sản phẩm đi trước
        abort_unless(
            $category->products()->count() === 0,
            422,
            'Không thể xóa danh mục đang có sản phẩm.'
        );

        return (bool) $category->delete();
    }
}
