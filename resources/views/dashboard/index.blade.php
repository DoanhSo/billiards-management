{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if(auth()->user()->isAdmin())
        @include('dashboard.admin')
    @elseif(auth()->user()->isStaff())
        @include('dashboard.staff', ['data' => $data])
    @else
        @include('dashboard.customer', ['data' => $data])
    @endif
@endsection
