@extends('layouts.app')

@section('content')
<div class="container">
    <div class="col-12 py-4">


        {{-- Hàng ngang: Form trái - Danh sách phải --}}
      <div class="row align-items-stretch ps-4">
    {{-- Cột bên trái: Form tạo ví --}}
    <div class="col-md-4 pe-3"> {{-- 👉 dùng pe-3 để tạo khoảng cách bên phải --}}
        <div class="bg-info-subtle p-3 rounded w-100 h-100">
            <h2 class="mb-4">Tạo thêm ví</h2>
            <form action="{{ route('wallets.store') }}" method="POST">
                @csrf

                <select name="bank" class="form-select mb-2" required>
                    <option value="">Chọn ngân hàng</option>
                    <option value="Tiền Mặt">Tiền mặt</option>
                    <option value="Hàng tồn">Ví tồn kho</option>
                    <option value="Vietcombank">Vietcombank</option>
                    <option value="Viettinbank">Viettinbank</option>
                    <option value="Techcombank">Techcombank</option>
                    <option value="MB Bank">MB Bank</option>
                    <option value="BIDV">BIDV</option>
                    <option value="TPBank">TPBank</option>
                </select>

                <input class="form-control mb-2 border rounded" type="number" name="balance" placeholder="Số dư ban đầu">

                <div class="d-flex align-items-center gap-3 mt-3">
                    <button type="submit" class="btn btn-success waves-effect waves-light">Tạo ví</button>
                </div>

                @if(session('error'))
                <div class="alert alert-danger mt-3 py-2 px-3">
                    {{ session('error') }}
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Cột bên phải: Danh sách ví --}}
    <div class="col-md-8">
        <div class="bg-warning-subtle p-3 rounded w-100 h-100">
            @if($wallets->count())
            <h5 class="mb-3">
                Tổng số dư: <span class="text-info">{{ number_format($wallets->sum('balance')) }} đ</span>
            </h5>

            <ul class="list-group">
                @foreach($wallets as $wallet)
                <li class="list-group-item d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        @if($wallet->logo_path)
                        <img src="{{ asset($wallet->logo_path) }}" alt="Logo" width="80" height="20" class="me-2">
                        @endif
                        <strong>{{ $wallet->name }}</strong>: {{ number_format($wallet->balance) }} đ
                    </div>
                    <div class="me-3">
                        @if($wallet->type === 'hangton')
                        <form action="{{ route('wallets.updateBalance', $wallet->id) }}" method="POST" class="d-flex align-items-center">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="new_balance" value="{{ $wallet->balance }}" class="form-control form-control-sm me-1 border rounded" style="max-width: 120px;" required>
                            <button type="submit" class="btn btn-outline-secondary btn-sm">💾</button>
                        </form>
                        @endif
                    </div>
                    @if($wallet->type !== 'hangton')
                    <form action="{{ route('wallets.toggle', $wallet->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" onchange="this.form.submit()" {{ $wallet->active ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $wallet->active ? 'Bật' : 'Tắt' }}</label>
                        </div>
                    </form>
                    @else
                    <span class="text-muted small">Ví hệ thống</span>
                    @endif
                </li>
                @endforeach
            </ul>

            @endif
        </div>
    </div>
</div>

    </div>


    {{-- Chuyển tiền --}}

    <div class="col-md-12 ps-4">
        @if(session('error_balance'))
        <div class="alert alert-danger mt-2">
            {{ session('error_balance') }}
        </div>
        @endif
        <div class="bg-secondary-subtle p-3 rounded w-100">

            <h3>Chuyển tiền giữa các ví</h3>
            <form action="{{ route('wallets.transfer') }}" method="POST" class="row g-2 align-items-end mb-4">
                @csrf
                <div class="col-md-2">
                    <select name="from_wallet_id" class="form-select" required>
                        <option value=""> Chọn ví gửi</option>
                        @foreach($wallets as $wallet)
                        <option value="{{ $wallet->id }}">{{ $wallet->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="to_wallet_id" class="form-select" required>
                        <option value=""> Chọn ví nhận</option>
                        @foreach($wallets as $wallet)
                        <option value="{{ $wallet->id }}">{{ $wallet->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 me-2">
                    <input class="border rounded form-control" type="number" step="0.01" min="0" name="amount" required placeholder="Số tiền chuyển">
                </div>

                <div class="col-md-2">
                    <input class="border rounded form-control" type="text" name="note" placeholder="Nội dung">
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-success" @disabled(session('error_balance'))>
                        Chuyển
                    </button>
                </div>
            </form>

            {{-- Lịch sử --}}
            <h3 class="p-2">Lịch sử chuyển tiền</h3>
            <ul class="list-group">
                @foreach($transactions as $tran)
                <li class="list-group-item d-flex flex-column">
                    <div>
                        <span class="">
                            {{ $tran->fromWallet->name ?? 'Hệ thống' }}
                        </span>
                        <i class="mx-1">→</i>
                        <span class="">
                            {{ $tran->toWallet->name ?? 'Hệ thống' }}
                        </span>:

                        @php
                        $isIncrease = is_null($tran->from_wallet_id) && !is_null($tran->to_wallet_id);
                        @endphp
                        <strong class="{{ $isIncrease ? 'text-success' : 'text-danger' }}">
                            {{ number_format($tran->amount) }} đ
                        </strong>
                    </div>

                    @if($tran->note)
                    <small class="text-muted d-block fst-italic">{{ $tran->note }}</small>
                    @endif

                    <small class="text-muted d-block">
                        {{ $tran->created_at->format('d/m/Y H:i') }}
                    </small>
                </li>
                @endforeach
            </ul>
            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection