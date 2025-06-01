<div class="card shadow-sm rounded-3 border-0 mb-4 p-2">
    <div class="card-header bg-info text-dark rounded-top-3">
        <h5 class="mb-0">💸 Danh sách đang mượn tiền</h5>
    </div>

    <div class="card-body p-0 scroll-table">
        <table class="table table-bordered table-hover table-sm m-0">
            <thead class="table-light">
                <tr>
                    <th>Người cho mượn</th>
                    <th class="text-end">Tổng tiền</th>
                    <th class="text-end">Gần nhất</th>
                </tr>
            </thead>
            <tbody>
                @forelse($muontienData->sortByDesc('total_amount') as $row)
                <tr>
                    <td>{{ $row->related_name }}</td>
                    <td class="text-end text-primary">{{ number_format($row->total_amount) }} đ</td>
                    <td class="text-end">{{ \Carbon\Carbon::parse($row->last_date)->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Không có giao dịch mượn tiền</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>