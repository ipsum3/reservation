@component('mail::message')
Bonjour,

@if($inspection->type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
Vous venez de signer éléctroniquement le contrat et l'état des lieux {{ strtolower($inspection->type->nom) }} pour votre réservation n°{{ $reservation->reference }} sur le site [{{ config('settings.nom_site') }}]({{ config('app.url') }}).

Vous pouvez retrouver ces documents en pièce jointe de ce courriel.
@else
Vous venez de signer éléctroniquement l'état des lieux {{ strtolower($inspection->type->nom) }} pour votre réservation n°{{ $reservation->reference }} sur le site [{{ config('settings.nom_site') }}]({{ config('app.url') }}).

Vous pouvez retrouver ce document en pièce jointe de ce courriel.
@endif

Bonne réception.
@endcomponent
