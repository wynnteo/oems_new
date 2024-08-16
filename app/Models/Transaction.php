<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 
        'transaction_id',
        'gateway_response',
        'payment_method',
        'status',
        'amount', 'transaction_date', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
