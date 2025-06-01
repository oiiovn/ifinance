<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'category_id',
        'type',
        'amount',
        'note',
        'related_name'
        
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
