@extends('IpsumAdmin::layouts.app')
@section('title', 'Départs et retours')

@section('content')

    <h1 class="main-title">Départs et retours</h1>

    <div class="box">
        <div class="box-header">
            <div class="box-title">
                {{ Aire::open()->class('form-inline')->route('admin.reservation.departEtRetour') }}
                <label class="sr-only" for="date_debut">Date</label>
                {{ Aire::date('date')->value($date)->id('date_debut')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
                <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
                {{ Aire::close() }}
            </div>
            <div class="btn-toolbar">
                <a href="{{ route('admin.reservation.contratDepart', ['date' => $date->format('Y-m-d')]) }}" class="btn btn-outline-secondary" data-toggle="tooltip" title="Télécharger tous les contrats"><i class="fas fa-file-signature"></i></a>&nbsp;
            </div>
        </div>
    </div>

    <h2 class="main-title">Départs</h2>
    @foreach($heures_depart as $heure => $reservations)
        @include('IpsumReservation::reservation._depart-retour_tableau')
    @endforeach

    <h2 class="main-title">Retours</h2>
    @foreach($heures_retour as $heure => $reservations)
        @include('IpsumReservation::reservation._depart-retour_tableau')
    @endforeach

@endsection