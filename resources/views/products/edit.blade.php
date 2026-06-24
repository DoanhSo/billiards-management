{{-- resources/views/products/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
<div class="mb-4">
    <a href="{{ route('products.index') }}" class="text-decoration-none">&larr; Quay lại danh sách</a>
</div>

<x-card title="Chỉnh sửa sản phẩm: {{ $product->name }}">
    <form novalidate action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-3 row g-3">
        @csrf
        @method('PUT')

        <div class="col-md-6">
            <x-input name="name" label="Tên sản phẩm" required="true" value="{{ old('name', $product->name) }}" error="{{ $errors->first('name') }}" />
        </div>

        <div class="col-md-6">
            <label for="category_id" class="form-label mb-1">Danh mục <span class="text-danger">*</span></label>
            <select name="category_id" id="category_id" class="form-select {{ $errors->has('category_id') ? 'is-invalid' : '' }}" required style="height: 40px;">
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;"><i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span>{{ $message }}</span></div>
            @enderror
        </div>

        <div class="col-md-4">
            <x-input name="price" type="number" label="Giá bán (VNĐ)" required="true" value="{{ old('price', (int)$product->price) }}" error="{{ $errors->first('price') }}" />
        </div>

        <div class="col-md-4">
            <x-input name="quantity" type="number" label="Số lượng tồn kho" required="true" value="{{ old('quantity', $product->quantity) }}" error="{{ $errors->first('quantity') }}" />
        </div>

        <div class="col-md-4">
            <label for="status" class="form-label mb-1">Trạng thái</label>
            <select name="status" id="status" class="form-select" style="height: 40px;">
                <option value="1" {{ old('status', $product->status ? '1' : '0') === '1' ? 'selected' : '' }}>Đang bán</option>
                <option value="0" {{ old('status', $product->status ? '1' : '0') === '0' ? 'selected' : '' }}>Ngừng bán</option>
            </select>
        </div>

        <div class="col-12">
            <label for="image" class="form-label mb-1">Ảnh sản phẩm</label>
            @if($product->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="100" class="rounded object-fit-cover">
                </div>
            @endif
            <input type="file" name="image" id="image" class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}" accept="image/*">
            <small class="text-muted">Chỉ chọn ảnh mới nếu muốn thay đổi ảnh hiện tại.</small>
            @error('image')
                <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;"><i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span>{{ $message }}</span></div>
            @enderror
        </div>

        <div class="col-12">
            <label for="description" class="form-label mb-1">Mô tả</label>
            <textarea name="description" id="description" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" rows="4">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <div class="d-flex align-items-center gap-1 mt-1" style="color: var(--danger); font-size: 0.82rem;"><i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span>{{ $message }}</span></div>
            @enderror
        </div>

        <div class="col-12 mt-4 d-flex gap-2">
            <x-button type="submit" variant="primary">Cập nhật sản phẩm</x-button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Hủy</a>
        </div>
    </form>
</x-card>
@endsection


