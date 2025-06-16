<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'currency'
    ];

    protected $casts = [
        'balance' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id');
    }

    public function hasEnoughBalance($amount)
    {
        return $this->balance >= $amount;
    }

    public function addBalance($amount, $description = null, $transactionId = null)
    {
        $this->increment('balance', $amount);
        
        Transaction::create([
            'user_id' => $this->user_id,
            'wallet_id' => $this->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $this->fresh()->balance,
            'description' => $description ?? 'Wallet top-up',
            'transaction_id' => $transactionId,
            'status' => 'completed'
        ]);
    }

    public function deductBalance($amount, $description = null, $examId = null)
    {
        if (!$this->hasEnoughBalance($amount)) {
            throw new \Exception('Insufficient balance');
        }

        $this->decrement('balance', $amount);
        
        Transaction::create([
            'user_id' => $this->user_id,
            'wallet_id' => $this->id,
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $this->fresh()->balance,
            'description' => $description ?? 'Exam purchase',
            'exam_id' => $examId,
            'status' => 'completed'
        ]);
    }
}