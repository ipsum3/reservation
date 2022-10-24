@extends('IpsumAdmin::layouts.app')
@section('title', 'Véhicule')

@section('content')

    <h1 class="main-title">Véhicule</h1>

    <div class="row">
        <div class="col-md-6">
            {{ Aire::open()->route($vehicule->exists ? 'admin.vehicule.update' : 'admin.vehicule.store', $vehicule->exists ? [$vehicule] : '')->bind($vehicule)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreVehicule::class) }}
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">{{ $vehicule->exists ? 'Modification' : 'Ajout' }}</h2>
                    <div class="btn-toolbar">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                        <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                        @if ($vehicule->exists)
                            <a class="btn btn-outline-secondary" href="{{ route('admin.vehicule.create') }}" data-toggle="tooltip" title="Ajouter">
                                <i class="fas fa-plus"></i>
                            </a>&nbsp;
                            <a class="btn btn-outline-danger" href="{{ route('admin.vehicule.destroy', $vehicule) }}" data-toggle="tooltip" title="Supprimer">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-row">
                        {{ Aire::input('immatriculation', 'Immatriculation*')->required()->groupAddClass('col-md-6') }}
                        {{ Aire::date('mise_en_circualtion_at', 'Date de mise en circulation*')->required()->groupAddClass('col-md-6') }}
                        {{ Aire::input('marque_modele', 'Marque modèle*')->required()->groupAddClass('col-md-6') }}
                        {{ Aire::select(collect(['' => '---- Catégories -----'])->union($categories), 'categorie_id', 'Catégorie*')->groupAddClass('col-md-4') }}
                        {{ Aire::date('entree_at', 'Date d\'entrée*')->groupAddClass('col-md-6') }}
                        {{ Aire::date('sortie_at', 'Date de sortie')->groupAddClass('col-md-6') }}
                    </div>
                </div>
            </div>
            {{ Aire::close() }}
        </div>

        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Prochaines interventions</h2>
                    <div class="btn-toolbar">
                        @if ($vehicule->exists)
                            <a class="btn btn-outline-secondary" href="{{ route('admin.intervention.create', ['vehicule_id' => $vehicule]) }}" data-toggle="tooltip" title="Ajouter">
                                <i class="fas fa-plus"></i>
                            </a>&nbsp;
                        @endif
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Type.</th>
                            <th scope="col">Début</th>
                            <th scope="col">Fin</th>
                            <th scope="col">Intervenant</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($vehicule->interventions as $intervention)
                            <tr>
                                <td><a href="{{ route('admin.intervention.edit', $intervention) }}">{{ $intervention->id }}</a></td>
                                <td>{{ $intervention->type ? $intervention->type->nom : '' }}</td>
                                <td>{{ $intervention->debut_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $intervention->fin_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $intervention->intervenant }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Prochaines réservations</h2>
                </div>
                <div class="box-body">
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Cat.</th>
                            <th scope="col">Montant</th>
                            <th scope="col">Etat</th>
                            <th scope="col">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($vehicule->reservations as $reservation)
                            <tr>
                                <td><a href="{{ route('admin.reservation.edit', [$reservation]) }}">{{ $reservation->reference }}</a></td>
                                <td>{{ $reservation->categorie_nom }}</td>
                                <td class="text-right">@prix($reservation->total) &nbsp;€</td>
                                <td>
                                    @if ($reservation->etat)
                                        <span class="badge badge-{{ $reservation->is_confirmed ? 'success' : 'light' }}">{{ $reservation->etat->nom }}</span>
                                    @endif
                                </td>
                                <td>{{ $reservation->debut_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection
