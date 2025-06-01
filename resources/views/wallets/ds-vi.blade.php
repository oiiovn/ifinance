@if($wallets->count())
<div class="card shadow-sm rounded border mb-0 p-0">
    <div class="card-header bg-primary-subtle text-primary py-2 px-3 rounded-top-3">
        <div class="d-flex align-items-center gap-2">
            <h6 class="card-title mb-0">Tổng:</h6>
            <span class="fw-medium fs-14 text-info">{{ number_format($wallets->sum('balance')) }} đ</span>
        </div>
    </div>
    <div class="card-body py-2 px-3">
        <ul class="list-group list-group-flush">
            @foreach($wallets as $wallet)
            <li class="list-group-item d-flex align-items-center border-0 px-0 py-1">
                @if($wallet->logo_path)
                    <img src="{{ asset($wallet->logo_path) }}" alt="Logo" width="60" class="me-2">
                @endif
                <span class="me-auto fw-semibold small">{{ $wallet->name }}</span>
                <span class="fw-light text-success small">{{ number_format($wallet->balance) }} đ</span>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
