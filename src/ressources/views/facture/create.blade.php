@extends('IpsumAdmin::layouts.app')
@section('title', 'Facture')

@section('content')

    <h1 class="main-title">{{ $facture->exists ? 'Modification' : 'Création' }} de facture <a href="{{ route('admin.reservation.edit', $reservation) }}"><small class="text-muted">(résa. {{ $reservation->reference }})</small></a></h1>

    {{ Aire::open()->route('admin.facture.store', [$reservation])->formRequest(\Ipsum\Reservation\app\Http\Requests\CreateFacture::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Client</h2>
            <div class="btn-toolbar">
                @if (!$facture->exists)
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Créer et envoyer la facture</button>&nbsp;
                @else
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                    <a class="btn btn-secondary"><i class="fas fa-envelope"></i> Envoyer TODO</a>
                @endif
            </div>
        </div>
        <div class="box-body">

            @if ($reservation->client?->numero)
                Client {{ $reservation->client->numero }}<br>
            @endif
            TODO pro<br>
            <i class="fa fa-user"></i> {{ $reservation->prenom }} {{ $reservation->nom }}<br>
            <i class="fa fa-envelope"></i> {{ $reservation->email }}<br>
            <i class="fa fa-phone"></i> {{ $reservation->telephone }}<br>
            <i class="fa fa-address-card"></i> {{ $reservation->adresse }}<br>
            {{ $reservation->cp }} - {{ $reservation->ville }} - {{ $reservation->pays->nom }}


            // TODO mettre la date d'échénace à +1 moi
            {{--
            <div clform-row">
               {{ Aire::date('nom', 'Nom*')->groupAddClass('col-md-6') }}
               {{ Aire::select(collect(['' => '---- Types -----'])->union(collect(\Ipsum\Reservation\app\Enum\FactureType::pluck())), 'type_id', 'Type*')->groupAddClass('col-md-6') }}
            </div>
            --}}
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Produits</h2>
                    @if (!$facture->exists)
                        <div class="btn-toolbar">
                            <button class="btn btn-outline-secondary table-editable-add" data-target="prestation" id="prestation-add" type="button" data-toggle="tooltip" title="Ajouter">
                                <i class="fas fa-plus"></i>
                            </button>&nbsp;
                        </div>
                    @endif
                </div>
                <div class="box-body">
                    <div class="table-wrapper">
                        <table class="table table-hover table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Nom</th>
                                <th scope="col">Quantité</th>
                                <th scope="col">Montant TTC</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="prestation-lignes">
                                @if (!$reservation->factureLocation)
                                    <tr>
                                        <td>Location de voiture</td>
                                        <td>1</td>
                                        <td>125&nbsp;€</td>
                                        <td></td>
                                    </tr>

                                    @foreach($reservation->prestations as $prestation)
                                        <tr>
                                            <td>{{ $prestation->nom }}</td>
                                            <td>{{ $prestation->quantite }}</td>
                                            <td>@prix($prestation->tarif)&nbsp;€</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                @endif
                                <script id="prestation-add-template" type="x-tmpl-mustache">
                                    <tr>
                                        <td>
                                            <select class="form-control" name="paiements[@{{ indice }}][paiement_type_id]" required>
                                                <option value="">-- Prestations --</option>
                                                @foreach($prestations as $prestation)
                                                    <option value="{{ $prestation->id }}">{{ $prestation->nom }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" value="" name="paiements[@{{ indice }}][quantite]" required></td>
                                        <td><input type="number" class="form-control" step=".01" value="" name="paiements[@{{ indice }}][montant]" required></td>
                                        <td><button type="button" class="paiement-delete btn btn-outline-danger" data-confirm="false"><i class="fa fa-trash-alt"></i></button></td>
                                    </tr>
                                </script>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">
                        Réglements
                        <x-reservation::reste_a_payer total="{{ $reservation->total }}"  montant_paye="{{ $reservation->montant_paye }}" />
                    </h2>
                    <div class="btn-toolbar">
                        <button class="btn btn-outline-secondary table-editable-add" data-target="paiement" id="paiement-add" type="button" data-toggle="tooltip" title="Ajouter">
                            <i class="fas fa-plus"></i>
                        </button>&nbsp;
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-wrapper">
                        <table class="table table-hover table-striped">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Date</th>
                                <th scope="col" style="min-width: 120px;">Moyen</th>
                                <th scope="col" style="min-width: 120px;">Type</th>
                                <th scope="col" style="min-width: 110px;">Montant (€)</th>
                                <th scope="col" style="min-width: 100px;">Note</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody id="paiement-lignes">
                            @foreach($reservation->paiements()->ok()->with(['moyen', 'type'])->doesntHave('facture')->orderBy('created_at', 'desc')->get() as $paiement)
                                <tr>
                                    <td>{{ $paiement->id }}</td>
                                    <td>{{ $paiement->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $paiement->moyen->nom }}</td>
                                    <td>{{ $paiement->type->nom }}</td>
                                    <td>@prix($paiement->montant)&nbsp;€</td>
                                    <td>{!! nl2br(e($paiement->note )) !!}</td>
                                </tr>
                            @endforeach
                            <script id="paiement-add-template" type="x-tmpl-mustache">
                                <tr>
                                    <td><input type="hidden" name="paiements[@{{ indice }}][id]" value="" /><input type="hidden" name="paiements[@{{ indice }}][reservation_id]" value="{{ $reservation->id }}" /></td>
                                    <td><input type="datetime-local" class="form-control" name="paiements[@{{ indice }}][created_at]" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" required></td>
                                    <td>
                                        <select class="form-control" name="paiements[@{{ indice }}][paiement_moyen_id]" required>
                                            <option value="">-- Moyens --</option>
                                            @foreach($moyens as $moyen)
                                                <option value="{{ $moyen->id }}">{{ $moyen->nom }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="paiements[@{{ indice }}][paiement_type_id]" required>
                                            <option value="">-- Types --</option>
                                            @foreach($types as $type)
                                                <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control" step=".01" value="" name="paiements[@{{ indice }}][montant]" required></td>
                                    <td><textarea cols="30" rows="1" class="form-control" name="paiements[@{{ indice }}][note]"></textarea></td>
                                    <td><button type="button" class="paiement-delete btn btn-outline-danger" data-confirm="false"><i class="fa fa-trash-alt"></i></button></td>
                                </tr>
                            </script>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Aire::close() }}
@endsection
