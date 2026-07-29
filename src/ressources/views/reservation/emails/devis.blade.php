@component('mail::message')
Bonjour,

{{ $message }}

@if (config('ipsum.reservation.module_de_paiement'))
Vous pouvez valider le devis en cliquant sur le lien ci-dessous.
@component('mail::button', ['url' => URL::signedRoute('devis.show', $reservation), 'color' => 'primary'])
Accéder au lien de paiement
@endcomponent
@endif


Bonne réception.
@endcomponent
