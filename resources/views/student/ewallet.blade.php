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

@section('scripts')
{{-- Only include scripts that are actually needed for this page --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any specific functionality for the e-wallet overview page here
    // For example, auto-refresh transaction history, etc.
    
    console.log('E-Wallet page loaded successfully');
});
</script>
@endsection