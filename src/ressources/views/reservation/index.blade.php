@extends('IpsumAdmin::layouts.app')
@section('title', 'Réservation')

@section('content')

    <h1 class="main-title">Réservation</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $reservations->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>&nbsp;
                <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.export', request()->all()) }}">
                    <i class="fas fa-upload"></i>
                    Export
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.reservation.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            <label class="sr-only" for="type_id">Etat</label>
            {{ Aire::select(collect(['' => '---- Etat -----'])->union($etats), 'etat_id')->value(request()->get('etat_id'))->id('etat_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <label class="sr-only" for="modalite_paiement_id">Modalité</label>
            {{ Aire::select(collect(['' => '---- Modalités -----'])->union($modalites), 'modalite_paiement_id')->value(request()->get('modalite_paiement_id'))->id('modalite_paiement_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <label class="sr-only" for="date_debut">Date de début</label>
            {{ Aire::date('date_debut')->value(request()->get('date_debut'))->id('date_debut')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <label class="sr-only" for="date_debut">Date de fin</label>
            {{ Aire::date('date_fin')->value(request()->get('date_fin'))->id('date_fin')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'reference'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Création', 'champ' => 'created_at'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Départ', 'champ' => 'debut_at'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Agence', 'champ' => 'debut_lieu_nom'])</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Etat', 'champ' => 'etat_id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Modalité', 'champ' => 'modalite_paiement_id'])</th>
                    <th width="180px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->reference }}</td>
                        <td>{{ $reservation->created_at->format('d/m/Y') }}</td>
                        <td>{{ $reservation->debut_at->format('d/m/Y') }}</td>
                        <td>{{ $reservation->debut_lieu_nom }}</td>
                        <td>
                            @if ($reservation->client)
                                <a href="{{ route('admin.client.edit', $reservation->client) }}">{{ $reservation->prenom }} {{ $reservation->nom }}</a>
                            @else
                                {{ $reservation->prenom }} {{ $reservation->nom }}
                            @endif
                        </td>
                        <td class="text-right">@prix($reservation->total) &nbsp;€</td>
                        <td>{{ $reservation->etat ? $reservation->etat->nom : '' }}</td>
                        <td>{{ $reservation->modalite ? $reservation->modalite->nom : '' }}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.reservation.destroy', $reservation) }}" method="POST">
                                @if($reservation->is_confirmed)
                                    <a class="btn btn-primary" href="{{ route('admin.reservation.confirmation', [$reservation]) }}"><i class="fa fa-eye"></i> Voir</a>
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

            {!! $reservations->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection