@php
    $photos = $inspection->medias()->groupe('photos')->get();
@endphp
@if($photos->count())
    @foreach($photos as $media)
        @include('IpsumReservation::reservation.etat_des_lieux.step._media')
    @endforeach
@endif