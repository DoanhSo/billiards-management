<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Services\Product\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    /**
     * Danh sách danh mục.
     */
    public function index(): View
    {
        $categories = $this->categoryService->getAllCategories();

        return view('categories.index', compact('categories'));
    }

    /**
     * Form thêm danh mục.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Lưu danh mục mới.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->createCategory($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Thêm danh mục thành công.');
    }

    /**
     * Form chỉnh sửa danh mục.
     */
    public function edit(int $id): View
    {
        $category = $this->categoryService->getCategoryById($id);

        return view('categories.edit', compact('category'));
    }

    /**
     * Cập nhật danh mục.
     */
    public function update(UpdateCategoryRequest $request, int $id): RedirectResponse
    {
        $this->categoryService->updateCategory($id, $request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Cập nhật danh mục thành công.');
    }

    /**
     * Xóa danh mục.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->categoryService->deleteCategory($id);

        return redirect()->route('categories.index')
            ->with('success', 'Xóa danh mục thành công.');
    }
}
