<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\Product\CategoryService;
use App\Services\Product\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService  $productService,
        private readonly CategoryService $categoryService,
    ) {}

    /**
     * Danh sách sản phẩm (tìm kiếm, lọc danh mục, lọc trạng thái).
     */
    public function index(Request $request): View
    {
        $search     = $request->string('search')->toString();
        $categoryId = $request->integer('category_id');
        $status     = $request->string('status')->toString();
        $products   = $this->productService->getAllProducts($search, $categoryId, $status);
        $categories = $this->categoryService->getAllCategories();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId', 'status'));
    }

    /**
     * Form thêm sản phẩm.
     */
    public function create(): View
    {
        $categories = $this->categoryService->getAllCategories();

        return view('products.create', compact('categories'));
    }

    /**
     * Lưu sản phẩm mới.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->createProduct($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Thêm sản phẩm thành công.');
    }

    /**
     * Form chỉnh sửa sản phẩm.
     */
    public function edit(int $id): View
    {
        $product    = $this->productService->getProductById($id);
        $categories = $this->categoryService->getAllCategories();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Cập nhật sản phẩm.
     */
    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $this->productService->updateProduct($id, $request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Cập nhật sản phẩm thành công.');
    }

    /**
     * Xóa sản phẩm.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->productService->deleteProduct($id);

        return redirect()->route('products.index')
            ->with('success', 'Xóa sản phẩm thành công.');
    }
}
