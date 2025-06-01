<div class="card shadow-sm mb-4 border p-2">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Rút tiền sàn theo shop</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th>Shop</th>
                    <th>Tổng rút</th>
                    <th>Gần nhất</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shopWithdrawals as $item)
                <tr>
                    <td>{{ $item->related_name }}</td>
                    <td class="text-end text-primary fw-bold">{{ number_format($item->total_amount) }} đ</td>
                    <td>{{ \Carbon\Carbon::parse($item->last_withdrawal)->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Chưa có giao dịch nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
