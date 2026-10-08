@extends('IpsumAdmin::layouts.app')
@section('title', 'Facture')

@section('content')

    <h1 class="main-title">Facture {{ $facture->numero }} <a href="{{ route('admin.reservation.edit', $reservation) }}"><small class="text-muted">(résa. {{ $reservation->reference }})</small></a></h1>

    {{ Aire::open()->route('admin.facture.update', [$facture])->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreFacture::class) }}

    <div class="row">
        <div class="col-lg-6">
            <iframe src="{{ $url_pdf }}" style="width:100%; height: 700px" frameborder="0"></iframe>
        </div>

        <div class="col-lg-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Facture</h2>
                    <div class="btn-toolbar">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                        <a class="btn btn-secondary" href="{{ route('admin.reservation.reservationDocumentSend', [$reservation, 'facture', 'id' => $facture->id, 'objet' => 'Facture '.$facture->numero]) }}"><i class="fas fa-envelope"></i> Envoyer</a>
                    </div>
                </div>
                <div class="box-body">
                    @if (!$facture->client->is_entreprise)
                        <i class="fa fa-user"></i> <a href="{{ route('admin.client.edit', $reservation->client) }}">{{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}</a> <br>
                    @else
                        <i class="fa fa-building"></i> <a href="{{ route('admin.client.edit', $facture->client) }}">{{ $facture->client->nom }}</a>
                    @endif
                    @if ($facture->send_at)
                        <i class="fa fa-envelope"></i> Envoyé le {{ $facture->send_at->format('d/m/Y') }}<br>
                    @endif
                </div>
            </div>

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">
                        Réglements
                        <x-reservation::reste_a_payer total="{{ $facture->total }}"  montant_paye="{{ $facture->montant_paye }}" />
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
    </div>
    {{ Aire::close() }}
@endsection
