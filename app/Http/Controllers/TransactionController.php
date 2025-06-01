<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['category', 'wallet'])
            ->where('user_id', optional(auth()->user())->id)
            ->latest()
            ->get();

        $categories = Category::where(function ($q) {
            $q->where('user_id', auth()->id())
                ->orWhereNull('user_id');
        })->get();
        $employees = DB::table('employees')
            ->where('type', 'employee')
            ->where('user_id', auth()->id()) // ✅ lọc theo user
            ->pluck('name');

        $shops = DB::table('employees')
            ->where('type', 'shop')
            ->where('user_id', auth()->id())
            ->pluck('name');

        $dautus = DB::table('employees')
            ->where('type', 'dautu')
            ->where('user_id', auth()->id())
            ->pluck('name');

        $nhacungcaps = DB::table('employees')
            ->where('type', 'nhacungcap')
            ->where('user_id', auth()->id()) // ✅ lọc theo user đang đăng nhập
            ->pluck('name');
        $chovays = DB::table('employees')
            ->where('type', 'chovay')
            ->where('user_id', auth()->id())
            ->pluck('name');

        $muontiens = DB::table('employees')
            ->where('user_id', auth()->id())
            ->where('type', 'muontien')
            ->pluck('name');

        $wallets = Wallet::where('user_id', auth()->id())
            ->where('active', true)
            ->get();
        $contacts = $shops;
        // dd($employees);

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'employees',
            'contacts',
            'dautus',
            'nhacungcaps',
            'chovays',
            'wallets',
            'muontiens'
        ));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // $type = $request->input('type');

        // $rules = [
        //     'type' => 'required|in:thu,chi,nhaphang,chovay,congno',
        //     'wallet_id' => 'required|exists:wallets,id',
        //     'category_id' => 'required_if:type,thu,chi|nullable|exists:categories,id',
        //     'note' => 'nullable|string',
        //     'employee_name' => 'nullable|string|max:255',
        //     'shop_name' => 'nullable|string|max:255',
        //     'dautu_name' => 'nullable|string|max:255',
        // ];

        // // Chỉ bắt buộc các trường riêng khi là nhập hàng
        // if ($type === 'nhaphang') {
        //     $rules['supplier_name'] = 'required|string|max:255';
        //     $rules['amount_total'] = 'required|numeric|min:0.01';
        //     $rules['amount_paid'] = 'required|numeric|min:0';
        // } else {
        //     $rules['amount'] = 'required|numeric|min:0.01';
        // }

        // $request->validate($rules);

        $wallet = Wallet::find($request->wallet_id);
        if (!$wallet) {
            return back()->with('error', 'Ví không tồn tại.');
        }

        $relatedName = $request->supplier_name
            ?? $request->employee_name
            ?? $request->shop_name
            ?? $request->dautu_name
            ?? $request->related_name
            ?? ($request->type === 'chovay' ? Category::find($request->category_id)?->name : null);

        // === NHẬP HÀNG ===
        if ($request->type === 'nhaphang') {
            $amountTotal = (float) str_replace([',', '.'], '', $request->amount_total);
            $amountPaid = (float) str_replace([',', '.'], '', $request->amount_paid);
            $amountDebt = $amountTotal - $amountPaid;

            // Kiểm tra amount_paid < 0
            if ($amountPaid < 0) {
                return back()->withInput()->with('error', 'Số tiền thanh toán không được nhỏ hơn 0.');
            }

            if ($amountPaid > $wallet->balance) {
                return back()->withInput()->with('error', 'Số dư ví không đủ để thanh toán nhập hàng.');
            }

            if ($amountPaid > 0) {
                Transaction::create([
                    'user_id' => auth()->id(),
                    'wallet_id' => $wallet->id,
                    'type' => 'nhaphang',
                    'amount' => -abs($amountPaid),
                    'category_id' => null,
                    'note' => $request->note ?? 'Nhập hàng',
                    'related_name' => $relatedName,
                ]);

                $wallet->decrement('balance', abs($amountPaid));
            }

            if ($amountDebt > 0) {
                Transaction::create([
                    'user_id' => auth()->id(),
                    'wallet_id' => $wallet->id,
                    'type' => 'congno',
                    'amount' => -abs($amountDebt),
                    'category_id' => null,
                    'note' => 'Công nợ: ' . $amountTotal . ' - ' . $amountPaid,
                    'related_name' => $relatedName,
                ]);
            }
        }
        // === THU / CHI / CHO VAY ===
        else {
            $amount = (float) str_replace(['.', ','], '', $request->amount); // bỏ dấu ngăn cách

            if (in_array($request->type, ['chi', 'chovay']) && $wallet->balance < $amount) {
                return back()->withInput()->with('error', 'Số dư ví không đủ.');
            }

            if (in_array($request->type, ['chi', 'chovay'])) {
                $amount = -abs($amount);
            } elseif ($request->type === 'muontien') {
                $amount = abs($amount); // ✅ mượn tiền là DƯƠNG
            }

            $type = $request->type;


            // lấy tên người liên quan
            $relatedName = $request->muontien_name
                ?? $request->supplier_name
                ?? $request->employee_name
                ?? $request->shop_name
                ?? $request->dautu_name
                ?? $request->related_name;

            Transaction::create([
                'user_id' => auth()->id(),
                'wallet_id' => $wallet->id,
                'type' => $type,
                'amount' => $amount,
                'category_id' => $request->category_id ?? null,
                'note' => $request->note,
                'related_name' => $relatedName,
            ]);

            $wallet->increment('balance', $amount);
        }

        return redirect()->back()->with('success', 'Giao dịch đã được ghi lại.');
    }




    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:thu,chi,chovay',
        ]);

        $name = trim($request->name);

        // Không cho phép lưu danh mục tên là "Nhập hàng" với type = "chi"
        if (strtolower($name) === 'nhập hàng' && $request->type === 'chi') {
            return redirect()->back()->with('error', 'Danh mục "Nhập hàng" là mặc định và không thể thêm.');
        }

        // Kiểm tra trùng tên (không phân biệt hoa thường)
        $exists = Category::where('user_id', auth()->id())
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->where('type', $request->type)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Tên danh mục đã tồn tại.');
        }

        Category::create([
            'user_id' => auth()->id(),
            'name' => $name,
            'type' => $request->type,
        ]);

        return redirect()->back()->with('success', 'Thêm danh mục thành công!');
    }


    public function addEmployee(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
        ]);

        DB::table('employees')->insert([
            'user_id' => auth()->id(),
            'name' => $request->employee_name,
            'type' => 'employee',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Nhân viên đã được thêm.');
    }

    public function addShop(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
        ]);

        // ✅ Chỉ kiểm tra trùng trong phạm vi người dùng hiện tại
        $exists = DB::table('employees')
            ->where('user_id', auth()->id())
            ->where('name', $request->shop_name)
            ->where('type', 'shop')
            ->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Tên shop đã tồn tại.');
        }

        DB::table('employees')->insert([
            'user_id' => auth()->id(),
            'name' => $request->shop_name,
            'type' => 'shop',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Shop đã được thêm.');
    }
    public function addDautu(Request $request)
    {
        $request->validate([
            'dautu_name' => 'required|string|max:255',
        ]);

        $exists = DB::table('employees')
            ->where('user_id', auth()->id()) // ✅ chỉ kiểm tra trong user hiện tại
            ->where('name', $request->dautu_name)
            ->where('type', 'dautu')
            ->exists();


        if ($exists) {
            return redirect()->back()->with('error', 'Tên đầu khoản tư đã tồn tại.');
        }

        DB::table('employees')->insert([
            'user_id' => auth()->id(),
            'name' => $request->dautu_name,
            'type' => 'dautu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Khoản đầu tư đã được thêm.');
    }
    public function addSupplier(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
        ]);

        $exists = DB::table('employees')
            ->where('user_id', auth()->id())
            ->where('name', $request->supplier_name)
            ->where('type', 'nhacungcap')
            ->exists();


        if ($exists) {
            return redirect()->back()->with('error', 'Nhà cung cấp đã tồn tại.');
        }

        DB::table('employees')->insert([
            'user_id' => auth()->id(),
            'name' => $request->supplier_name,
            'type' => 'nhacungcap',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Nhà cung cấp đã được thêm.');
    }
    public function history(Request $request)
    {
        $query = Transaction::with('wallet')
            ->where('user_id', auth()->id());

        if ($request->filled('wallet_id')) {
            $query->where('wallet_id', $request->wallet_id);
        }

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('note', 'like', '%' . $request->keyword . '%')
                    ->orWhere('related_name', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest()->paginate(20);
        $wallets = Wallet::where('user_id', auth()->id())->get();

        return view('transactions.history', compact('transactions', 'wallets'));
    }
    public function addChovay(Request $request)
    {
        // dd($request->all());
        // $request->validate([
        //     'chovay_name' => 'required|string|max:255',
        // ]);

        $exists = DB::table('employees')
            ->where('user_id', auth()->id())
            ->where('name', $request->chovay_name)
            ->where('type', 'chovay')
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Người vay đã tồn tại.');
        }

        DB::table('employees')->insert([
            'user_id' => auth()->id(),
            'name' => $request->chovay_name,
            'type' => 'chovay',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Đã thêm người vay.');
    }
    public function addMuontien(Request $request)
    {
        $request->validate([
            'muontien_name' => 'required|string|max:255',
        ]);

        $exists = DB::table('employees')
            ->where('user_id', auth()->id())
            ->where('name', $request->muontien_name)
            ->where('type', 'muontien')
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Người cho mượn đã tồn tại.');
        }

        DB::table('employees')->insert([
            'user_id' => auth()->id(),
            'name' => $request->muontien_name,
            'type' => 'muontien',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Đã thêm người cho mượn.');
    }
}
