{{-- resources/views/categories/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Thêm danh mục')

@section('content')
<div class="mb-4">
    <a href="{{ route('categories.index') }}" class="text-decoration-none">&larr; Quay lại danh sách</a>
</div>

<x-card title="Thêm danh mục mới">
    <form action="{{ route('categories.store') }}" method="POST" class="p-3">
        @csrf

        <div class="mb-3">
            <x-input name="name" label="Tên danh mục" required="true" value="{{ old('name') }}" error="{{ $errors->first('name') }}" />
        </div>

        <div class="mb-4">
            <label for="description" class="form-label mb-1">Mô tả</label>
            <textarea name="description" id="description" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" rows="4">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <x-button type="submit" variant="primary">Lưu danh mục</x-button>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Hủy</a>
        </div>
    </form>
</x-card>
@endsection
