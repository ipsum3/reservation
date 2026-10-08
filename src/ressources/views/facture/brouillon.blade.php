@extends('IpsumAdmin::layouts.app')
@section('title', 'Facture')

@section('content')

    <h1 class="main-title">{{ $facture->exists ? 'Modification' : 'Création' }} de facture <a href="{{ route('admin.reservation.edit', $reservation) }}"><small class="text-muted">(résa. {{ $reservation->reference }})</small></a></h1>

    {{ Aire::open()->route($facture->exists ? 'admin.facture.update.brouillon' : 'admin.facture.store.brouillon', $facture->exists ? $facture : $reservation)->bind($facture)->formRequest(\Ipsum\Reservation\app\Http\Requests\BrouillonFacture::class) }}

    <div class="row">
        <div class="col-lg-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Client locataire {{ $facture->client->code }}</h2>
                </div>
                <div class="box-body">
                    @if (!$facture->client->is_entreprise)
                        <i class="fa fa-user"></i> <a href="{{ route('admin.client.edit', $reservation->client) }}">{{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}</a> <br>
                        <i class="fa fa-envelope"></i> {{ $reservation->email }}<br>
                        <i class="fa fa-phone"></i> {{  $reservation->telephone }}<br>
                        <i class="fa fa-address-card"></i> {{ $reservation->adresse }} - {{ $reservation->cp }} - {{ $reservation->ville }} - {{ $reservation->pays?->nom }}
                    @else
                        <i class="fa fa-building"></i> <a href="{{ route('admin.client.edit', $facture->client) }}">{{ $facture->client->nom }}</a><br>
                        <i class="fa fa-envelope"></i> {{ $facture->client->email }}<br>
                        <i class="fa fa-phone"></i> {{ $facture->client->telephone }}<br>
                        <i class="fa fa-address-card"></i> {{ $facture->client->adresse }} {{ $facture->client->cp }} - {{ $facture->client->ville }} - {{ $facture->client->pays?->nom }}<br>
                        <i class="fa fa-clipboard-list"></i> Siren : {{ $facture->client->vat_numero }}<br>
                        <i class="fa fa-clipboard-list"></i> Numéro Tva : {{ $facture->client->vat_numero }}
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Facture</h2>
                    <div class="btn-toolbar">
                        <button class="btn btn-outline-secondary" type="submit" name="brouillon" value="1"><i class="fas fa-save"></i> Enregistrer un brouillon</button>&nbsp;
                        @if ($facture->exists)
                            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Créer et envoyer la facture</button>&nbsp;
                        @endif
                        @can('delete', $facture)
                            <a class="btn btn-outline-danger" href="{{ route('admin.facture.destroy', $facture) }}" data-toggle="tooltip" title="Supprimer">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="box-body">

                    <div class="form-row">
                        {{ Aire::date('echeance_at', "Date d'échéance*")->required()->groupAddClass('col-md-6') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">
                        Produits
                        @if ($facture->exists)
                            <span class="badge badge-light">Total @prix($facture->total)&nbsp;€</span>
                        @endif
                    </h2>
                    <div class="btn-toolbar">
                        <button class="btn btn-outline-secondary table-editable-add" data-target="prestation" id="prestation-add" type="button" data-toggle="tooltip" title="Ajouter">
                            <i class="fas fa-plus"></i>
                        </button>&nbsp;
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-wrapper">
                        <table class="table table-hover table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Nom</th>
                                <th scope="col">Quantité</th>
                                <th scope="col">Remise</th>
                                <th scope="col">Montant total TTC</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="prestation-lignes">
                                @php
                                    $i = 1;
                                @endphp
                                @if (!$facture->exists and !$reservation->factureLocation)
                                    @php
                                        $prestation_location = \Ipsum\Reservation\app\Models\Prestation\Prestation::where('type_id', \Ipsum\Reservation\app\Models\Prestation\Type::LOCATION_ID)->first();
                                        $description = 'Catégorie '.$reservation->categorie_nom . ' du '.$reservation->debut_at->format('d/m/Y').' au '.$reservation->fin_at->format('d/m/Y');
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $prestation_location->nom }}<br>
                                            <small>{{ $description }}</small>
                                        </td>
                                        <td>1</td>
                                        <td>
                                            @if ($reservation->remise)
                                                @prix($reservation->remise)&nbsp;€
                                            @endif
                                        </td>
                                        <td>@prix($reservation->montant_base)&nbsp;€</td>
                                        <td>
                                            <input type="hidden" name="produits[{{ $i }}][prestation_id]" value="{{ $prestation_location->id }}">
                                            <input type="hidden" name="produits[{{ $i }}][quantite]" value="1">
                                            <input type="hidden" name="produits[{{ $i }}][montant]" value="{{ $reservation->montant_base }}">
                                            <input type="hidden" name="produits[{{ $i }}][description]" value="{{ $description }}">
                                            <input type="hidden" name="produits[{{ $i }}][remise]" value="{{ $reservation->remise }}">
                                        </td>
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                    @foreach($reservation->prestations as $prestation)
                                        <tr>
                                            <td>{{ $prestation->nom }}</td>
                                            <td>{{ $prestation->quantite }}</td>
                                            <td></td>
                                            <td>@prix($prestation->tarif)&nbsp;€</td>
                                            <td>
                                                <input type="hidden" name="produits[{{ $i }}][prestation_id]" value="{{ $prestation->id }}">
                                                <input type="hidden" name="produits[{{ $i }}][quantite]" value="{{ $prestation->quantite }}">
                                                <input type="hidden" name="produits[{{ $i }}][montant]" value="{{ $prestation->tarif }}">
                                            </td>
                                        </tr>
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                @endif
                                @foreach($facture->produits as $produit)
                                    <tr>
                                        <td>
                                            <select class="form-control" name="produits[{{ $i }}][prestation_id]" required>
                                                <option value="">-- Produits --</option>
                                                @foreach($prestations as $prestation)
                                                    <option value="{{ $prestation->id }}" {{ $produit->id === $prestation->id  ? 'selected' : '' }}>{{ $prestation->nom }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="produits[{{ $i }}][description]" value="{{ $produit->pivot->description }}">
                                        </td>
                                        <td><input type="number" class="form-control" value="{{ $produit->pivot->quantite }}" name="produits[{{ $i }}][quantite]" required></td>
                                        <td><input type="number" class="form-control" step=".01" value="{{ $produit->pivot->remise }}" name="produits[{{ $i }}][remise]"></td>
                                        <td><input type="number" class="form-control" step=".01" value="{{ $produit->pivot->montant }}" name="produits[{{ $i }}][montant]" required></td>
                                        <td><button type="button" class="prestation-delete btn btn-outline-danger" data-confirm="false"><i class="fa fa-trash-alt"></i></button></td>
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                                <script id="prestation-add-template" type="x-tmpl-mustache">
                                    <tr>
                                        <td>
                                            <select class="form-control" name="produits[@{{ indice }}][prestation_id]" required>
                                                <option value="">-- Produits --</option>
                                                @foreach($prestations as $prestation)
                                                    <option value="{{ $prestation->id }}">{{ $prestation->nom }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" value="" name="produits[@{{ indice }}][quantite]" required></td>
                                        <td><input type="number" class="form-control" step=".01" value="" name="produits[@{{ indice }}][remise]"></td>
                                        <td><input type="number" class="form-control" step=".01" value="" name="produits[@{{ indice }}][montant]" required></td>
                                        <td><button type="button" class="prestation-delete btn btn-outline-danger" data-confirm="false"><i class="fa fa-trash-alt"></i></button></td>
                                    </tr>
                                </script>
                                @error('produits.*')
                                    <div class="alert alert-warning">{{ $message }}</div>
                                @enderror
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
                        @if ($facture->exists)
                            <x-reservation::reste_a_payer total="{{ $facture->total }}"  montant_paye="{{ $facture->montant_paye }}" />
                        @endif
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
                            @php
                                $i = 1;
                            @endphp
                            @foreach($reservation->paiements()->ok()->whereNot('paiement_type_id', '<=>', \Ipsum\Reservation\app\Models\Reservation\Type::CAUTION_ID)->with(['moyen', 'type'])->orderBy('created_at', 'desc')->get() as $paiement)
                                <tr>
                                    <td>{{ $paiement->id }}</td>
                                    <td>{{ $paiement->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $paiement->moyen->nom }}</td>
                                    <td>{{ $paiement->type->nom }}</td>
                                    <td>@prix($paiement->montant)&nbsp;€</td>
                                    <td>{!! nl2br(e($paiement->note )) !!}</td>
                                    <td>
                                        <input type="hidden" name="paiements[{{ $i }}][id]" value="{{ $paiement->id }}">
                                        <input type="hidden" name="paiements[{{ $i }}][reservation_id]" value="{{ $paiement->reservation_id }}">
                                        <input type="hidden" name="paiements[{{ $i }}][created_at]" value="{{ $paiement->created_at->format('Y-m-d\TH:i') }}">
                                        <input type="hidden" name="paiements[{{ $i }}][paiement_moyen_id]" value="{{ $paiement->paiement_moyen_id }}">
                                        <input type="hidden" name="paiements[{{ $i }}][paiement_type_id]" value="{{ $paiement->paiement_type_id }}">
                                        <input type="hidden" name="paiements[{{ $i }}][montant]" value="{{ $paiement->montant }}">
                                        <input type="hidden" name="paiements[{{ $i }}][note]" value="{{ $paiement->note }}">
                                    </td>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                            @foreach($facture->paiements()->doesntHave('reservation')->orderBy('created_at', 'desc')->get() as $paiement)
                                <tr>
                                    <td>{{ $paiement->id }}<input type="hidden" name="paiements[{{ $i }}][id]" value="{{ $paiement->id }}" /></td>
                                    <td><input type="datetime-local" class="form-control" name="paiements[{{ $i }}][created_at]" value="{{ $paiement->created_at->format('Y-m-d\TH:i') }}" required></td>
                                    <td>
                                        <div class="d-flex">
                                            <select class="form-control" name="paiements[{{ $i }}][paiement_moyen_id]" required>
                                                <option value="">-- Moyens --</option>
                                                @foreach($moyens as $moyen)
                                                    <option value="{{ $moyen->id }}" {{ $paiement->moyen?->id === $moyen->id  ? 'selected' : '' }}>{{ $moyen->nom }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control" name="paiements[{{ $i }}][paiement_type_id]" required>
                                            <option value="">-- Types --</option>
                                            @foreach($types as $type)
                                                <option value="{{ $type->id }}" {{ $paiement->type?->id === $type->id  ? 'selected' : '' }}>{{ $type->nom }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control" step=".01" value="{{ $paiement->montant }}" name="paiements[{{ $i }}][montant]" required></td>
                                    <td><textarea cols="30" rows="1" class="form-control" name="paiements[{{ $i }}][note]">{!! nl2br(e($paiement->note )) !!}</textarea></td>
                                    <td><button type="button" class="paiement-delete btn btn-outline-danger" data-confirm="false"><i class="fa fa-trash-alt"></i></button></td>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                            <script id="paiement-add-template" type="x-tmpl-mustache">
                                <tr>
                                    <td><input type="hidden" name="paiements[@{{ indice }}][id]" value="" /></td>
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
                            @error('paiements.*')
                                <div class="alert alert-warning">{{ $message }}</div>
                            @enderror
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if ($facture->exists)
            <div class="col-lg-6">
                <x-facture :facture="$facture" />
            </div>
        @endif
    </div>
    {{ Aire::close() }}
@endsection
