@component('mail::message')
Bonjour,

Vous avez reçu un devis en provenance du site [{{{ config('settings.nom_site') }}}]({{{ config('app.url') }}}).

Retrouvez votre devis en pièce jointe de ce mail.

Bonne réception.
@endcomponent
