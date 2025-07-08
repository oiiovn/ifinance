<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ScamReportController;
use App\Models\ScamReport;

// Trang chủ hiển thị tra cứu + tổng lượt
Route::get('/', function () {
    $result = null;
    $reportCount = ScamReport::count();
    return view('welcome', compact('result', 'reportCount'));
})->name('scam.report.form');

// Tra cứu scam
Route::get('/check-scam', [ChatController::class, 'search'])->name('scam.search');

// Tố cáo scam
Route::view('/report-scam', 'report-scam')->name('scam.report.form');
Route::post('/report-scam-submit', [ScamReportController::class, 'submit'])->name('scam.report.submit');

// API chat (nếu dùng)
Route::post('/api/chatbot', [ChatController::class, 'reply']);
