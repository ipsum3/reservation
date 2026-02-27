@extends('IpsumAdmin::layouts.app')
@section('title', 'Départs et retours')

@section('content')

    <h1 class="main-title">Départs et retours</h1>

    <div class="box">
        <div class="box-header">
            <div class="box-title">
                {{ Aire::open()->class('form-inline')->route('admin.reservation.departEtRetour') }}
                <label class="sr-only" for="dates">Date</label>
                @if (config('ipsum.reservation.depart_retour_date_range'))
                    {{ Aire::input('dates')->value($debut_at->format('d/m/Y') . ' - ' . $fin_at->format('d/m/Y'))->id('dates')->class('form-control mb-2 mr-sm-2 datepicker-range-next')->style('width: 200px')->withoutGroup() }}
                @else
                    {{ Aire::date('debut_at')->value($debut_at->format('Y-m-d'))->id('date_debut')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
                @endif
                <label class="sr-only" for="lieu_id">Lieu</label>
                {{ Aire::select(collect(['' => '---- Lieux -----'])->union($lieux), 'lieu_id')->value(request()->get('lieu_id'))->id('lieu_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
                <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
                {{ Aire::close() }}
            </div>
            <div class="btn-toolbar">
                <a href="{{ route('admin.reservation.imprimerDepartEtRetour', ['debut_at' => $debut_at->format('Y-m-d'), 'fin_at' => $fin_at->format('Y-m-d'), 'lieu_id' => request()->get('lieu_id')]) }}" class="btn btn-outline-secondary" data-toggle="tooltip" title="Imprimer le tableau"><i class="fas fa-print"></i></a>&nbsp;
                <a href="{{ route('admin.reservation.contratDepart', ['debut_at' => $debut_at->format('Y-m-d'), 'fin_at' => $fin_at->format('Y-m-d'), 'lieu_id' => request()->get('lieu_id')]) }}" class="btn btn-outline-secondary" data-toggle="tooltip" title="Télécharger tous les contrats"><i class="fas fa-file-signature"></i></a>&nbsp;
            </div>
        </div>
    </div>

    @foreach($jours as $date => $jour)
        <h2 class="main-title">{{ ucfirst(\Carbon\Carbon::createFromFormat('Y-m-d', $date)->isoFormat('dddd D MMMM G')) }}</h2>

        @foreach($jour->groupBy(function ($reservation) { return  $reservation->is_depart ? $reservation->debut_at->format('H') : $reservation->fin_at->format('H'); })->sortKeys() as $heure => $reservations)
            @include('IpsumReservation::reservation._depart-retour_tableau')
        @endforeach
        <div class="mb-5"></div>
    @endforeach

@endsection