<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProof extends Model
{
    protected $fillable = ['user_id', 'image', 'status', 'amount'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
