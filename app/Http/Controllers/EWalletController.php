<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WalletService;
use App\Services\PaymentService;
use App\Models\Exam;

class EWalletController extends Controller
{
    protected $walletService;
    protected $paymentService;

    public function __construct(WalletService $walletService, PaymentService $paymentService)
    {
        $this->walletService = $walletService;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateWallet($user);
        $transactions = $this->walletService->getTransactionHistory($user, 20);

        return view('student.ewallet.index', compact('wallet', 'transactions'));
    }

    public function topUp()
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateWallet($user);

        return view('student.ewallet.topup', compact('wallet'));
    }

    public function examStore()
    {
        $user = Auth::user();
        $wallet = $this->walletService->getOrCreateWallet($user);
        
        // Get available exams that user hasn't purchased
        $availableExams = Exam::where('status', 'active')
            ->whereDoesntHave('purchases', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        return view('student.ewallet.exam-store', compact('wallet', 'availableExams'));
    }

    public function purchaseExam(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id'
        ]);

        try {
            $user = Auth::user();
            $exam = Exam::findOrFail($request->exam_id);
            
            $wallet = $this->walletService->purchaseExam($user, $exam);
            
            return response()->json([
                'success' => true,
                'message' => 'Exam purchased successfully!',
                'new_balance' => $wallet->balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}