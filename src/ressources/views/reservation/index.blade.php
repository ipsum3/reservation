@extends('IpsumAdmin::layouts.app')
@section('title', 'Réservation')

@section('content')

    <h1 class="main-title">Réservations</h1>
    <div class="row">
        <div class="col-md-3">
            <div class="box">
                <div class="box-body">
                    <div class="stat-description">
                        Réservations hier
                    </div>
                    <div class="stat-number lead">
                        <strong>{{ $stats['hier'] }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="box">
                <div class="box-body">
                    <div class="stat-description">
                        Réservations aujourd'hui
                    </div>
                    <div class="stat-number lead">
                        <strong>{{ $stats['jour'] }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="box">
                <div class="box-body">
                    <div class="stat-description">
                        Reservations du mois
                    </div>
                    <div class="stat-number lead">
                        <strong>{{ $stats['mois'] }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="box">
                <div class="box-body">
                    <div class="stat-description">
                        CA réservations du mois
                    </div>
                    <div class="stat-number lead">
                        <strong>@prix($stats['montant']) &nbsp;€</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->style('width: 180px')->withoutGroup() }}
            <label class="sr-only" for="type_id">Catégorie</label>
            {{ Aire::select(collect(['' => '---- Catégories -----'])->union($categories), 'categorie_id')->value(request()->get('categorie_id'))->id('categorie_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <label class="sr-only" for="type_id">Etat</label>
            {{ Aire::select(collect(['' => '---- Etats -----'])->union($etats), 'etat_id')->value(request()->get('etat_id'))->id('etat_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <label class="sr-only" for="condition_paiement_id">Condition</label>
            {{ Aire::select(collect(['' => '---- Conditions -----'])->union($conditions), 'condition_paiement_id')->value(request()->get('condition_paiement_id'))->id('condition_paiement_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <label class="sr-only" for="date_creation">Date de création</label>
            {{ Aire::input('date_creation')->value(request()->get('date_creation'))->id('date_creation')->placeholder('Date de création')->style('width: 200px')->class('form-control mb-2 mr-sm-2 datepicker-range')->withoutGroup() }}
            <label class="sr-only" for="date_debut">Date de début</label>
            {{ Aire::input('date_debut')->value(request()->get('date_debut'))->id('date_debut')->placeholder('Date de début')->style('width: 200px')->class('form-control mb-2 mr-sm-2 datepicker-range')->withoutGroup() }}
            <label class="sr-only" for="date_fin">Date de fin</label>
            {{ Aire::input('date_fin')->value(request()->get('date_fin'))->id('date_fin')->placeholder('Date de fin')->style('width: 200px')->class('form-control mb-2 mr-sm-2 datepicker-range')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'reference'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Création', 'champ' => 'created_at'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Départ', 'champ' => 'debut_at'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Lieu', 'champ' => 'debut_lieu_nom'])</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Etat', 'champ' => 'etat_id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Condition', 'champ' => 'condition_paiement_id'])</th>
                    <th width="180px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($reservations as $reservation)
                    <tr class="{{ $reservation->fin_at->lt(\Carbon\Carbon::now()) ? 'text-muted' : '' }}">
                        <td>{{ $reservation->reference }}</td>
                        <td>{{ $reservation->created_at->format('d/m/Y') }}</td>
                        <td>{{ $reservation->debut_at->format('d/m/Y') }}</td>
                        <td>{{ $reservation->debut_lieu_nom }}</td>
                        <td>
                            @if ($reservation->client)
                                <a href="{{ route('admin.client.edit', $reservation->client) }}">{{ $reservation->prenom }} {{ $reservation->nom }}</a>
                            @else
                                {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($reservation->total)
                                @prix($reservation->total) &nbsp;€
                            @endif
                        </td>
                        <td>
                            @if ($reservation->etat)
                                <span class="badge badge-{{ $reservation->is_confirmed ? 'success' : 'light' }}">{{ $reservation->etat->nom }}</span>
                            @endif
                        </td>
                        <td>{{ $reservation->condition ? $reservation->condition->nom : '' }}</td>
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