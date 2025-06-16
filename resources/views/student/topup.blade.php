@extends('layouts.studentmaster')

@section('title', 'Top Up Wallet | Student Portal')

@section('content')
<div class="container-fluid py-4">
    <!-- Back Button -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('student.ewallet.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to E-Wallet
            </a>
        </div>
    </div>

    <!-- Current Balance -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-wallet fa-2x text-primary mb-2"></i>
                    <h4 class="text-primary mb-1">${{ number_format($wallet->balance, 2) }}</h4>
                    <small class="text-muted">Current Balance</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Top-up Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Top Up Your Wallet</h5>
                </div>
                <div class="card-body">
                    <!-- Amount Selection -->
                    <div class="mb-4">
                        <label class="form-label">Select Amount</label>
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-md-3">
                                <button type="button" class="btn btn-outline-primary w-100 amount-btn" data-amount="10">$10</button>
                            </div>
                            <div class="col-6 col-md-3">
                                <button type="button" class="btn btn-outline-primary w-100 amount-btn" data-amount="25">$25</button>
                            </div>
                            <div class="col-6 col-md-3">
                                <button type="button" class="btn btn-outline-primary w-100 amount-btn" data-amount="50">$50</button>
                            </div>
                            <div class="col-6 col-md-3">
                                <button type="button" class="btn btn-outline-primary w-100 amount-btn" data-amount="100">$100</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="custom-amount" class="form-label">Or enter custom amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="custom-amount" 
                                           placeholder="Enter amount" min="1" max="1000" step="0.01">
                                </div>
                                <small class="text-muted">Minimum: $1.00, Maximum: $1,000.00</small>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mb-4">
                        <label class="form-label">Choose Payment Method</label>
                        <ul class="nav nav-pills mb-3" id="payment-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="stripe-tab" data-bs-toggle="pill" 
                                        data-bs-target="#stripe-panel" type="button" role="tab">
                                    <i class="fas fa-credit-card me-2"></i>Credit Card
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="paypal-tab" data-bs-toggle="pill" 
                                        data-bs-target="#paypal-panel" type="button" role="tab">
                                    <i class="fab fa-paypal me-2"></i>PayPal
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="payment-content">
                            <!-- Stripe Credit Card Panel -->
                            <div class="tab-pane fade show active" id="stripe-panel" role="tabpanel">
                                <form id="stripe-payment-form">
                                    <div class="mb-3">
                                        <label class="form-label">Card Information</label>
                                        <div id="card-element" class="form-control" style="height: 40px; padding-top: 10px;">
                                            <!-- Stripe Elements will create form elements here -->
                                        </div>
                                        <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                                    </div>
                                    <button type="submit" id="stripe-submit" class="btn btn-primary btn-lg w-100" disabled>
                                        <span id="stripe-button-text">
                                            <i class="fas fa-lock me-2"></i>Pay with Credit Card
                                        </span>
                                        <div id="stripe-spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </button>
                                </form>
                            </div>

                            <!-- PayPal Panel -->
                            <div class="tab-pane fade" id="paypal-panel" role="tabpanel">
                                <div id="paypal-button-container" class="mb-3">
                                    <!-- PayPal Buttons will be rendered here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-shield-alt text-success me-2"></i>Secure Payment</h6>
                    <p class="card-text small text-muted">
                        Your payment information is encrypted and secure. We use industry-standard security measures to protect your data.
                    </p>
                    <ul class="list-unstyled small text-muted">
                        <li><i class="fas fa-check text-success me-2"></i>SSL Encrypted</li>
                        <li><i class="fas fa-check text-success me-2"></i>PCI Compliant</li>
                        <li><i class="fas fa-check text-success me-2"></i>No card details stored</li>
                    </ul>
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