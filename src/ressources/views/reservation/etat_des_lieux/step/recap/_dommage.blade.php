{{--@if(($reservation->vehicule?->dommages->count() && $inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID) || $inspection->dommages->count())
    @if($reservation->vehicule?->dommages->count() && $inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
        @foreach($reservation->vehicule?->dommages as $dommage)
            @if($dommage->inspection->id != $inspection->id && $dommage->inspection->id != $reservation->inspection_initiale->id)
                @include('IpsumReservation::reservation.etat_des_lieux.step._dommage')
            @endif
        @endforeach
    @endif


    @if($inspection->dommages->count())
        @foreach($inspection->dommages as $dommage)
            @include('IpsumReservation::reservation.etat_des_lieux.step._dommage')
        @endforeach
    @endif
@else
    <p>Aucun dommage constaté</p>
@endif--}}
@php
   // TODO pour la page show, impossible de connaitre les dommages de l'inspection iitiale après qu'une autre inspection soit faite. Il faudrait ajouter une table dommage_inspections
    $dommages = $inspection->type->is_initial ? $reservation->vehicule->dommages : $inspection->dommages;
@endphp

@if($dommages->count())
    @foreach($dommages as $dommage)
        @include('IpsumReservation::reservation.etat_des_lieux.step._dommage')
    @endforeach
@else
    <p>Aucun dommage constaté</p>
@endif