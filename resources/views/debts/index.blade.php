@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">📌 Quản lý công nợ nhà cung cấp</h4>

    {{-- ✅ Thông báo thành công --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ✅ Thông báo lỗi --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <table class="table table-bordered align-middle text-center">
        <thead class="table-light align-middle">
            <tr>
                <th>Nhà cung cấp</th>
                <th>Còn nợ lại</th>
                <th>Thanh toán</th>
                <th>Nguồn ví</th>
                <th>Nội dung</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($debts as $debt)
            <tr>
                <td>{{ $debt->related_name }}</td>
                @if($debt->total_debt < 0)
                    <td class="text-danger">{{ number_format(abs($debt->total_debt)) }} đ</td>
                    @else
                    <td class="text-success">Đã thanh toán hết</td>
                    @endif


                    <td>
                        <form action="{{ route('congno.pay') }}" method="POST" class="d-flex flex-wrap gap-2 align-items-center">
                            @csrf
                            <input type="hidden" name="supplier_name" value="{{ $debt->related_name }}">

                            {{-- Input hiển thị định dạng --}}
                            <input type="text" class="form-control form-control-sm text-end formatted-amount rounded border" placeholder="Số tiền">
                            {{-- Input thực tế gửi về --}}
                            <input type="hidden" name="amount" class="real-amount">
                    </td>
                    <td>
                        <select name="wallet_id" class="form-select form-select-sm " required>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}">
                                {{ $wallet->name }} - {{ $wallet->bank }} ({{ number_format($wallet->balance) }} đ)
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" name="note" class="form-control form-control-sm  rounded border"
                            placeholder="Ghi chú" value="{{ old('note', 'Thanh toán công nợ') }}">
                    </td>
                    <td>
                        <button type="submit" class="btn btn-sm btn-success rounded border">Thanh toán</button>
                        </form>
                    </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Không còn công nợ nào</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const formatInputs = document.querySelectorAll('.formatted-amount');

        formatInputs.forEach(input => {
            input.addEventListener('input', function() {
                const raw = this.value.replace(/\D/g, '');
                const formatted = new Intl.NumberFormat().format(raw);
                this.value = formatted;

                const hiddenInput = this.closest('form').querySelector('.real-amount');
                hiddenInput.value = raw;
            });
        });
    });
</script>
@endpush