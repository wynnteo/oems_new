<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Assuming $amount is coming from the request or is predefined
        $amount = $request->amount * 100; // Amount in cents
        //$studentId = auth()->id();
        $studentId = 1;

        // Generate a transaction ID for your application
        $transactionId = uniqid('txn_', true);

        // Create a PaymentIntent with metadata
        $paymentIntent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
            'metadata' => [
                'transaction_id' => $transactionId,
                'student_id' => $studentId,
            ],
        ]);

        // Create a pending transaction record in your database
        $transaction = Transaction::create([
            'user_id' => $studentId,
            'amount' => $amount / 100, // Convert back to dollars
            'transaction_id' => $transactionId,
            'payment_method' => 'Stripe',
            'status' => 'pending',
            'transaction_date' => now(),
            'description' => 'eWallet top-up via Stripe',
            'gateway_response' => json_encode([]), // You can update this later with actual response data
            //'currency' => 'USD',
        ]);

        // Return the PaymentIntent's client secret to the frontend
        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    public function handleTransaction(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $clientSecret = $request->query('payment_intent_client_secret');
        $paymentIntentId = $request->input('payment_intent');

        if (!$paymentIntentId) {
            return redirect()->route('ewallet')->with('error', 'No payment intent found.');
        }

        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
        try {
            $transactionId = $paymentIntent->metadata->transaction_id;
            $amount = $paymentIntent->amount_received / 100; // Convert to dollars
            $status = $paymentIntent->status; // e.g., 'succeeded', 'requires_payment_method', etc.

            $transaction = Transaction::where('transaction_id', $transactionId)->first();

            if ($transaction) {
                $transaction->update([
                    'status' => $status,
                    'gateway_response' => json_encode($paymentIntent),
                ]);

                if ($status === 'succeeded') {
                    // // Update eWallet balance
                    // $wallet = Wallet::where('student_id', Auth::id())->first();
                    // $wallet->balance += $amount;
                    // $wallet->save();
                } 
            }

            return redirect()->route('ewallet')->with('status', $status);
        } catch (\Exception $e) {
            // Handle exceptions such as invalid PaymentIntent ID
            return redirect()->route('ewallet')->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    private function recordTransaction($amount, $gateway, $transactionId, $paymentIntent)
    {
        Transaction::create([
            'student_id' => auth()->id(),
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'gateway' => $gateway,
            'status' => 'completed',
            'transaction_date' => now(),
            'description' => 'eWallet top-up via Stripe',
            'gateway_response' => json_encode($paymentIntent->toArray()),
            'currency' => 'USD',
        ]);
    }
}
