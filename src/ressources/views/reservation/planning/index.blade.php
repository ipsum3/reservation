@extends('IpsumAdmin::layouts.app')
@section('title', 'Planning des réservations')

@php

    function addInfosToReservation($reservations, $date_debut, $date_fin): array {
        $resas = [];
        foreach($reservations as $reservation) {
            $resa_debut_at = $reservation->debut_at->greaterThanOrEqualTo($date_debut) ? $reservation->debut_at->copy() : $date_debut->copy();
            $resa_fin_at = $reservation->fin_at->lessThan($date_fin) ? $reservation->fin_at->copy() : $date_fin->copy()->endOfDay();

            $reservation->width = $resa_debut_at->floatDiffInDays($resa_fin_at) * 35;
            $reservation->decalage = $resa_debut_at->copy()->startOfDay()->floatDiffInDays($resa_debut_at) * 35;
            $resas[$resa_debut_at->format('Y-m-d')][] = $reservation;
        }

        return $resas;
    }

@endphp

@section('content')

    <style>
        .box {
            /* Sur Chrome la boite n'est pas poussé sinon */
            display: inline-block;
            min-width: 100%;
            width: auto
        }
        .planning {

        }
        .planning thead {

        }
        .planning td,
        .planning th {
            padding: 2px 5px;
        }
        .planning-mois {
            border-left: 1px solid #ccc;
            font-weight: normal;
            color: #aaa;
        }
        .planning-jour {
            border-left: 1px solid #ccc;
            border-bottom: 2px solid gray;
            font-weight: normal;
        }
        .planning-vehicule-entete, .planning-reservation-entete {
            min-width: 150px;
            height: 60px;
            font-size: 12px;
            font-weight: normal;
            vertical-align: top;
        }
        .planning-case {
            padding: 0 !important;
            border: 0 none;
            vertical-align: top;
            border-bottom: 1px solid #ddd;
        }
        .planning-case > div {
            position: relative;
            width: 35px;
        }
        .planning-reservation {
            display: block;
            height: 40px;
            position: absolute;
            top : 10px;
            overflow: hidden;
            padding: 2px 5px;
            color: white;
            font-size: 12px;
        }
        .planning-reservation:hover {
            color: white;
            text-decoration: none;
        }
    </style>

    <h1 class="main-title">Planning des réservations</h1>

    <div class="box">
        <div class="box-body">
            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.reservation.planning') }}
            <label class="sr-only" for="date_debut">Date de début</label>
            {{ Aire::date('date_debut')->value($date_debut)->id('date_debut')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <label class="sr-only" for="date_debut">Date de fin</label>
            {{ Aire::date('date_fin')->value($date_fin)->id('date_fin')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <label class="sr-only" for="type_id">Catégorie</label>
            {{ Aire::select(collect(['' => '---- Catégorie -----'])->union($categories_all), 'categorie_id')->value(request()->get('categorie_id'))->id('categorie_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}
        </div>
    </div>

    @foreach($categories as $categorie)
        <div class="box">
            <div class="box-header">
                <h2 class="box-title">Catégorie {{ $categorie->nom }} </h2>
            </div>
            <div class="box-body">
                <table class="planning">
                    <thead>
                        <tr>
                            <td></td>
                            @for($date = $date_debut->copy(); $date->lte($date_fin); $date->addMonth()->firstOfMonth())
                                <th class="planning-mois" colspan="{{ $date->diffInDays($date->copy()->lastOfMonth()->endOfDay()) + 1 }}">
                                    @if($date->diffInDays($date->copy()->lastOfMonth()->endOfDay()) > 4)
                                        {{ $date->format('F Y') }}
                                    @endif
                                </th>
                            @endfor
                        </tr>
                        <tr>
                            <td></td>
                            @for($date = $date_debut->copy(); $date->lte($date_fin); $date->addDay())
                                <th class="planning-jour">{{ $date->format('d') }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categorie->reservations as $reservation)
                            @include('IpsumReservation::reservation.planning._ligne', ['type' => 'reservation'])
                        @endforeach
                        @foreach($categorie->vehicules as $vehicule)
                            @include('IpsumReservation::reservation.planning._ligne', ['type' => 'vehicule'])
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    @endforeach

@endsection