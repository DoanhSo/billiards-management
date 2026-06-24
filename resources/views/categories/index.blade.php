{{-- resources/views/categories/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Quản lý danh mục')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0">Danh sách danh mục</h2>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        + Thêm danh mục
    </a>
</div>


<x-card>
    <x-table>
        <x-slot:thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Số sản phẩm</th>
                <th class="text-end">Hành động</th>
            </tr>
        </x-slot:thead>

        @forelse($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td class="fw-bold">{{ $category->name }}</td>
                <td>{{ $category->description ?? '--' }}</td>
                <td>
                    <x-badge color="info">{{ $category->products_count }}</x-badge>
                </td>
                <td class="text-end">
                    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary me-1">Sửa</a>
                    
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline form-delete" data-name="danh mục {{ $category->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-4">Chưa có danh mục nào.</td>
            </tr>
        @endforelse
    </x-table>
</x-card>
@endsection

