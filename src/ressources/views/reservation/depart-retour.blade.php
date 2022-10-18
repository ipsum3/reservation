@extends('IpsumAdmin::layouts.app')
@section('title', 'Départs et retours<')

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



    @foreach($heures as $heure => $reservations)
        <div class="box">
            <div class="box-header">
                <h2 class="box-title">{{ $heure }}</h2>
            </div>
            <div class="box-body">

                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th style="width: 60px"></th>
                            <th>Véhicule</th>
                            <th style="width: 20%">Lieu</th>
                            <th style="width: 20%">Client</th>
                            <th style="width: 100px">Dû</th>
                            <th style="width: 10%">Condition</th>
                            <th style="width: 140px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td class="text-white {{ $reservation->is_debut ? 'bg-success' : 'bg-info' }}">{{ $reservation->is_debut ? 'Départ' : 'Retour' }}</td>
                                <td>Catégorie {{ $reservation->categorie_nom }}<br>{{ $reservation->vehicule ? $reservation->vehicule->immatriculation : '' }}</td>
                                <td>{{ $reservation->is_debut ? $reservation->debut_lieu_nom : $reservation->fin_lieu_nom }}</td>
                                <td>
                                    @if ($reservation->client)
                                        <a href="{{ route('admin.client.edit', $reservation->client) }}">{{ $reservation->prenom }} {{ $reservation->nom }}</a>
                                    @else
                                        {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}
                                    @endif
                                    <br>{{ $reservation->telephone }}
                                </td>
                                <td class="text-right">
                                    @if ($reservation->total != $reservation->montant_paye)
                                        @prix($reservation->total - $reservation->montant_paye) &nbsp;€
                                    @endif
                                </td>
                                <td>{{ $reservation->condition ? $reservation->condition->nom : '' }}</td>
                                <td class="text-right">
                                    <form action="{{ route('admin.reservation.destroy', $reservation) }}" method="POST">
                                        @if ($reservation->is_debut)
                                            <a class="btn btn-outline-primary" href="{{ route('admin.reservation.contrat', [$reservation]) }}"><i class="fa fa-file-signature"></i></a>
                                        @endif
                                        <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.edit', [$reservation]) }}"><i class="fa fa-edit"></i></a>
                                        @can('delete', $reservation)
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    @endforeach

@endsection