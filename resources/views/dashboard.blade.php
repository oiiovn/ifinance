@extends('layouts.app')

@section('content')
@php
    // Lấy ngày hôm nay
    $today = now();

    // Gán mặc định from_date là đầu tháng nếu không có request
    $defaultFrom = request('from_date') ?? $today->copy()->startOfMonth()->format('Y-m-d');

    // Gán mặc định to_date là ngày hôm nay nếu không có request
    $defaultTo = request('to_date') ?? $today->format('Y-m-d');
@endphp

{{-- Bộ lọc theo khoảng thời gian --}}
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

{{-- Hàng đầu tiên của báo cáo (3 cột) --}}
<div class="row gx-2"> {{-- gx-2 = khoảng cách ngang giữa các cột khoảng 8px --}}
    
    {{-- Cột 1: Hiển thị danh sách ví nếu có --}}
    <div class="col-md-4">
        @if($wallets->count())
            @include('wallets.ds-vi', ['wallets' => $wallets])
        @endif
    </div>

    {{-- Cột 2: Báo cáo nhập hàng theo nhà cung cấp nếu có dữ liệu --}}
    @if($data->isNotEmpty())
    <div class="col-md-4">
        @include('suppliers.summary')
    </div>
    @endif

    {{-- Cột 3: Báo cáo đầu tư (luôn hiển thị) --}}
    <div class="col-md-4">
        @include('funds.dautu')
    </div>
</div>

{{-- Hàng thứ hai của báo cáo (3 cột) --}}
<div class="row gx-2 mt-2">
    
    {{-- Cột 1: Hiển thị lịch sử rút tiền từ shop nếu có --}}
    <div class="col-md-4">
        @if($shopWithdrawals->count())
            @include('shop.shop_withdrawals', ['shopWithdrawals' => $shopWithdrawals])
        @endif
    </div>

    {{-- Cột 2: Báo cáo mượn tiền nếu có người cho mượn --}}
    @if($muontienData->isNotEmpty())
    <div class="col-md-4">
        @include('borrow-money.muontien')
    </div>
    @endif

    {{-- Cột 3: Để trống hoặc có thể thêm báo cáo khác sau --}}
    <div class="col-md-4">
        {{-- Cột trống hoặc thêm báo cáo khác --}}
    </div>
</div>
@endsection
