<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PaymentService;

class StripeController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:1000'
        ]);

        try {
            $user = Auth::user();
            $result = $this->paymentService->createStripePaymentIntent($user, $request->amount);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleSuccess(Request $request)
    {
        try {
            $paymentIntentId = $request->input('payment_intent');
            
            if (!$paymentIntentId) {
                return redirect()->route('student.ewallet.index')
                    ->with('error', 'Payment intent not found.');
            }

            $transaction = $this->paymentService->handleStripeSuccess($paymentIntentId);
            
            return redirect()->route('student.ewallet.index')
                ->with('success', 'Wallet topped up successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('student.ewallet.index')
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }
}