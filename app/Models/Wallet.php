<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'balance',
        'is_active',
        'logo_path',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sentTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'from_wallet_id');
    }

    public function receivedTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'to_wallet_id');
    }
}
