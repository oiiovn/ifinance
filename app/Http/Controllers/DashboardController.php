<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /* ---------- 1. Báo cáo NHẬP HÀNG theo nhà cung cấp ---------- */
        $data = Transaction::where('user_id', auth()->id())
            ->whereIn('type', ['nhaphang', 'congno', 'thanhtoan'])
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'),   fn($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->groupBy('related_name')
            ->select(
                'related_name',

                // Tổng đã thanh toán = nhaphang (ABS) + thanhtoan
                DB::raw('SUM(CASE WHEN type = "nhaphang" THEN ABS(amount) ELSE 0 END) + SUM(CASE WHEN type = "thanhtoan" THEN amount ELSE 0 END) AS total_paid'),

                // Tổng công nợ = congno + thanhtoan
                DB::raw('SUM(CASE WHEN type = "congno" THEN amount ELSE 0 END) + SUM(CASE WHEN type = "thanhtoan" THEN amount ELSE 0 END) AS total_debt'),

                // Tổng giá trị đơn hàng = nhaphang (ABS) + congno (ABS)
                DB::raw('SUM(CASE WHEN type IN ("nhaphang", "congno") THEN ABS(amount) ELSE 0 END) AS total_order')
            )
            ->get();

        /* ---------- 2. Báo cáo CHI cho danh mục Đầu tư ---------- */
        $dautuData = Transaction::where('type', 'chi')
            ->where('user_id', auth()->id())
            ->whereIn('related_name', DB::table('employees')
                ->where('type', 'dautu')
                ->pluck('name'))
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'),   fn($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->groupBy('related_name')
            ->select('related_name', DB::raw('SUM(amount) AS total'))
            ->get();

        /* ---------- 3. Danh sách ví để hiển thị trên dashboard ---------- */
        $wallets = Wallet::where('user_id', auth()->id())->get();
        /* ---------- 3. Báo cáo RÚT TIỀN SHOP ---------- */
        $shopWithdrawals = Transaction::where('type', 'ruttien')
            ->where('user_id', auth()->id())
            ->whereNotNull('related_name')  // chỉ lấy giao dịch có tên shop
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'),   fn($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->groupBy('related_name')
            ->select(
                'related_name',
                DB::raw('SUM(amount) AS total_amount'),
                DB::raw('MAX(created_at) AS last_withdrawal')
            )
            ->get();



        /* ---------- 4. Báo cáo MƯỢN TIỀN ---------- */
        $muontienData = Transaction::where('user_id', auth()->id())
            ->where('type', 'muontien')
            ->whereNotNull('related_name')
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'),   fn($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->groupBy('related_name')
            ->select(
                'related_name',
                DB::raw('SUM(amount) AS total_amount'),
                DB::raw('MAX(created_at) AS last_date')
            )
            ->get();

        return view('dashboard', compact('data', 'wallets', 'dautuData', 'shopWithdrawals', 'muontienData'));
    }
}
