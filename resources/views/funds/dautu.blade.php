<style>
    .scroll-table {
        display: block;
        max-height: 300px;
        overflow-y: auto;
    }
</style>

@if(!$dautuData->isEmpty())
<div class="card shadow-sm rounded-3 border-0 mb-4 p-2">
    <div class="card-header bg-success-subtle text-success rounded-top-3 d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-2 mb-md-0">💼 Báo cáo đầu tư</h5>
    </div>

    <div class="card-body p-0 scroll-table">
        <table class="table table-bordered table-sm table-hover m-0">
            <thead class="table-light">
                <tr>
                    <th>Khoản đầu tư</th>
                    <th class="text-end">Tổng tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dautuData->sortByDesc('total') as $item)
                <tr>
                    <td class="text-dark">{{ $item->related_name }}</td>
                    <td class="text-end text-primary fw-semibold">{{ number_format(abs($item->total)) }} đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif