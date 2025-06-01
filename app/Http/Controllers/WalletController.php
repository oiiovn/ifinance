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

        $transactions = WalletTransaction::with(['fromWallet', 'toWallet', 'user'])
            ->whereHas('fromWallet', function ($q) {
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

        // Xử lý số tiền nhập vào: loại bỏ dấu . hoặc ,
        $balance = $request->balance ?? $request->amount ?? 0;
        $balance = str_replace(['.', ','], '', $balance);

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
            'balance'   => $balance,
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
            'amount' => 'required|numeric|min:1',
        ]);

        $from = Wallet::find($request->from_wallet_id);
        $to = Wallet::find($request->to_wallet_id);

        // 👉 Không cho chuyển nếu 1 trong 2 ví là hàng tồn
        if ($from->type === 'hangton' || $to->type === 'hangton') {
            return back()->with('error_balance', 'Ví Hàng tồn không được phép chuyển hoặc nhận tiền.');
        }

        // Tiếp tục xử lý chuyển tiền như bình thường
        DB::transaction(function () use ($request, $from, $to) {
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
                'user_id'        => auth()->id(), // 👈 thêm dòng này
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
                    'note'           => 'Điều chỉnh giá trị hàng tồn kho',
                    'user_id'        => auth()->id(), // 👈 thêm dòng này
                ]);
            } elseif ($amountChange < 0) {
                WalletTransaction::create([
                    'from_wallet_id' => $wallet->id,
                    'to_wallet_id'   => null,
                    'amount'         => abs($amountChange),
                    'note'           => 'Điều chỉnh giảm xuống số dư thủ công',
                    'user_id'        => auth()->id(), // 👈 thêm dòng này
                ]);
            }
        });

        return redirect()->back()->with('success', 'Cập nhật số dư thành công!');
    }

    public function destroyTransaction($id)
    {
        $tran = WalletTransaction::findOrFail($id);

        // Kiểm tra quyền
        if (
            optional($tran->fromWallet)->user_id !== auth()->id() &&
            optional($tran->toWallet)->user_id !== auth()->id()
        ) {
            abort(403, 'Không có quyền với giao dịch này.');
        }

        // Kiểm tra thời gian tạo < 3 phút
        if (now()->diffInMinutes($tran->created_at) >= 3) {
            return redirect()->back()->with('error', 'Chỉ có thể huỷ giao dịch trong vòng 3 phút.');
        }

        DB::transaction(function () use ($tran) {
            // Trả lại số dư
            if ($tran->from_wallet_id) {
                $from = Wallet::find($tran->from_wallet_id);
                $from->increment('balance', $tran->amount);
            }

            if ($tran->to_wallet_id) {
                $to = Wallet::find($tran->to_wallet_id);
                $to->decrement('balance', $tran->amount);
            }

            // Xoá giao dịch
            $tran->delete();
        });

        return redirect()->back()->with('success', 'Đã huỷ và khôi phục số dư.');
    }
}
