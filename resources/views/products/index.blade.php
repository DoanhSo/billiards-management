{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Quản lý sản phẩm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0">Danh sách sản phẩm</h2>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        + Thêm sản phẩm
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<x-card>
    <div class="filter-bar mb-0" style="padding: 16px 0 16px 0;">
        <form action="{{ route('products.index') }}" method="GET" class="ajax-search-form">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text text-muted border-end-0" style="background: transparent;"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" style="height: 40px;"
                               placeholder="Tên sản phẩm..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" class="form-select" style="height: 40px;">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select" style="height: 40px;">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>🟢 Đang bán</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>🔴 Ngừng bán</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-funnel-fill me-1"></i> Lọc
                    </button>
                    @if(request('search') || request('category_id') || request('status'))
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" style="height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div id="searchable-content">
        <x-table>
            <x-slot:thead>
            <tr>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th class="text-end">Hành động</th>
            </tr>
        </x-slot:thead>

        @forelse($products as $product)
            <tr>
                <td>
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="50" height="50" class="rounded object-fit-cover">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px; font-size: 10px;">No img</div>
                    @endif
                </td>
                <td class="fw-bold">{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td>{{ number_format($product->price) }} đ</td>
                <td>{{ $product->quantity }}</td>
                <td>
                    @if($product->status)
                        <x-badge color="success">Đang bán</x-badge>
                    @else
                        <x-badge color="secondary">Ngừng bán</x-badge>
                    @endif
                </td>
                <td class="text-end">
                    <form action="{{ route('products.toggle', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-warning me-1">
                            {{ $product->status ? 'Ngừng bán' : 'Mở bán' }}
                        </button>
                    </form>
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary me-1">Sửa</a>
                    
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4">Không tìm thấy sản phẩm nào.</td>
            </tr>
        @endforelse
        </x-table>
        
        <div class="px-3 py-2">
            {{ $products->links() }}
        </div>
    </div>
</x-card>
@endsection

