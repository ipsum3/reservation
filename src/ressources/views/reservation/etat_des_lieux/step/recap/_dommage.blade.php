@if(($reservation->vehicule?->dommages && $inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID) || $inspection->dommages->count())
    @if($reservation->vehicule?->dommages && $inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
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
@endif