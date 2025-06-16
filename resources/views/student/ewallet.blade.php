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


@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=USD"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedAmount = 0;
    let stripe, elements, card;
    let paypalButtonsRendered = false;

    // Initialize Stripe
    initializeStripe();

    // Amount selection handlers
    const amountButtons = document.querySelectorAll('.amount-btn');
    const customAmountInput = document.getElementById('custom-amount');

    amountButtons.forEach(button => {
        button.addEventListener('click', function() {
            const amount = parseFloat(this.dataset.amount);
            selectAmount(amount);
            
            // Update button states
            amountButtons.forEach(btn => btn.classList.remove('btn-primary'));
            amountButtons.forEach(btn => btn.classList.add('btn-outline-primary'));
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');
            
            // Clear custom input
            customAmountInput.value = '';
        });
    });

    customAmountInput.addEventListener('input', function() {
        const amount = parseFloat(this.value);
        if (amount && amount >= 1 && amount <= 1000) {
            selectAmount(amount);
            
            // Clear preset button selections
            amountButtons.forEach(btn => btn.classList.remove('btn-primary'));
            amountButtons.forEach(btn => btn.classList.add('btn-outline-primary'));
        } else {
            selectAmount(0);
        }
    });

    function selectAmount(amount) {
        selectedAmount = amount;
        
        // Enable/disable payment buttons
        const stripeSubmit = document.getElementById('stripe-submit');
        if (amount > 0) {
            stripeSubmit.disabled = false;
            stripeSubmit.querySelector('#stripe-button-text').innerHTML = 
                `<i class="fas fa-lock me-2"></i>Pay $${amount.toFixed(2)} with Credit Card`;
            
            // Render PayPal buttons if not already rendered
            if (!paypalButtonsRendered) {
                renderPayPalButtons();
            }
        } else {
            stripeSubmit.disabled = true;
            stripeSubmit.querySelector('#stripe-button-text').innerHTML = 
                '<i class="fas fa-lock me-2"></i>Pay with Credit Card';
        }
    }

    function initializeStripe() {
        stripe = Stripe('{{ config("services.stripe.key") }}');
        elements = stripe.elements();
        
        // Create card element
        card = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#424770',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
            },
        });

        card.mount('#card-element');

        // Handle real-time validation errors from the card Element
        card.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        const form = document.getElementById('stripe-payment-form');
        form.addEventListener('submit', handleStripeSubmit);
    }

    async function handleStripeSubmit(event) {
        event.preventDefault();

        if (selectedAmount <= 0) {
            showAlert('Please select an amount to top up.', 'warning');
            return;
        }

        const submitButton = document.getElementById('stripe-submit');
        const buttonText = document.getElementById('stripe-button-text');
        const spinner = document.getElementById('stripe-spinner');

        // Disable submit button and show loading
        submitButton.disabled = true;
        buttonText.classList.add('d-none');
        spinner.classList.remove('d-none');

        try {
            // Create payment intent
            const response = await fetch('{{ route("payments.stripe.create-intent") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    amount: selectedAmount
                })
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            // Confirm payment with Stripe
            const {error} = await stripe.confirmCardPayment(result.client_secret, {
                payment_method: {
                    card: card,
                    billing_details: {
                        
                        name: 'Alice Smith',
                        email: 'alice@example.com'
                    }
                }
            });

            if (error) {
                throw new Error(error.message);
            } else {
                // Payment succeeded, redirect to success page
                window.location.href = '{{ route("payments.stripe.success") }}?payment_intent=' + result.payment_intent_id;
            }

        } catch (error) {
            showAlert('Payment failed: ' + error.message, 'danger');
            console.error('Stripe payment error:', error);
        } finally {
            // Re-enable submit button
            submitButton.disabled = false;
            buttonText.classList.remove('d-none');
            spinner.classList.add('d-none');
        }
    }

    function renderPayPalButtons() {
        const container = document.getElementById('paypal-button-container');
        container.innerHTML = ''; // Clear existing content

        paypal.Buttons({
            createOrder: function(data, actions) {
                if (selectedAmount <= 0) {
                    showAlert('Please select an amount to top up.', 'warning');
                    return Promise.reject('No amount selected');
                }

                return fetch('{{ route("payments.paypal.create") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        amount: selectedAmount
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    return data.id;
                })
                .catch(error => {
                    showAlert('PayPal order creation failed: ' + error.message, 'danger');
                    throw error;
                });
            },

            onApprove: function(data, actions) {
                // Redirect to capture route
                window.location.href = '{{ route("payments.paypal.return") }}?token=' + data.orderID;
            },

            onError: function(err) {
                showAlert('PayPal payment error: ' + err.message, 'danger');
                console.error('PayPal error:', err);
            },

            onCancel: function(data) {
                showAlert('PayPal payment was cancelled.', 'warning');
            },

            style: {
                layout: 'vertical',
                color: 'blue',
                shape: 'rect',
                label: 'paypal'
            }
        }).render('#paypal-button-container');

        paypalButtonsRendered = true;
    }

    function showAlert(message, type = 'info') {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Insert at top of container
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = bootstrap.Alert.getOrCreateInstance(alertDiv);
            alert.close();
        }, 5000);
    }

    // Tab switching handler
    const tabs = document.querySelectorAll('[data-bs-toggle="pill"]');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(event) {
            if (event.target.id === 'paypal-tab' && !paypalButtonsRendered && selectedAmount > 0) {
                renderPayPalButtons();
            }
        });
    });
});
</script>
@endsection