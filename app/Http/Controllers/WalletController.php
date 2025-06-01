<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::where('user_id', auth()->id())->get();

        $transactions = WalletTransaction::whereHas('fromWallet', function ($q) {
            $q->where('user_id', auth()->id());
        })->orWhereHas('toWallet', function ($q) {
            $q->where('user_id', auth()->id());
        })->latest()->paginate(10);

        return view('wallets.index', compact('wallets', 'transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank' => 'required|string|max:255',
            'balance' => 'nullable|numeric',
        ]);

        $exists = Wallet::where('user_id', auth()->id())
            ->where('name', $request->bank)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Trùng ví có sẵn.');
        }

        $logos = [
            'Vietcombank' => 'logos/vietcombank.png',
            'Techcombank' => 'logos/techcombank.png',
            'MB Bank'     => 'logos/mbbank.png',
            'BIDV'        => 'logos/bidv.png',
            'TPBank'      => 'logos/tpbank.png',
            'Tiền Mặt'    => 'logos/tienmat.png',
            'Viettinbank' => 'logos/viettinbank.png',
            'Hàng tồn'    => 'logos/hangton.png',
        ];

        $wallet = Wallet::create([
            'user_id'   => auth()->id(),
            'name'      => $request->bank,
            'balance'   => $request->balance ?? 0,
            'logo_path' => $logos[$request->bank] ?? null,
            'active'    => false,
            'type' => mb_strtolower(trim($request->bank)) === 'hàng tồn' ? 'hangton' : 'normal',
        ]);

        $hasActiveWallet = Wallet::where('user_id', auth()->id())->where('active', true)->exists();

        if (!$hasActiveWallet) {
            $wallet->active = true;
            $wallet->save();
        }

        return redirect()->route('wallets.index')->with('success', 'Tạo ví thành công!');
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_wallet_id' => 'required|exists:wallets,id',
            'to_wallet_id' => 'required|exists:wallets,id|different:from_wallet_id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $from = Wallet::findOrFail($request->from_wallet_id);
            $to = Wallet::findOrFail($request->to_wallet_id);

            if ($from->user_id !== auth()->id() || $to->user_id !== auth()->id()) {
                abort(403, 'Không có quyền truy cập ví này.');
            }

            if ($from->balance < $request->amount) {
                return redirect()->back()
                    ->withInput()
                    ->with('error_balance', 'Số dư ví gửi không đủ để thực hiện giao dịch.');
            }

            $from->decrement('balance', $request->amount);
            $to->increment('balance', $request->amount);

            WalletTransaction::create([
                'from_wallet_id' => $from->id,
                'to_wallet_id'   => $to->id,
                'amount'         => $request->amount,
                'note'           => $request->note,
            ]);
        });

        return redirect()->route('wallets.index')->with('success', 'Chuyển tiền thành công!');
    }

    public function toggle($id)
    {
        $wallet = Wallet::findOrFail($id);
        // ❌ Không cho phép bật/tắt ví hàng tồn
        if ($wallet->type === 'hangton') {
            
            return redirect()->back()->with('error', 'Không thể bật/tắt ví Hàng tồn.');
        }

        if ($wallet->active) {
            $wallet->active = false;
            $wallet->save();
            return redirect()->back()->with('info', 'Đã tắt ví: ' . $wallet->name);
        } else {
            Wallet::where('user_id', auth()->id())->update(['active' => false]);

            $wallet->active = true;
            $wallet->save();

            return redirect()->back()->with('success', 'Đã bật ví: ' . $wallet->name);
        }
    }
    // Cập nhật số dư ví thủ công
    public function updateBalance(Request $request, $id)
    {
        $request->validate([
            'new_balance' => 'required|numeric|min:0',
        ]);

        $wallet = Wallet::findOrFail($id);

        if ($wallet->user_id !== auth()->id()) {
            abort(403, 'Không có quyền cập nhật ví này.');
        }

        // Lưu giao dịch thay đổi số dư
        $amountChange = $request->new_balance - $wallet->balance;

        DB::transaction(function () use ($wallet, $amountChange, $request) {
            $wallet->balance = $request->new_balance;
            $wallet->save();
            if ($amountChange > 0) {
                WalletTransaction::create([
                    'from_wallet_id' => null,
                    'to_wallet_id'   => $wallet->id,
                    'amount'         => $amountChange,
                    'note'           => 'Điều chỉnh tăng thêm số dư thủ công',
                ]);
            } elseif ($amountChange < 0) {
                WalletTransaction::create([
                    'from_wallet_id' => $wallet->id,
                    'to_wallet_id'   => null,
                    'amount'         => abs($amountChange),
                    'note'           => 'Điều chỉnh giảm xuống số dư thủ công',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Cập nhật số dư thành công!');
    }
}
