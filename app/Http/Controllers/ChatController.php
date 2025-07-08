<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ScamReport;


class ChatController extends Controller
{
   public function search(Request $request)
    {
        $result = ScamReport::with('images')
            ->when($request->filled('account'), function ($query) use ($request) {
                $query->where('scammer_account', 'LIKE', '%' . $request->account . '%')
                      ->orWhere('scammer_name', 'LIKE', '%' . $request->account . '%');
            })
            ->when($request->filled('bank'), function ($query) use ($request) {
                $query->where('bank', $request->bank);
            })
            ->get();

        return view('welcome', compact('result'));
    }
    public function reply(Request $request)
    {
        $text = $request->message;

        // Trả lời cứng hoặc dùng GPT sau
        $reply = 'Cảm ơn bạn đã nhắn! Chức năng đang trong quá trình thử nghiệm.';

        return response()->json(['reply' => $reply]);
    }
}
