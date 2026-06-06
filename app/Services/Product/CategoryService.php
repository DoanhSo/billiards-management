<?php

namespace App\Services\Product;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Lấy tất cả danh mục (kèm số lượng sản phẩm).
     */
    public function getAllCategories(): Collection
    {
        return Category::withCount('products')->orderBy('name')->get();
    }

    /**
     * Lấy thông tin danh mục theo ID.
     */
    public function getCategoryById(int $id): Category
    {
        return Category::withCount('products')->findOrFail($id);
    }

    /**
     * Tạo danh mục mới.
     *
     * @param array<string, mixed> $data
     */
    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Cập nhật danh mục.
     *
     * @param array<string, mixed> $data
     */
    public function updateCategory(int $id, array $data): Category
    {
        $category = $this->getCategoryById($id);
        $category->update($data);

        return $category->fresh();
    }

    /**
     * Xóa danh mục (chỉ cho phép xóa khi không có sản phẩm).
     */
    public function deleteCategory(int $id): bool
    {
        $category = $this->getCategoryById($id);

        abort_unless(
            $category->products()->count() === 0,
            422,
            'Không thể xóa danh mục đang có sản phẩm.'
        );

        return (bool) $category->delete();
    }
}
