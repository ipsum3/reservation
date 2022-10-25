@extends('IpsumAdmin::layouts.app')
@section('title', 'Clients')

@section('content')

    <h1 class="main-title">Client <small>({{ $client->exists ? 'Modification' : 'Ajout' }})</small></h1>

    <div class="row">
        <div class="col-md-6">
            {{ Aire::open()->route('admin.client.update', [$client->id])->bind($client)->formRequest(\Ipsum\Reservation\app\Http\Requests\UpdateClient::class) }}
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Connexion</h2>
                    <div class="btn-toolbar">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                        <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                        @if ($client->exists)
                            <a class="btn btn-outline-danger" href="{{ route('admin.client.destroy', $client) }}" data-toggle="tooltip" title="Supprimer">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-row">
                        {{ Aire::password('password', 'Mot de passe')->helpText('Laisser vide pour ne pas le modifier')->value('')->groupAddClass('col-md-6') }}
                        {{ Aire::input('email', 'Email*')->groupAddClass('col-md-6') }}
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Information</h2>
                </div>
                <div class="box-body">
                    <div class="form-row">
                        {{ Aire::input('nom', 'Nom*')->groupAddClass('col-md-6') }}
                        {{ Aire::input('prenom', 'Prénom*')->groupAddClass('col-md-6') }}
                    </div>
                    <div class="form-row">
                        {{ Aire::input('telephone', 'Téléphone*')->groupAddClass('col-md-6') }}
                    </div>
                    <div class="form-row">
                        {{ Aire::input('adresse', 'Adresse*')->groupAddClass('col-md-3') }}
                        {{ Aire::input('cp', 'Code postal*')->groupAddClass('col-md-3') }}
                        {{ Aire::input('ville', 'Ville*')->groupAddClass('col-md-3') }}
                        {{ Aire::select($pays, 'pays_id', 'Pays*')->groupAddClass('col-md-3') }}
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Permis</h2>
                </div>
                <div class="box-body">
                    <div class="form-row">
                        {{ Aire::date('naissance_at', 'Date de naissance*')->groupAddClass('col-md-3') }}
                        {{ Aire::input('permis_numero', 'N° du permis*')->groupAddClass('col-md-3') }}
                        {{ Aire::date('permis_at', 'Permis délivré le*')->groupAddClass('col-md-3') }}
                        {{ Aire::input('permis_delivre', 'Permis délivré par*')->groupAddClass('col-md-3') }}
                    </div>
                </div>
            </div>
            @if (config('ipsum.reservation.client.custom_fields'))
                <div class="box">
                    <div class="box-header">
                        <h2 class="box-title">
                            Informations complémentaires
                        </h2>
                    </div>
                    <div class="box-body">
                        @foreach(config('ipsum.reservation.client.custom_fields') as $field)
                            <x-admin::custom
                                    name="{{ 'custom_fields['.$field['name'].']' }}"
                                    label="{{ $field['label'] }}"
                                    description="{{ $field['description'] }}"
                                    value="{!! old('custom_fields.'.$field['name'], $client->custom_fields->{$field['name']}) !!}"
                                    type="{{ $field['type'] }}"
                            />
                        @endforeach
                    </div>
                </div>
            @endif
            {{ Aire::close() }}
        </div>
        <div class="col-md-6">
            <div class="box" id="demandes">
                <div class="box-header">
                    <h2 class="box-title">Réservations</h2>
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
                        @foreach($client->reservations->sortByDesc('created_at') as $reservation)
                            <tr>
                                <td><a href="{{ route('admin.reservation.edit', [$reservation]) }}">{{ $reservation->reference }}</a></td>
                                <td>{{ $reservation->categorie_nom }}</td>
                                <td class="text-right">@prix($reservation->total) &nbsp;€</td>
                                <td>
                                    @if ($reservation->etat)
                                        <span class="badge badge-{{ $reservation->is_confirmed ? 'success' : 'light' }}">{{ $reservation->etat->nom }}</span>
                                    @endif
                                </td>
                                <td>{{ $reservation->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
