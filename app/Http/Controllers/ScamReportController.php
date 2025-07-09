<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScamReport;
use App\Models\ScamReportImage;
use Illuminate\Support\Facades\Storage;

class ScamReportController extends Controller
{
    public function submit(Request $request)
    {
      

        $data = $request->only([
            'scammer_name',
            'scammer_account',
            'bank',
            'scammer_facebook',
            'content',
            'reporter',
            'reporter_zalo',
            'confirm_type',
        ]);
        $data['status'] = 'đã duyệt';

        $report = ScamReport::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('scam_uploads', 'public');
                ScamReportImage::create([
                    'report_id' => $report->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Gửi tố cáo thành công. Đang chờ duyệt.');
    }
}
