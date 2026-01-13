<x-front-layout title="Order Payment">
    <div class="account-login section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3 col-md-10 offset-md-1 col-12">
                    <div id="payment-message" style="display: none;" class="alert"></div>

                    <!-- Payment Element form -->
                    <div id="payment-form">
                        <div id="payment-element"></div>
                        <button type="button" id="submit" class="btn" disabled>
                            <span id="button-text">Pay now</span>
                            <span id="spinner" style="display: none;">Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe("{{ config('services.stripe.key') }}");
        let elements;

        // Initialize Stripe Payment Element
        async function initialize() {
            try {
const response = await fetch("{{ route('stripe.paymentIntent.create', $order) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Failed to create payment intent');
                }

                const data = await response.json();

                if (!data.clientSecret) {
                    throw new Error("clientSecret missing from server");
                }

                // Initialize Stripe elements with Payment Element
                elements = stripe.elements({ clientSecret: data.clientSecret });
                const paymentElement = elements.create("payment");
                paymentElement.mount("#payment-element");

                document.querySelector("#submit").disabled = false;

            } catch (error) {
                console.error('Initialization error:', error);
                showMessage(error.message, 'danger');
            }
        }

        // Handle submit
        async function handleSubmit() {
            setLoading(true);

            try {
                const { error } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: "{{ route('orders.payments.confirm', $order) }}",
                    },
                });

                if (error) {
                    showMessage(error.message, 'danger');
                }
            } catch (err) {
                console.error('Payment error:', err);
                showMessage("Unexpected error: " + err.message, 'danger');
            }

            setLoading(false);
        }

        // UI helpers
        function showMessage(messageText, type = 'info') {
            const messageContainer = document.querySelector("#payment-message");
            messageContainer.className = 'alert alert-' + type;
            messageContainer.style.display = "block";
            messageContainer.textContent = messageText;

            setTimeout(() => {
                messageContainer.style.display = "none";
                messageContainer.textContent = "";
            }, 5000);
        }

        function setLoading(isLoading) {
            const submitBtn = document.querySelector("#submit");
            const spinner = document.querySelector("#spinner");
            const buttonText = document.querySelector("#button-text");

            if (isLoading) {
                submitBtn.disabled = true;
                spinner.style.display = "inline";
                buttonText.style.display = "none";
            } else {
                submitBtn.disabled = false;
                spinner.style.display = "none";
                buttonText.style.display = "inline";
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            initialize();
            document.querySelector("#submit").addEventListener("click", handleSubmit);
        });
    </script>
</x-front-layout>