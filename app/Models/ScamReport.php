<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ScamReportImage;

class ScamReport extends Model
{
    protected $fillable = [
        'scammer_name', 'scammer_account', 'bank', 'scammer_facebook',
        'content', 'reporter', 'reporter_zalo', 'confirm_type', 'status',
    ];

    public function images()
    {
        return $this->hasMany(ScamReportImage::class, 'report_id');
    }
}
