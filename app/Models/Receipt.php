<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'transaction_id', 'issued_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
