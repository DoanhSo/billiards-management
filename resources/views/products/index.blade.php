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
    <form action="{{ route('products.index') }}" method="GET" class="row g-3 mb-4 p-3 border-bottom">
        <div class="col-md-4">
            <x-input name="search" placeholder="Tìm tên sản phẩm..." value="{{ request('search') }}" />
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-select" style="height: 40px;">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select" style="height: 40px;">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang bán</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ngừng bán</option>
            </select>
        </div>
        <div class="col-md-2">
            <x-button type="submit" variant="secondary" class="w-100">Lọc</x-button>
        </div>
    </form>

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
</x-card>
@endsection
