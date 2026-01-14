@component('mail::message')
Bonjour,

Vous avez reçu l'état des lieux {{{ $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'initial': 'final' }}} de votre véhicule en provenance du site [{{{ config('settings.nom_site') }}}]({{{ config('app.url') }}}).

Veuillez trouver ci-joint le PDF de l'état des lieux de votre véhicule pour votre réservation n°{{ $reservation->id }}.

Bonne réception.
@endcomponent
