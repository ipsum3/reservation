@extends('IpsumAdmin::layouts.app')
@section('title', 'État des lieux')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux.step._progressbar')

    <h1 class="main-title">État des lieux {{ strtolower($inspection->type->nom) }} <a href="{{ route('admin.reservation.edit', $inspection->reservation) }}"><small class="text-muted">(résa. {{ $inspection->reservation->reference }})</small></a></h1>

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Dommages</h2>
                    <div class="btn-toolbar">
                        <a href="{{ route('admin.inspection.dommage.create', [$inspection]) }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> <i class="fas fa-plus"></i> Ajouter un dommage
                        </a>
                    </div>
                </div>
                <div class="box-body">

                    {{-- Dommages précédents (Inspection initiale) --}}
                    <div class="mb-5">
                        @php
                            $dommages_initial = $inspection->vehicule->dommages->filter(function ($dommage) use ($inspection) {
                                return !$dommage->inspection_id or $dommage->inspection_id !== $inspection->reservation->inspectionFinale?->id;
                            })->loadMissing('emplacement', 'type', 'element', 'illustration')->sortBy(function ($dommage) { return $dommage->emplacement->order; });
                        @endphp

                        @if (!$inspection->type->is_initial)
                            <h3>Dommages de l’état des lieux initial</h3>
                        @endif

                        @if($dommages_initial->count())
                            <div class="row">
                                @foreach($dommages_initial as $dommage)
                                    <div class="d-flex flex-row flex-wrap justify-content-center">
                                        @include('IpsumReservation::reservation.etat_des_lieux.step._dommage', ['protected' => !$inspection->type->is_initial])
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="alert alert-info">
                                @if ($inspection->type->is_initial)
                                    Aucun dommage enregistré.
                                @else
                                    Aucun dommage enregistré lors de l’état des lieux initial.
                                @endif
                            </p>
                        @endif
                    </div>


                    @if(!$inspection->type->is_initial)
                        {{-- Dommages de l’inspection finale --}}
                        @php
                            $dommages_new = $inspection->vehicule->dommages->filter(function ($dommage) use ($inspection) {
                                return $inspection->type->is_initial or $dommage->inspection_id === $inspection->reservation->inspectionFinale?->id;
                            })->loadMissing('emplacement', 'type', 'element', 'illustration')->sortBy(function ($dommage) { return $dommage->emplacement->order; });
                        @endphp

                        <h3>Nouveaux dommages</h3>

                        @if($dommages_new->count())<div class="<!--col-sm-6 col-lg-4 col-xl-3-->"></div>
                            <div class="d-flex flex-row flex-wrap justify-content-center">
                                @foreach($dommages_new as $dommage)
                                    @include('IpsumReservation::reservation.etat_des_lieux.step._dommage')
                                @endforeach
                            </div>
                        @else
                            <p class="alert alert-info">Aucun nouveau dommage enregistré.</p>
                        @endif
                    @endif

                </div>

                <div class="box-footer">
                    <div><a href="{{ route('admin.inspection.checklist', [$inspection]) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div><a href="{{ route('admin.inspection.photo', [$inspection]) }}" id="nextBtn" class="btn btn-primary">Suivant</a></div>
                </div>

            </div>

        </div>

    </div>

@endsection