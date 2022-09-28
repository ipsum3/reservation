@extends('IpsumAdmin::layouts.app')
@section('title', 'Planning des réservations')

@section('content')

    <h1 class="main-title">Planning des réservations</h1>

    @foreach($categories as $categorie)
        <div class="box">
            <div class="box-header">
                <h2 class="box-title">Catégorie {{ $categorie->nom }} </h2>
            </div>
            <div class="box-body">
                <table>
                    <thead>
                        <tr>
                            <td style="min-width: 100px;"></td>
                            @for($date = $date_debut->copy(); $date->lt($date_fin); $date->addMonth()->firstOfMonth())
                                <th style="border: 1px solid gray" colspan="{{ $date->diffInDays($date->copy()->lastOfMonth()->endOfDay()) + 1 }}">
                                    @if($date->diffInDays($date->copy()->lastOfMonth()->endOfDay()) > 4)
                                        {{ $date->format('F Y') }}
                                    @endif
                                </th>
                            @endfor
                        </tr>
                        <tr>
                            <td></td>
                            @for($date = $date_debut->copy(); $date->lt($date_fin); $date->addDay())
                                <th style="border: 1px solid gray">{{ $date->format('d') }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categorie->reservations as $reservation)
                            <tr>
                                <th>
                                    Réservation {{ $reservation->reference }}
                                    @php
                                        $resas = [];
                                        $resa_debut_at = $reservation->debut_at->greaterThanOrEqualTo($date_debut) ? $reservation->debut_at->copy() : $date_debut->copy();
                                        $resa_fin_at = $reservation->fin_at->lessThan($date_fin) ? $reservation->fin_at->copy() : $date_fin->copy();


                                        $reservation->width = $resa_debut_at->floatDiffInDays($resa_fin_at) * 40;
                                        $reservation->decalage = $resa_debut_at->copy()->startOfDay()->floatDiffInDays($resa_debut_at) * 40;

                                        $resas[$resa_debut_at->format('Y-m-d')][] = $reservation;
                                    @endphp
                                </th>
                                @for($date = $date_debut->copy(); $date->lt($date_fin); $date->addDay())
                                    <td style="border: 0 none; vertical-align: top">
                                        <div style="position: relative; width: 38px;">
                                            @if (isset($resas[$date->format('Y-m-d')]))
                                                @foreach($resas[$date->format('Y-m-d')] as $resa)
                                                    <a href="{{ route('admin.reservation.edit', $resa) }}" style="display: block; width: {{ $resa->width }}px; height: 40px; left: {{ $resa->decalage }}px; position: absolute; top : 0; overflow: hidden"
                                                         class="bg-danger"
                                                         data-toggle="tooltip" data-placement="auto" data-html="true" title="
                                                         <div>Réservation : {{ $resa->reference }}</div>
                                                         <div>
                                                            Départ : {{ $reservation->debut_lieu_nom }} {{ $resa->debut_at->format('d/m/Y H\hi') }}<br>
                                                            Retour : {{ $reservation->fin_lieu_nom }} {{ $resa->fin_at->format('d/m/Y H\hi') }}<br>
                                                         </div>"
                                                    >
                                                        {{ $reservation->prenom.' '.$reservation->nom }}
                                                    </a>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                        @foreach($categorie->vehicules as $vehicule)
                            <tr>
                                <th>
                                    {{ $vehicule->immatriculation }}<br>
                                    {{ $vehicule->marque_modele }}
                                    @php
                                        $resas = [];
                                    @endphp
                                    @foreach($vehicule->reservations as $reservation)
                                        @php
                                            $resa_debut_at = $reservation->debut_at->greaterThanOrEqualTo($date_debut) ? $reservation->debut_at->copy() : $date_debut->copy();
                                            $resa_fin_at = $reservation->fin_at->lessThan($date_fin) ? $reservation->fin_at->copy() : $date_fin->copy();


                                            $reservation->width = $resa_debut_at->floatDiffInDays($resa_fin_at) * 40;
                                            $reservation->decalage = $resa_debut_at->copy()->startOfDay()->floatDiffInDays($resa_debut_at) * 40;

                                            $resas[$resa_debut_at->format('Y-m-d')][] = $reservation;
                                        @endphp
                                    @endforeach
                                </th>
                                @for($date = $date_debut->copy(); $date->lt($date_fin); $date->addDay())
                                    <td style="border: 0 none; vertical-align: top">
                                        <div style="position: relative; width: 38px;">
                                            @if (isset($resas[$date->format('Y-m-d')]))
                                                @foreach($resas[$date->format('Y-m-d')] as $resa)
                                                    <a href="{{ route('admin.reservation.edit', $resa) }}" style="display: block; width: {{ $resa->width }}px; height: 40px; left: {{ $resa->decalage }}px; position: absolute; top : 0; overflow: hidden"
                                                         class="bg-success"
                                                         data-toggle="tooltip" data-placement="auto" data-html="true" title="
                                                         <div>Réservation : {{ $resa->reference }}</div>
                                                         <div>
                                                            Départ : {{ $resa->debut_lieu_nom }} {{ $resa->debut_at->format('d/m/Y H\hi') }}<br>
                                                            Retour : {{ $resa->fin_lieu_nom }} {{ $resa->fin_at->format('d/m/Y H\hi') }}<br>
                                                         </div>"
                                                    >
                                                        {{ $resa->prenom.' '.$resa->nom }}
                                                    </a>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    @endforeach

    <script>
        $(function () {
            $('[data-toggle="popover"]').popover()
        })

    </script>

@endsection