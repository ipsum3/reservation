@component('mail::message')
Bonjour,

Vous avez reçu un devis en provenance du site [{{ config('settings.nom_site') }}]({{ config('app.url') }}).

Retrouvez votre devis en pièce jointe de ce mail
@if (config('ipsum.reservation.module_de_paiement'))
et le lien de paiement ci-contre : [{{ URL::signedRoute('devis.redirectBanque', $reservation) }}]({{ URL::signedRoute('devis.redirectBanque', $reservation) }})
@endif

Bonne réception.
@endcomponent
