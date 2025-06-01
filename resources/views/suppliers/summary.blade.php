<div class="card shadow-sm rounded-3 border-0 m-4 p-2">
    <div class="card-header bg-warning text-dark rounded-top-3 ">
        <div class="d-flex justify-content-between flex-wrap align-items-center">
            <h5 class="mb-3 mb-md-2">📦 Báo cáo nhập hàng theo nhà cung cấp</h5>
        </div>
    </div>

    <div class="card-body p-0 scroll-table">
        <table class="table table-bordered table-hover table-sm m-0">
            <thead class="table-light">
                <tr>
                    <th>Nhà cung cấp</th>
                    <th class="text-end">Giá trị đơn hàng</th>
                    <th class="text-end text-success">Đã thanh toán</th>
                    <th class="text-end text-danger">Còn nợ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data->sortByDesc('total_order') as $row)
                <tr>
                    <td>{{ $row->related_name ?? '(Không xác định)' }}</td>
                    <td class="text-end fw-semibold">{{ number_format($row->total_order) }} đ</td>
                    <td class="text-end text-success">{{ number_format($row->total_paid) }} đ</td>
                    <td class="text-end text-danger">{{ number_format($row->total_debt) }} đ</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Không có dữ liệu</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
