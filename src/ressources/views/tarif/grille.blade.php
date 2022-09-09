@extends('IpsumAdmin::layouts.app')
@section('title', 'Tarifs')


@section('content')

    <h1 class="main-title">Tarifs</h1>
    {{ Aire::open()->route('admin.tarif.update', $saison) }}

        @if ($modalites)
            @foreach($modalites as $modalite)
                @include('IpsumReservation::tarif._grille')
            @endforeach
        @else
            @include('IpsumReservation::tarif._grille')
        @endif

    {{ Aire::close() }}

@endsection
