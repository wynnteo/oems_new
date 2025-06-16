<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Transaction;
use App\Models\User;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createStripePaymentIntent(User $user, float $amount): array
    {
        $transactionId = uniqid('stripe_', true);
        
        $paymentIntent = PaymentIntent::create([
            'amount' => $amount * 100, // Convert to cents
            'currency' => 'usd',
            'metadata' => [
                'transaction_id' => $transactionId,
                'user_id' => $user->id,
            ],
        ]);

        // Create pending transaction record
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'payment_method' => 'stripe',
            'status' => 'pending',
            'description' => 'Wallet top-up via Stripe',
            'currency' => 'USD'
        ]);

        return [
            'client_secret' => $paymentIntent->client_secret,
            'transaction_id' => $transactionId
        ];
    }

    public function handleStripeSuccess(string $paymentIntentId): Transaction
    {
        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
        $transactionId = $paymentIntent->metadata->transaction_id;
        
        $transaction = Transaction::where('transaction_id', $transactionId)->firstOrFail();
        
        if ($paymentIntent->status === 'succeeded') {
            $walletService = new WalletService();
            $walletService->topUpWallet(
                $transaction->user,
                $transaction->amount,
                'Stripe',
                $transactionId,
                $paymentIntent->toArray()
            );
        } else {
            $transaction->update(['status' => 'failed']);
        }

        return $transaction;
    }
}