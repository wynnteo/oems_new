<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\LiveEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use App\Models\Transaction;
use App\Services\WalletService;

class PayPalController extends Controller
{
    private $client;
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
        
        $environment = config('services.paypal.mode') === 'live'
            ? new LiveEnvironment(config('services.paypal.client_id'), config('services.paypal.client_secret'))
            : new SandboxEnvironment(config('services.paypal.client_id'), config('services.paypal.client_secret'));

        $this->client = new PayPalHttpClient($environment);
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:1000',
        ]);

        $user = Auth::user();
        $amount = $request->amount;
        $transactionId = uniqid('paypal_', true);

        // Create pending transaction
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'credit',
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'payment_method' => 'paypal',
            'status' => 'pending',
            'description' => 'Wallet top-up via PayPal',
            'currency' => 'USD'
        ]);

        $orderRequest = new OrdersCreateRequest();
        $orderRequest->prefer('return=representation');
        $orderRequest->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'custom_id' => $transactionId,
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($amount, 2, '.', ''),
                ]
            ]],
            'application_context' => [
                'return_url' => route('payments.paypal.return'),
                'cancel_url' => route('payments.paypal.cancel')
            ]
        ];

        try {
            $response = $this->client->execute($orderRequest);
            return response()->json(['id' => $response->result->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function captureOrder(Request $request)
    {
        $orderId = $request->query('token');
        
        if (!$orderId) {
            return redirect()->route('student.ewallet.index')
                ->with('error', 'Invalid PayPal order ID');
        }

        $captureRequest = new OrdersCaptureRequest($orderId);
        $captureRequest->prefer('return=representation');

        try {
            $response = $this->client->execute($captureRequest);
            $result = $response->result;
            
            if ($result->status === 'COMPLETED') {
                $transactionId = $result->purchase_units[0]->custom_id;
                $amount = floatval($result->purchase_units[0]->amount->value);
                
                $transaction = Transaction::where('transaction_id', $transactionId)->first();
                
                if ($transaction) {
                    $this->walletService->topUpWallet(
                        $transaction->user,
                        $amount,
                        'PayPal',
                        $transactionId,
                        $result->toArray()
                    );
                    
                    return redirect()->route('student.ewallet.index')
                        ->with('success', 'Wallet topped up successfully via PayPal!');
                }
            }
            
            return redirect()->route('student.ewallet.index')
                ->with('error', 'Payment was not completed');
                
        } catch (\Exception $e) {
            return redirect()->route('student.ewallet.index')
                ->with('error', 'PayPal payment failed: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('student.ewallet.index')
            ->with('warning', 'PayPal payment was cancelled');
    }
}