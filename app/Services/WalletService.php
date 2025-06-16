<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getOrCreateWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'currency' => 'USD']
        );
    }

    public function topUpWallet(User $user, float $amount, string $paymentMethod, string $transactionId, array $gatewayResponse = [])
    {
        return DB::transaction(function () use ($user, $amount, $paymentMethod, $transactionId, $gatewayResponse) {
            $wallet = $this->getOrCreateWallet($user);
            
            $wallet->addBalance(
                $amount,
                "Wallet top-up via {$paymentMethod}",
                $transactionId
            );

            // Update the payment transaction record
            \App\Models\Transaction::where('transaction_id', $transactionId)
                ->update([
                    'status' => 'completed',
                    'gateway_response' => $gatewayResponse,
                    'wallet_id' => $wallet->id
                ]);

            return $wallet->fresh();
        });
    }

    public function purchaseExam(User $user, Exam $exam)
    {
        return DB::transaction(function () use ($user, $exam) {
            $wallet = $this->getOrCreateWallet($user);
            
            if (!$wallet->hasEnoughBalance($exam->price)) {
                throw new \Exception('Insufficient wallet balance');
            }

            $wallet->deductBalance(
                $exam->price,
                "Exam purchase: {$exam->title}",
                $exam->id
            );

            // Create exam registration or purchase record
            // You might want to create a separate ExamPurchase model
            
            return $wallet->fresh();
        });
    }

    public function getTransactionHistory(User $user, int $limit = 50)
    {
        $wallet = $this->getOrCreateWallet($user);
        
        return $wallet->transactions()
            ->with(['exam'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}