<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\LiveEnvironment;
use PayPalCheckoutSdk\Payments\OrdersCreateRequest;
use PayPalCheckoutSdk\Payments\OrdersCaptureRequest;
use App\Models\Transaction;

class PayPalController extends Controller
{
    private $client;

    public function __construct()
    {
        $environment = config('services.paypal.mode') === 'live'
            ? new LiveEnvironment(config('services.paypal.client_id'), config('services.paypal.client_secret'))
            : new SandboxEnvironment(config('services.paypal.client_id'), config('services.paypal.client_secret'));

        $this->client = new PayPalHttpClient($environment);
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amount = $request->amount;

        $orderRequest = new OrdersCreateRequest();
        $orderRequest->prefer('return=representation');
        $orderRequest->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => $amount,
                ]
            ]],
            'application_context' => [
                'return_url' => route('paypal.return'),
                'cancel_url' => route('paypal.cancel')
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
        $orderId = $request->orderId;

        $captureRequest = new OrdersCaptureRequest($orderId);
        $captureRequest->prefer('return=representation');

        try {
            $response = $this->client->execute($captureRequest);
            $amount = $response->result->purchase_units[0]->amount->value;
            $transactionId = $response->result->id;

            // Record the transaction
            $this->handleTransaction($amount, 'paypal', $transactionId);

            return redirect()->route('ewallet.index')->with('success', 'eWallet topped up successfully!');
        } catch (\Exception $e) {
            return redirect()->route('ewallet.index')->withErrors('Error: ' . $e->getMessage());
        }
    }

    private function handleTransaction($amount, $gateway, $transactionId)
    {
        // Logic to update the student's balance and record the transaction
        // Example transaction record creation
        Transaction::create([
            'student_id' => auth()->id(),
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'gateway' => $gateway,
            'status' => 'completed',
            'transaction_date' => now(),
            'description' => 'eWallet top-up via PayPal',
            'gateway_response' => json_encode([]), // Replace with actual response data
            'currency' => 'USD',
        ]);
    }
}
