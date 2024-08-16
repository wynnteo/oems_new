<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class eWalletController extends Controller
{
    public function index()
    {
        $studentId = Auth::id();
        $balance = $this->getStudentBalance($studentId);

        return view('student.ewallet', compact('balance'));
    }

    private function getStudentBalance($studentId)
    {
        // Logic to get the student’s current balance
        // This is just a placeholder, replace with actual logic
        return 100.00; // Example balance
    }
}
