@extends('IpsumAdmin::layouts.app')
@section('title', 'Réservations')

@section('content')

    <h1 class="main-title">Réservation {{ $reservation->exists ? $reservation->reference : '' }}</h1>

    {{ Aire::open()->route($reservation->exists ? 'admin.reservation.update' : 'admin.reservation.store', $reservation->exists ? [$reservation] : '')->bind($reservation)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreAdminReservation::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $reservation->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($reservation->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    @can('delete', $reservation)
                        <a class="btn btn-outline-danger" href="{{ route('admin.reservation.destroy', $reservation) }}" data-toggle="tooltip" title="Supprimer">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    @endcan
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::textArea('observation', 'Observation client')->groupAddClass('col-md-6') }}

                {{ Aire::select(collect(['' => '---- Etats -----'])->union($etats), 'etat_id', 'Etat*')->required()->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Modalités -----'])->union($modalites), 'modalite_paiement_id', 'Modalité*')->required()->groupAddClass('col-md-6') }}

                {{ Aire::select(collect(['' => '---- Catégories -----'])->union($categories), 'categorie_id', 'Catégorie*')->required()->groupAddClass('col-md-6') }}
                {{ Aire::number('franchise', 'Franchise (€)')->setAttribute('step', 0.01)->groupAddClass('col-md-6') }}
                {{ Aire::dateTime('debut_at', 'Date départ*')->required()->groupAddClass('col-md-6') }}
                {{ Aire::dateTime('fin_at', 'Date fin*')->required()->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Lieux -----'])->union($lieux), 'debut_lieu_id', 'Lieu départ*')->required()->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Lieux -----'])->union($lieux), 'fin_lieu_id', 'Lieu retour*')->required()->groupAddClass('col-md-6') }}

                {{-- TODO prestations et promotions --}}

                {{ Aire::number('montant_base', 'Montant de base (€)')->setAttribute('step', 0.01)->groupAddClass('col-md-6') }}
                {{ Aire::number('total', 'Total (€)')->setAttribute('step', 0.01)->groupAddClass('col-md-6') }}

                {{ Aire::textArea('note', 'Notes')->groupAddClass('col-md-6') }}
            </div>
        </div>
    </div>

    @if (config('ipsum.reservation.custom_fields'))
        <div class="box">
            <div class="box-header">
                <h2 class="box-title">
                    Informations complémentaires
                </h2>
            </div>
            <div class="box-body">
                @foreach(config('ipsum.reservation.custom_fields') as $field)
                    <x-admin::custom
                            name="{{ 'custom_fields['.$field['name'].']' }}"
                            label="{{ $field['label'] }}"
                            description="{{ $field['description'] }}"
                            value="{{ old('custom_fields.'.$field['name'], isset($reservation->custom_fields[$field['name']]) ? $reservation->custom_fields[$field['name']] : null) }}"
                            type="{{ $field['type'] }}"
                    />
                @endforeach
            </div>
        </div>
    @endif

    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Client</h2>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::input('prenom', 'Prénom')->groupAddClass('col-md-6') }}
                {{ Aire::input('nom', 'Nom*')->required()->groupAddClass('col-md-6') }}
                {{ Aire::input('email', 'Email')->groupAddClass('col-md-6') }}
                {{ Aire::input('telephone', 'Téléphone')->groupAddClass('col-md-6') }}
                {{ Aire::input('adresse', 'Adresse')->groupAddClass('col-md-6') }}
                {{ Aire::input('cp', 'Code postal')->groupAddClass('col-md-6') }}
                {{ Aire::input('ville', 'Ville')->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Pays -----'])->union($pays), 'pays_id', 'Pays')->groupAddClass('col-md-6') }}
                {{ Aire::date('naissance_at', 'Date de naissance')->groupAddClass('col-md-6') }}
                {{ Aire::input('permis_numero', 'Numéro de permis')->groupAddClass('col-md-6') }}
                {{ Aire::date('permis_at', 'Permis délivré le')->groupAddClass('col-md-6') }}
                {{ Aire::input('permis_delivre', 'Permis délivré par')->groupAddClass('col-md-6') }}
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Documents</h2>
        </div>
        <div class="box-body">

            @if($reservation->is_confirmed)
                <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.confirmation', [$reservation]) }}"><i class="fa fa-eye"></i> Voir la confirmation</a>&nbsp;
                <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.confirmationSend', [$reservation]) }}" ><i class="fas fa-envelope"></i> Renvoyer le mail de confirmation</a>&nbsp;
            @endif

        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Paiements</h2>
        </div>
        <div class="box-body">

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Date</th>
                    <th scope="col">Moyen</th>
                    <th scope="col">Montant</th>
                    <th scope="col">Transaction</th>
                </tr>
                </thead>
                <tbody>
                @foreach($reservation->paiements()->ok()->with('moyen')->orderBy('created_at', 'desc')->get() as $paiement)
                    <tr>
                        <td>{{ $paiement->id }}</td>
                        <td>{{ $paiement->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $paiement->moyen ? $paiement->moyen->nom : '' }}</td>
                        <td>@prix($paiement->montant) €</td>
                        <td>{{ $paiement->transaction_ref }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
    
    {{ Aire::close() }}


@endsection
