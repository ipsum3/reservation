@extends('IpsumAdmin::layouts.app')
@section('title', 'Départs et retours')

@section('content')

    <h1 class="main-title">Départs et retours</h1>

    <div class="box">
        <div class="box-header">
            <div class="box-title">
                {{ Aire::open()->class('form-inline')->route('admin.reservation.departEtRetour') }}
                <label class="sr-only" for="dates">Date</label>
                {{ Aire::input('dates')->value($dates)->id('dates')->class('form-control mb-2 mr-sm-2 datepicker-range-next')->style('width: 200px')->withoutGroup() }}
                <label class="sr-only" for="lieu_id">Lieu</label>
                {{ Aire::select(collect(['' => '---- Lieux -----'])->union($lieux), 'lieu_id')->value(request()->get('lieu_id'))->id('lieu_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
                <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
                {{ Aire::close() }}
            </div>
            <div class="btn-toolbar">
                <a href="{{ route('admin.reservation.imprimerDepartEtRetour', ['dates' => $dates, 'lieu_id' => request()->get('lieu_id')]) }}" class="btn btn-outline-secondary" data-toggle="tooltip" title="Imprimer le tableau"><i class="fas fa-print"></i></a>&nbsp;
                <a href="{{ route('admin.reservation.contratDepart', ['dates' => $dates, 'lieu_id' => request()->get('lieu_id')]) }}" class="btn btn-outline-secondary" data-toggle="tooltip" title="Télécharger tous les contrats"><i class="fas fa-file-signature"></i></a>&nbsp;
            </div>
        </div>
    </div>

    @foreach($jours as $jour)
        <h2 class="main-title">{{ ucfirst($jour['date']->isoFormat('dddd D MMMM G')) }}</h2>

        @if(isset($jour['depart']))
            <h3 class="main-title">Départs</h3>
            @foreach($jour['depart'] as $heure => $reservations)
                @include('IpsumReservation::reservation._depart-retour_tableau', ['is_depart' => true])
            @endforeach
        @endif

        @if(isset($jour['retour']))
            <h3 class="main-title">Retours</h3>
            @foreach($jour['retour'] as $heure => $reservations)
                @include('IpsumReservation::reservation._depart-retour_tableau', ['is_depart' => false])
            @endforeach
        @endif
        <div class="mb-5"></div>
    @endforeach

@endsection