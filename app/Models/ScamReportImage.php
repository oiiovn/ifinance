<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScamReportImage extends Model
{
    protected $fillable = ['report_id', 'image_path'];

    public function report()
    {
        return $this->belongsTo(ScamReport::class, 'report_id');
    }
}
