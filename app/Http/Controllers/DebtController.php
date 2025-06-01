<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
{
public function index(Request $request)
{
    $debts = Transaction::where('user_id', auth()->id())
        ->whereIn('type', ['congno', 'thanhtoan'])
        ->groupBy('related_name')
        ->select(
            'related_name',
            DB::raw('
                SUM(CASE WHEN type = "thanhtoan" THEN ABS(amount) ELSE 0 END) -
                SUM(CASE WHEN type = "congno" THEN ABS(amount) ELSE 0 END)
                AS total_debt
            '),
            DB::raw('SUM(CASE WHEN type = "congno" THEN 1 ELSE 0 END) AS has_congno')
        )
        ->get();

    $wallets = Wallet::where('user_id', auth()->id())->where('active', true)->get();

    return view('debts.index', compact('debts', 'wallets'));
}






    public function pay(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'wallet_id'      => 'required|exists:wallets,id',
            'supplier_name'  => 'required|string|max:255',
            'amount'         => 'required|numeric|min:1000',
            'note'           => 'nullable|string|max:255',
        ]);

        $wallet = Wallet::where('user_id', auth()->id())->findOrFail($request->wallet_id);

        // Công nợ còn lại
        $currentDebt = Transaction::where('user_id', auth()->id())
            ->where('type', 'congno')
            ->where('related_name', $request->supplier_name)
            ->sum('amount');

        $maxCanPay = abs($currentDebt);

        // Kiểm tra số dư ví
        if ($wallet->balance < $request->amount) {
            return redirect()->back()->withInput()->with('error', 'Số dư ví không đủ để thanh toán.');
        }

        // Kiểm tra vượt công nợ
        if ($request->amount > $maxCanPay) {
            return redirect()->back()->withInput()->with('error', 'Số tiền thanh toán vượt quá công nợ hiện tại (' . number_format($maxCanPay) . ' đ).');
        }

        // ✅ Ghi giao dịch thanh toán
        Transaction::create([
            'user_id'      => Auth::user()->id,
            'wallet_id'    => $wallet->id,
            'type'         => 'thanhtoan',
            'amount'       => abs($request->amount),
            'category_id'  => null,
            'note'         => $request->note ?? 'Thanh toán công nợ',
            'related_name' => $request->supplier_name,
        ]);

        // ✅ Trừ tiền ví
        $wallet->decrement('balance', abs($request->amount));

        return redirect()->route('congno.index')->with('success', 'Đã thanh toán công nợ thành công.');
    }
}
