@extends('IpsumAdmin::layouts.app')
@section('title', 'État des lieux')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux.step._progressbar')

    <h1 class="main-title">État des lieux {{ strtolower($inspection->type->nom) }} <a href="{{ route('admin.reservation.edit', $inspection->reservation) }}"><small class="text-muted">(résa. {{ $inspection->reservation->reference }})</small></a></h1>

    {{ Aire::open()->id('reservation')->route('admin.inspection.vehicule.store', [$inspection]) }}

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Réservation</h2>
                    <div class="btn-toolbar">
                        <a href="{{ route('admin.reservation.edit', [$inspection->reservation]) }}#vehicule-select" class="btn btn-outline-primary"><i class="fa fa-edit"></i> Modifier les informations</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-row">
                        <div class="col-md-12">
                            {{-- Informations de réservation --}}
                            <div class="mb-6">
                                <h3>Informations client</h3>
                                <div class="form-row">
                                    {{ Aire::select(collect(['' => '---- Civilité -----', 'M.' => 'Monsieur', 'Mme' => 'Madame']), 'civilite', 'Civilité')->value($inspection->reservation->civilite)->disabled()->groupAddClass('col-md-2') }}
                                    {{ Aire::input('prenom', 'Prénom')->value($inspection->reservation->prenom)->disabled()->groupAddClass('col-md-5') }}
                                    {{ Aire::input('nom', 'Nom*')->value($inspection->reservation->nom)->disabled()->groupAddClass('col-md-5') }}
                                    {{ Aire::input('email', 'Email*')->value($inspection->reservation->email)->disabled()->groupAddClass('col-md-6') }}
                                    {{ Aire::input('telephone', 'Téléphone')->value($inspection->reservation->telephone)->disabled()->groupAddClass('col-md-6') }}
                                    {{ Aire::input('adresse', 'Adresse')->value($inspection->reservation->adresse)->disabled()->groupAddClass('col-md-6') }}
                                    {{ Aire::input('cp', 'Code postal')->value($inspection->reservation->cp)->disabled()->groupAddClass('col-md-6') }}
                                    {{ Aire::input('ville', 'Ville')->value($inspection->reservation->ville)->disabled()->groupAddClass('col-md-6') }}
                                    {{ Aire::select(collect(['' => '---- Pays -----'])->union($pays), 'pays_id', 'Pays')->value($inspection->reservation->pays_id)->disabled()->groupAddClass('col-md-6') }}
                                    {{ Aire::date('naissance_at', 'Date de naissance')->value($inspection->reservation->naissance_at)->disabled()->groupAddClass('col-md-6') }}
                                    {{ Aire::input('naissance_lieu', 'Lieu de naissance')->value($inspection->reservation->naissance_lieu)->disabled()->groupAddClass('col-md-6') }}
                                    {{ Aire::input('permis_numero', 'Numéro de permis')->value($inspection->reservation->permis_numero)->disabled()->groupAddClass('col-md-6') }}
                                    {{ Aire::date('permis_at', 'Permis délivré le')->value($inspection->reservation->permis_at)->disabled()->groupAddClass('col-md-6') }}
                                    {{ Aire::input('permis_delivre', 'Permis délivré par')->value($inspection->reservation->permis_delivre)->disabled()->groupAddClass('col-md-6') }}
                                </div>

                                @if (config('ipsum.reservation.conducteurs_additionnels') && $inspection->reservation->conducteurs->count() )
                                    <h3 class="mt-4">Conducteurs additionnels</h3>
                                    <table class="table table-hover table-striped" style="min-width: 1000px">
                                        <thead>
                                        <tr>
                                            <th> Nom</th>
                                            <th> Prénom</th>
                                            <th> Date de naissance</th>
                                            <th> Lieu de naissance</th>
                                            <th> Numéro de permis</th>
                                            <th> Permis délivré le</th>
                                            <th> Permis délivré par</th>
                                        </tr>
                                        </thead>
                                        <tbody id="conducteurs-lignes">
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach($inspection->reservation->conducteurs as $conducteur)
                                            <tr>
                                                <td><input class="form-control" type="text" disabled name="conducteurs[{{ $i }}][nom]" value="{{ old('nom', $conducteur->nom) }}"/></td>
                                                <td><input class="form-control" type="text" disabled name="conducteurs[{{ $i }}][prenom]" value="{{ old('prenom', $conducteur->prenom) }}"/></td>
                                                <td><input class="form-control" type="date" disabled name="conducteurs[{{ $i }}][naissance_at]" value="{{ old('naissance_at', $conducteur->naissance_at?->format('Y-m-d')) }}"/></td>
                                                <td><input class="form-control" type="text" disabled name="conducteurs[{{ $i }}][naissance_lieu]" value="{{ old('naissance_lieu', $conducteur->naissance_lieu) }}"/></td>
                                                <td><input class="form-control" type="text" disabled name="conducteurs[{{ $i }}][permis_numero]" value="{{ old('permis_numero', $conducteur->permis_numero) }}"/></td>
                                                <td><input class="form-control" type="date" disabled name="conducteurs[{{ $i }}][permis_at]" value="{{ old('permis_at', $conducteur->permis_at?->format('Y-m-d')) }}"/></td>
                                                <td><input class="form-control" type="text" disabled name="conducteurs[{{ $i }}][permis_delivre]" value="{{ old('permis_delivre', $conducteur->permis_delivre) }}"/></td>
                                            </tr>

                                            @php
                                                $i++;
                                            @endphp
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif

                                <h3 class="mt-4">Informations réservation</h3>
                                <div class="form-row">
                                    {{ Aire::dateTimeLocal('debut_at', 'Date départ*')->value($inspection->reservation->debut_at)->disabled()->id('debut_at')->required()->defaultValue(\Carbon\Carbon::now()->format('Y-m-d H:00:00'))->groupAddClass('col-md-6') }}
                                    {{ Aire::dateTimeLocal('fin_at', 'Date retour*')->value($inspection->reservation->fin_at)->disabled()->id('fin_at')->required()->defaultValue(\Carbon\Carbon::now()->format('Y-m-d H:00:00'))->groupAddClass('col-md-6') }}
                                    {{ Aire::select(collect(['' => '---- Lieux -----'])->union($lieux), 'debut_lieu_id', 'Lieu départ*')->value($inspection->reservation->debut_lieu_id)->disabled()->required()->groupAddClass('col-md-6') }}
                                    {{ Aire::select(collect(['' => '---- Lieux -----'])->union($lieux), 'fin_lieu_id', 'Lieu retour*')->value($inspection->reservation->fin_lieu_id)->disabled()->required()->groupAddClass('col-md-6') }}
                                    {{ Aire::textArea('observation', 'Observation client')->value($inspection->reservation->observation)->disabled()->groupAddClass('col-md-6') }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="box-footer">
                    <div><a href="{{ route('admin.inspection.vehicule', [$inspection]) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div><a href="{{ route('admin.inspection.checklist', [$inspection]) }}" id="nextBtn" class="btn btn-primary">Suivant</a></div>
                </div>
            </div>

        </div>

    </div>
    {{ Aire::close() }}

@endsection