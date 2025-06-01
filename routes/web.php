<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $wallets = \App\Models\Wallet::where('user_id', auth()->id())->get();
    return view('dashboard', compact('wallets'));
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth'])->group(function () {
    Route::resource('wallets', WalletController::class);
    Route::post('wallets/transfer', [WalletController::class, 'transfer'])->name('wallets.transfer');
    Route::patch('/wallets/{id}/toggle', [WalletController::class, 'toggle'])->name('wallets.toggle');
    Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
    Route::patch('/wallets/{id}/update-balance', [WalletController::class, 'updateBalance'])->name('wallets.updateBalance');
    Route::delete('/wallets/transactions/{id}', [WalletController::class, 'destroyTransaction'])->name('wallets.transactions.destroy');
});
Route::middleware(['auth'])->group(function () {
    Route::resource('transactions', TransactionController::class)->only(['index', 'store']);
    Route::post('transactions/add-category', [TransactionController::class, 'addCategory'])->name('transactions.addCategory');
    Route::post('transactions/add-employee', [TransactionController::class, 'addEmployee'])->name('transactions.addEmployee');
    Route::post('/transactions/add-shop', [TransactionController::class, 'addShop'])->name('transactions.addShop');
    Route::post('transactions/add-dautu', [TransactionController::class, 'addDautu'])->name('transactions.addDautu');
    Route::post('/transactions/add-supplier', [TransactionController::class, 'addSupplier'])->name('transactions.addSupplier');
    Route::post('/transactions/add-chovay', [TransactionController::class, 'addChovay'])->name('transactions.addChovay');
    Route::post('/transactions/add-muontien', [TransactionController::class, 'addMuontien'])->name('transactions.addMuontien'); // ✅ Thêm dòng này
    Route::get('/congno', [DebtController::class, 'index'])->name('congno.index');
    Route::post('/congno/pay', [DebtController::class, 'pay'])->name('congno.pay');
});



require __DIR__ . '/auth.php';
