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
                        <div class="input-group">
                            <input type="text" class="form-control rounded-start border" name="amount" data-type="money" placeholder="Số tiền">
                            <span class="input-group-text rounded-end border">VNĐ</span>
                        </div>
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
                                    <input type="text" name="new_balance" value="{{ number_format($wallet->balance) }}" class="form-control form-control-sm me-1 border rounded" style="max-width: 120px;" required data-type="money">
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

            <h3 class="mb-2">Chuyển tiền giữa các ví</h3>
            <form action="{{ route('wallets.transfer') }}" method="POST" class="row g-2 align-items-end mb-4">
                @csrf
                <div class="col-md-2">
                    {{-- Ví gửi --}}
                    <select name="from_wallet_id" class="form-select" required>
                        <option value=""> Chọn ví gửi</option>
                        @foreach($wallets as $wallet)
                        @if($wallet->type !== 'hangton')
                        <option value="{{ $wallet->id }}">{{ $wallet->name }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    {{-- Ví nhận --}}
                    <select name="to_wallet_id" class="form-select" required>
                        <option value=""> Chọn ví nhận</option>
                        @foreach($wallets as $wallet)
                        @if($wallet->type !== 'hangton')
                        <option value="{{ $wallet->id }}">{{ $wallet->name }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 me-2">
                    <div class="input-group">
                        <input class="form-control border rounded-start" type="text" name="amount" required placeholder="Số tiền chuyển" data-type="money">
                        <span class="input-group-text border rounded-end">VNĐ</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <input class="border rounded form-control" type="text" name="note" placeholder="Nội dung" value="Chuyển tiền qua ví">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-success" @disabled(session('error_balance'))>
                        Chuyển
                    </button>
                </div>
            </form>

            {{-- Lịch sử --}}
            <h3 class="mb-2">Lịch sử chuyển tiền</h3>
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
                        <strong class="{{ $isIncrease ? 'text-success' : 'text-info' }}">
                            {{ number_format($tran->amount) }} đ
                        </strong>
                        @php
                        $remainingSeconds = max(0, 180 - $tran->created_at->diffInSeconds(now()));
                        @endphp

                        @if($remainingSeconds > 0)
                        <form action="{{ route('wallets.transactions.destroy', $tran->id) }}" method="POST" class="mt-1 d-inline-block delete-transaction-form"
                            data-id="{{ $tran->id }}" data-created="{{ $tran->created_at->timestamp }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="tooltip"
                                title="Bạn được quyền xoá nó trong 3 phút"
                                onclick="return confirm('Bạn có chắc muốn huỷ giao dịch này?')">
                                🗑️ Xoá giao dịch này trong 3 phút (<span class="countdown" data-id="{{ $tran->id }}">{{ gmdate('i:s', $remainingSeconds) }}</span>)
                            </button>
                        </form>
                        @endif
                    </div>

                    @if($tran->note)
                    <small class="text-muted d-block fst-italic">{{ $tran->note }}</small>
                    @endif

                    <small class="text-muted d-flex align-items-center gap-2">
                        {{ $tran->created_at->format('d/m/Y H:i') }}

                        @if($tran->user)
                        <div style="width: 22px; height: 22px; overflow: hidden; border-radius: 50%; display: inline-block;">
                            <img src="{{ $tran->user->avatar ? Storage::url($tran->user->avatar) : asset('images/avatar.png') }}"
                                alt="{{ $tran->user->name }}"
                                title="{{ $tran->user->name }}"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        @endif
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
<script>
    // Lặp qua các input nhập tiền
    document.querySelectorAll('input[data-type="money"]').forEach(input => {
        input.addEventListener('input', function(e) {
            let raw = this.value.replace(/\D/g, ''); // bỏ tất cả ký tự không phải số
            if (raw === '') {
                this.value = '';
                return;
            }
            this.value = Number(raw).toLocaleString('vi-VN'); // 1234567 => 1.234.567
        });
    });

    // Trước khi submit form, gỡ định dạng (chỉ giữ số)
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            this.querySelectorAll('input[data-type="money"]').forEach(input => {
                input.value = input.value.replace(/\./g, '').replace(/[^0-9]/g, '');
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function() {

        // Xử lý đếm ngược
        const forms = document.querySelectorAll('.delete-transaction-form');
        forms.forEach(form => {
            const created = parseInt(form.dataset.created); // timestamp
            const id = form.dataset.id;
            const countdownSpan = form.querySelector(`.countdown[data-id="${id}"]`);

            const interval = setInterval(() => {
                const now = Math.floor(Date.now() / 1000);
                const elapsed = now - created;
                const remaining = 180 - elapsed;

                if (remaining <= 0) {
                    clearInterval(interval);
                    form.remove(); // ẩn form xoá khi hết hạn
                } else {
                    const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
                    const seconds = String(remaining % 60).padStart(2, '0');
                    countdownSpan.textContent = `${minutes}:${seconds}`;
                }
            }, 1000);
        });
    });
</script>
@endsection