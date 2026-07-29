@component('mail::message')
# Bonjour {{ $reservation->prenom }} {{ $reservation->nom }},

Nous vous invitons à déposer votre caution en ligne pour votre réservation **#{{ $reservation->reference }}** prévue du **{{ $reservation->debut_at->format('d/m/Y à H:i') }}** au **{{ $reservation->fin_at->format('d/m/Y à H:i') }}**.

@component('mail::button', ['url' => $reservation->caution_url, 'color' => 'primary'])
Déposer ma caution en ligne
@endcomponent

Important : Le dépôt est à réaliser avant le **{{ $reservation->debut_at->format('d/m/Y') }}**.

@if($reservation->caution_frais)
Frais additionnels : **@prix($reservation->caution_frais) €**
@endif
Montant de la caution : **@prix($reservation->caution) €**

Cordialement.
@endcomponent