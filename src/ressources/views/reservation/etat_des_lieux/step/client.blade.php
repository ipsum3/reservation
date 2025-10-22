@extends('IpsumAdmin::layouts.app')
@section('title', 'Inspection')

@section('content')

    <h1 class="main-title">État des lieux - Inspection {{ $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'initiale': 'finale' }}</h1>

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    @include('IpsumReservation::reservation.etat_des_lieux._progressbar')

                    <!-- Progress bar -->
                    <ul class="progressbar mt-2 clearfix overflow-auto">
                        @if($type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
                            <li><a href="{{ route('admin.inspection.vehicule', [$reservation, $type]) }}">Véhicule</a></li>
                            <li class="active">Client / Réservation</li>
                        @endif
                        <li>Kilométrage / Carburant / Checklist</li>
                        <li>Dommages / Photos</li>
                        <li>Récapitulatif</li>
                        <li>Signature client</li>
                        <li>Signature agent</li>
                    </ul>
                </div>
                <div class="box-body">

                    {{ Aire::open()->id('reservation')->route('admin.inspection.vehicule.store', [$reservation, $type])->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionVehicule::class) }}

                    <!-- STEP 2 -->
                            @if($type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
                            <!-- STEP 3 -->
                            <div class="step active">
                                <div class="form-row">
                                    <div class="col-md-12">
                                        {{-- Informations de réservation --}}
                                        <div class="mb-6">
                                            <h2 class="text-xl font-semibold mb-2">Informations client</h2>
                                            <div class="form-row">
                                                {{ Aire::select(collect(['' => '---- Civilité -----', 'M.' => 'Monsieur', 'Mme' => 'Madame']), 'civilite', 'Civilité')->value($reservation->civilite)->disabled()->groupAddClass('col-md-2') }}
                                                {{ Aire::input('prenom', 'Prénom')->value($reservation->prenom)->disabled()->groupAddClass('col-md-5') }}
                                                {{ Aire::input('nom', 'Nom*')->value($reservation->nom)->disabled()->groupAddClass('col-md-5') }}
                                                {{ Aire::input('email', 'Email*')->value($reservation->email)->disabled()->groupAddClass('col-md-6') }}
                                                {{ Aire::input('telephone', 'Téléphone')->value($reservation->telephone)->disabled()->groupAddClass('col-md-6') }}
                                                {{ Aire::input('adresse', 'Adresse')->value($reservation->adresse)->disabled()->groupAddClass('col-md-6') }}
                                                {{ Aire::input('cp', 'Code postal')->value($reservation->cp)->disabled()->groupAddClass('col-md-6') }}
                                                {{ Aire::input('ville', 'Ville')->value($reservation->ville)->disabled()->groupAddClass('col-md-6') }}
                                                {{ Aire::select(collect(['' => '---- Pays -----'])->union($pays), 'pays_id', 'Pays')->value($reservation->pays_id)->disabled()->groupAddClass('col-md-6') }}
                                                {{ Aire::date('naissance_at', 'Date de naissance')->value($reservation->naissance_at)->disabled()->groupAddClass('col-md-6') }}
                                                {{ Aire::input('naissance_lieu', 'Lieu de naissance')->value($reservation->naissance_lieu)->disabled()->groupAddClass('col-md-6') }}
                                                {{ Aire::input('permis_numero', 'Numéro de permis')->value($reservation->permis_numero)->disabled()->groupAddClass('col-md-6') }}
                                                {{ Aire::date('permis_at', 'Permis délivré le')->value($reservation->permis_at)->disabled()->groupAddClass('col-md-6') }}
                                                {{ Aire::input('permis_delivre', 'Permis délivré par')->value($reservation->permis_delivre)->disabled()->groupAddClass('col-md-6') }}
                                            </div>

                                            @if (config('ipsum.reservation.conducteurs_additionnels') && $reservation->conducteurs->count() )
                                                <h2 class="text-xl font-semibold mb-2 mt-4">Conducteurs additionnels</h2>
                                                <table class="table table-hover table-striped"  style="min-width: 1000px">
                                                    <thead>
                                                    <tr>
                                                        <th> Nom </th>
                                                        <th> Prénom </th>
                                                        <th> Date de naissance </th>
                                                        <th> Lieu de naissance </th>
                                                        <th> Numéro de permis </th>
                                                        <th> Permis délivré le </th>
                                                        <th> Permis délivré par </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="conducteurs-lignes">
                                                    @php
                                                        $i = 1;
                                                    @endphp
                                                    @foreach($reservation->conducteurs as $conducteur)
                                                        <tr>
                                                            <td><input class="form-control" type="text" disabled name="conducteurs[{{ $i }}][nom]" value="{{ old('nom', $conducteur->nom) }}" /></td>
                                                            <td><input class="form-control" type="text" disabled name="conducteurs[{{ $i }}][prenom]" value="{{ old('prenom', $conducteur->prenom) }}" /></td>
                                                            <td><input class="form-control" type="date" disabled name="conducteurs[{{ $i }}][naissance_at]" value="{{ old('naissance_at', $conducteur->naissance_at->format('Y-m-d')) }}" /></td>
                                                            <td><input class="form-control" type="text" disabled name="conducteurs[{{ $i }}][naissance_lieu]" value="{{ old('naissance_lieu', $conducteur->naissance_lieu) }}" /></td>
                                                            <td><input class="form-control" type="text" disabled name="conducteurs[{{ $i }}][permis_numero]" value="{{ old('permis_numero', $conducteur->permis_numero) }}" /></td>
                                                            <td><input class="form-control" type="date" disabled name="conducteurs[{{ $i }}][permis_at]" value="{{ old('permis_at', $conducteur->permis_at->format('Y-m-d')) }}" /></td>
                                                            <td><input class="form-control" type="text" disabled name="conducteurs[{{ $i }}][permis_delivre]" value="{{ old('permis_delivre', $conducteur->permis_delivre) }}" /></td>
                                                        </tr>

                                                        @php
                                                            $i++;
                                                        @endphp
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif

                                            <h2 class="text-xl font-semibold mb-2 mt-4">Informations réservation</h2>
                                            <div class="form-row">
                                                {{ Aire::dateTimeLocal('debut_at', 'Date départ*')->value($reservation->debut_at)->disabled()->id('debut_at')->required()->defaultValue(\Carbon\Carbon::now()->format('Y-m-d H:00:00'))->groupAddClass('col-md-6') }}
                                                {{ Aire::dateTimeLocal('fin_at', 'Date retour*')->value($reservation->fin_at)->disabled()->id('fin_at')->required()->defaultValue(\Carbon\Carbon::now()->format('Y-m-d H:00:00'))->groupAddClass('col-md-6') }}
                                                {{ Aire::select(collect(['' => '---- Lieux -----'])->union($lieux), 'debut_lieu_id', 'Lieu départ*')->value($reservation->debut_lieu_id)->disabled()->required()->groupAddClass('col-md-6') }}
                                                {{ Aire::select(collect(['' => '---- Lieux -----'])->union($lieux), 'fin_lieu_id', 'Lieu retour*')->value($reservation->fin_lieu_id)->disabled()->required()->groupAddClass('col-md-6') }}
                                                {{ Aire::textArea('observation', 'Observation client')->value($reservation->observation)->disabled()->groupAddClass('col-md-6') }}
                                            </div>
                                            <a href="{{ route('admin.reservation.edit', [$reservation]) }}#vehicule-select" class="btn btn-primary"><i class="fa fa-edit"></i> Editer les informations</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @endif

                            <!-- Navigation -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('admin.inspection.vehicule', [$reservation, $type]) }}" id="prevBtn" class="btn btn-secondary">Retour</a>
                                <a href="{{ route('admin.inspection.checklist', [$reservation, $type]) }}" id="nextBtn" class="btn btn-primary">Suivant</a>
                            </div>

                        {{ Aire::close() }}
                </div>
            </div>

        </div>

    </div>

@endsection