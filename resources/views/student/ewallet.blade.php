@extends('layouts.studentmaster')

@section('title', 'E-Wallet | Student Portal')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Balance Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-wallet fa-3x text-primary mb-3"></i>
                    <h2 class="text-primary mb-2">${{ number_format($wallet->balance, 2) }}</h2>
                    <p class="text-muted">Available Balance</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.ewallet.topup') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Top Up Wallet
                        </a>
                        <a href="{{ route('student.ewallet.exam-store') }}" class="btn btn-outline-success">
                            <i class="fas fa-shopping-cart me-2"></i>Buy Exams
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-8 mb-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-up text-success fa-2x mb-2"></i>
                            <h5 class="text-success">
                                ${{ number_format($transactions->where('type', 'credit')->sum('amount'), 2) }}
                            </h5>
                            <small class="text-muted">Total Top-ups</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-down text-danger fa-2x mb-2"></i>
                            <h5 class="text-danger">
                                ${{ number_format($transactions->where('type', 'debit')->sum('amount'), 2) }}
                            </h5>
                            <small class="text-muted">Total Spent</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt text-info fa-2x mb-2"></i>
                            <h5 class="text-info">{{ $transactions->where('type', 'debit')->where('exam_id', '!=', null)->count() }}</h5>
                            <small class="text-muted">Exams Purchased</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Transaction History</h5>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Balance After</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                {{ $transaction->description }}
                                                @if($transaction->exam)
                                                    <br><small class="text-muted">{{ $transaction->exam->title }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->type === 'credit')
                                                    <span class="badge bg-success">Top-up</span>
                                                @else
                                                    <span class="badge bg-danger">Purchase</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->type === 'credit')
                                                    <span class="text-success">+${{ number_format($transaction->amount, 2) }}</span>
                                                @else
                                                    <span class="text-danger">-${{ number_format($transaction->amount, 2) }}</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($transaction->balance_after, 2) }}</td>
                                            <td>
                                                @switch($transaction->status)
                                                    @case('completed')
                                                        <span class="badge bg-success">Completed</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge bg-danger">Failed</span>
                                                        @break
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No transactions yet. Start by topping up your wallet!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- resources/views/student/ewallet/topup.blade.php --}}
@extends('layouts.studentmaster')

@section('title', 'Top Up Wallet | Student Portal')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Current Balance -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <h4 class="text-muted mb-2">Current Balance</h4>
                    <h2 class="text-primary">${{ number_format($wallet->balance, 2) }}</h2>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Top Up Your Wallet</h5>
                </div>
                <div class="card-body">
                    <!-- Amount Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Amount</label>
                        <div class="row g-2">
                            @foreach([10, 25, 50, 100, 200, 500] as $amount)
                                <div class="col-4 col-md-2">
                                    <button class="btn btn-outline-primary w-100 amount-btn" data-amount="{{ $amount }}">
                                        ${{ $amount }}
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <label for="custom-amount" class="form-label">Or enter custom amount:</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="custom-amount" 
                                       placeholder="0.00" min="1" max="1000" step="0.01">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Tabs -->
                    <ul class="nav nav-pills nav-justified mb-4" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="stripe-tab" data-bs-toggle="pill" 
                                    data-bs-target="#stripe-panel" type="button">
                                <i class="fab fa-cc-stripe me-2"></i>Credit Card
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="paypal-tab" data-bs-toggle="pill" 
                                    data-bs-target="#paypal-panel" type="button">
                                <i class="fab fa-paypal me-2"></i>PayPal
                            </button>
                        </li>
                    </ul>

                    <!-- Payment Panels -->
                    <div class="tab-content">
                        <!-- Stripe Panel -->
                        <div class="tab-pane fade show active" id="stripe-panel">
                            <form id="stripe-payment-form">
                                @csrf
                                <div id="card-element" class="mb-3">
                                    <!-- Stripe Elements will be inserted here -->
                                </div>
                                <div id="card-errors" class="text-danger mb-3"></div>
                                <button type="submit" id="stripe-submit" class="btn btn-primary w-100" disabled>
                                    <span id="stripe-button-text">
                                        <i class="fas fa-lock me-2"></i>Pay with Credit Card
                                    </span>
                                    <div class="spinner-border spinner-border-sm ms-2 d-none" id="stripe-spinner"></div>
                                </button>
                            </form>
                        </div>

                        <!-- PayPal Panel -->
                        <div class="tab-pane fade" id="paypal-panel">
                            <div id="paypal-button-container" class="text-center">
                                <p class="text-muted mb-3">Select an amount above to enable PayPal payment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
@endsection