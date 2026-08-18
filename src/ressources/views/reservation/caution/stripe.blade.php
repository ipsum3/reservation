<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déposer votre caution - Réservation #{{ $reservation->reference }}</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body { font-family: system-ui, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f4f5f7; margin: 0; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 450px; width: 100%; }
        button { background: #5469d4; color: white; border: 0; padding: 12px 16px; border-radius: 4px; width: 100%; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 20px; }
        button:disabled { opacity: 0.5; }
        #error-message { color: #df1b41; margin-top: 10px; font-size: 14px; }
    </style>
</head>
<body>
<div class="card">
    <h2>Dépôt de caution</h2>
    <p>Réservation <strong>#{{ $reservation->reference }}</strong></p>
    <p>Montant à sécuriser (non débité) : <strong>{{ number_format($reservation->caution, 2, ',', ' ') }} €</strong></p>

    <form id="payment-form">
        <div id="payment-element"></div>
        <button id="submit">Sécuriser ma caution</button>
        <div id="error-message"></div>
    </form>
</div>

<script>
    const stripe = Stripe("{{ config('ipsum.reservation.caution_token') }}");
    const elements = stripe.elements({ clientSecret: "{{ $clientSecret }}" });
    const paymentElement = elements.create("payment");
    paymentElement.mount("#payment-element");

    const form = document.getElementById("payment-form");
    const submitBtn = document.getElementById("submit");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        submitBtn.disabled = true;

        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: "{{ route('reservation.confirmation', ['reservation' => $reservation->id]) }}",
            },
        });

        if (error) {
            document.getElementById("error-message").textContent = error.message;
            submitBtn.disabled = false;
        }
    });
</script>
</body>
</html>