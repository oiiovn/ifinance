@extends('layouts.app')

@section('content')
@php
    $today = now();
    $defaultFrom = request('from_date') ?? $today->copy()->startOfMonth()->format('Y-m-d');
    $defaultTo   = request('to_date') ?? $today->format('Y-m-d');
@endphp

<div class="d-flex justify-content-end mb-3">
    <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-center">
        <div class="col-auto">
            <input type="date" name="from_date" class="form-control rounded border" value="{{ $defaultFrom }}">
        </div>
        <div class="col-auto">
            <input type="date" name="to_date" class="form-control rounded border" value="{{ $defaultTo }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>
    </form>
</div>

<div class="row gx-2"> {{-- gx-2 = horizontal gutter ~8px --}}
    {{-- Cột 1: Ví --}}
    <div class="col-md-4">
        @if($wallets->count())
            @include('wallets.ds-vi', ['wallets' => $wallets])
        @endif
    </div>

    {{-- Cột 2: Nhập hàng --}}
    <div class="col-md-4">
        @include('suppliers.summary')
    </div>

    {{-- Cột 3: Đầu tư --}}
    <div class="col-md-4">
        @include('funds.dautu')
    </div>
</div>

<div class="row gx-2 mt-2">
    <div class="col-md-4">
        @if($shopWithdrawals->count())
            @include('shop.shop_withdrawals', ['shopWithdrawals' => $shopWithdrawals])
        @endif
    </div>

    <div class="col-md-4">
        @include('borrow-money.muontien')
    </div>

    <div class="col-md-4">
        {{-- Cột trống hoặc thêm báo cáo khác --}}
    </div>
</div>



@endsection
