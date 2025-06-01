@extends('layouts.app')

@section('content')
<div class="container p-2 bg-white shadow-sm rounded">
    <h3 class="m-4 text-danger">Lịch sử giao dịch</h3>

    <form method="GET" action="{{ route('transactions.history') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <select name="wallet_id" class="form-select">
                <option value="">-- Tất cả ví --</option>
                @foreach($wallets as $wallet)
                    <option value="{{ $wallet->id }}" {{ request('wallet_id') == $wallet->id ? 'selected' : '' }}>
                        {{ $wallet->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">-- Loại --</option>
                <option class="badge rounded-pill bg-secondary-subtle text-secondary" value="thu" {{ request('type') == 'thu' ? 'selected' : '' }}>Thu</option>
                <option class="badge rounded-pill bg-danger" value="chi" {{ request('type') == 'chi' ? 'selected' : '' }}>Chi</option>
                <option class="badge rounded-pill bg-info" value="nhaphang" {{ request('type') == 'nhaphang' ? 'selected' : '' }}>Nhập hàng</option>
            </select>
        </div>
        <div class="col-md-4">
            <input class="form-control rounded border" type="text" name="keyword" placeholder="Tìm kiếm ghi chú, người liên quan..." value="{{ request('keyword') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-soft-success waves-effect waves-light material-shadow-none w-100">Lọc</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Ngày</th>
                <th>Ngày giao dịch</th> <!-- ✅ Thêm cột mới -->
                <th>Ví</th>
                <th>Loại</th>
                <th>Số tiền</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tran)
            <tr>
                <td>{{ $tran->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($tran->transaction_date)->format('d/m/Y') }}</td> <!-- ✅ Hiển thị ngày giao dịch -->
                <td>{{ $tran->wallet->name ?? '—' }}</td>
                <td>
                    <span class="badge bg-{{ $tran->type == 'thu' ? 'success' : ($tran->type == 'chi' ? 'danger' : 'info') }}">
                        {{ strtoupper($tran->type) }}
                    </span>
                </td>
                <td>{{ number_format($tran->amount) }} đ</td>
                <td>{{ $tran->related_name ?? $tran->note }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Không có dữ liệu</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $transactions->withQueryString()->links() }}
    </div>
</div>
@endsection
