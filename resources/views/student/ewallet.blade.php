@extends('layouts.studentmaster')

@section('title')
    eWallet | Student Portal
@endsection
<style>
form {
  min-width: 500px;
  align-self: center;
  box-shadow: 0px 0px 0px 0.5px rgba(50, 50, 93, 0.1),
    0px 2px 5px 0px rgba(50, 50, 93, 0.1), 0px 1px 1.5px 0px rgba(0, 0, 0, 0.07);
  border-radius: 7px;
  padding: 40px;
}

.hidden {
  display: none;
}

#payment-message {
  color: rgb(105, 115, 134);
  font-size: 16px;
  line-height: 20px;
  padding-top: 12px;
  text-align: center;
}

#payment-element {
  margin-bottom: 24px;
}

/* Buttons and links */
button {
  background: #5469d4;
  font-family: Arial, sans-serif;
  color: #ffffff;
  border-radius: 4px;
  border: 0;
  padding: 12px 16px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  display: block;
  transition: all 0.2s ease;
  box-shadow: 0px 4px 5.5px 0px rgba(0, 0, 0, 0.07);
  width: 100%;
}
button:hover {
  filter: contrast(115%);
}
button:disabled {
  opacity: 0.5;
  cursor: default;
}

/* spinner/processing state, errors */
.spinner,
.spinner:before,
.spinner:after {
  border-radius: 50%;
}
.spinner {
  color: #ffffff;
  font-size: 22px;
  text-indent: -99999px;
  margin: 0px auto;
  position: relative;
  width: 20px;
  height: 20px;
  box-shadow: inset 0 0 0 2px;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
}
.spinner:before,
.spinner:after {
  position: absolute;
  content: "";
}
.spinner:before {
  width: 10.4px;
  height: 20.4px;
  background: #5469d4;
  border-radius: 20.4px 0 0 20.4px;
  top: -0.2px;
  left: -0.2px;
  -webkit-transform-origin: 10.4px 10.2px;
  transform-origin: 10.4px 10.2px;
  -webkit-animation: loading 2s infinite ease 1.5s;
  animation: loading 2s infinite ease 1.5s;
}
.spinner:after {
  width: 10.4px;
  height: 10.2px;
  background: #5469d4;
  border-radius: 0 10.2px 10.2px 0;
  top: -0.1px;
  left: 10.2px;
  -webkit-transform-origin: 0px 10.2px;
  transform-origin: 0px 10.2px;
  -webkit-animation: loading 2s infinite ease;
  animation: loading 2s infinite ease;
}

@-webkit-keyframes loading {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes loading {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

@media only screen and (max-width: 600px) {
  form {
    width: 80vw;
    min-width: initial;
  }
}

.amount-selection {
    display: flex;
    margin-bottom: 2rem;
}

.amount-button {
    background-color: white;
    border: 1px solid #344768;
    color: #344768;
    padding: 15px 32px; 
    text-align: center;
    text-decoration: none; 
    display: inline-block;
    font-size: 16px; 
    margin: 4px 2px;
    cursor: pointer; 
    border-radius: 12px; 
    transition-duration: 0.4s;
}

.amount-button:hover {
    background-color: white; /* White background on hover */
    color: black; /* Black text on hover */
    border: 2px solid #4CAF50; /* Green border */
}
</style>
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Balance Section -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">eWallet Balance</h5>
                </div>
                <div class="card-body">
                    <h3 class="text-center">${{ number_format($balance, 2) }}</h3>
                    <p class="text-center text-muted">Current balance in your eWallet.</p>
                </div>
            </div>
        </div>

        <!-- Top-Up Section -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Up eWallet</h5>
                </div>
                <div class="card-body">
                    <div class="amount-selection mb-4">
                        <button class="amount-button" data-amount="10">$10</button>
                        <button class="amount-button" data-amount="50">$50</button>
                        <button class="amount-button" data-amount="100">$100</button>
                        <button class="amount-button" data-amount="150">$150</button>
                        <button class="amount-button" data-amount="200">$200</button>
                    </div>
                    <form id="payment-form">
                        @csrf
                        <div id="payment-element">
                            <!-- Stripe Elements will be inserted here -->
                        </div>
                        <button id="submit" type="submit" class="btn btn-primary">
                            <div class="spinner hidden" id="spinner"></div>
                            <span id="button-text">Pay now</span>
                        </button>
                        <div id="payment-message" class="hidden"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ config('services.stripe.key') }}');
    let elements;
    let selectedAmount = 0;


    document.querySelectorAll('.amount-button').forEach(button => {
        button.addEventListener('click', async function() {
            selectedAmount = this.getAttribute('data-amount');
            await createPaymentIntent(selectedAmount);
        });
    });

    async function createPaymentIntent(amount) {
        const response = await fetch("/stripe/create-payment-intent", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ amount: amount }),
        });

        const { clientSecret } = await response.json();

        if (!elements) {
            elements = stripe.elements({ clientSecret });
        } else {
            elements.update({ clientSecret });
        }

        const paymentElement = elements.create("payment", {
            layout: "tabs",
        });
        paymentElement.mount("#payment-element");
    }

    document.querySelector("#payment-form").addEventListener("submit", async (e) => {
        e.preventDefault();
        setLoading(true);

        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: "http://localhost:8000/stripe/payment-success",
                receipt_email: "wynnnnyw@hotmail.com"
            },
        });

        if (error) {
            showMessage(error.message);
        } else {
            showMessage("Payment processing...");
        }

        setLoading(false);
    });


    function showMessage(messageText) {
        const messageContainer = document.querySelector("#payment-message");
        messageContainer.classList.remove("hidden");
        messageContainer.textContent = messageText;
        setTimeout(() => {
            messageContainer.classList.add("hidden");
            messageContainer.textContent = "";
        }, 4000);
    }

    function setLoading(isLoading) {
        document.querySelector("#submit").disabled = isLoading;
        document.querySelector("#spinner").classList.toggle("hidden", !isLoading);
        document.querySelector("#button-text").classList.toggle("hidden", isLoading);
    }
</script>
@endsection