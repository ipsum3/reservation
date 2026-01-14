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
                            <li><a href="{{ route('admin.inspection.client', [$reservation, $type]) }}">Client / Réservation</a></li>
                        @endif
                        <li><a href="{{ route('admin.inspection.checklist', [$reservation, $type]) }}">Kilométrage / Carburant / Checklist</a></li>
                        <li class="active">Dommages / Photos</li>
                        <li>Récapitulatif</li>
                        <li>Signature client</li>
                        <li>Signature agent</li>
                    </ul>
                </div>
                <div class="box-body">


                    <!-- STEP 5 -->
                    <div class="step active">

                        {{-- Dommages de l’inspection actuelle --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="mb-0">Dommage(s) de l’inspection</h2>
                            <a href="{{ route('admin.inspection.dommage.create', [$reservation, $type]) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-plus-circle"></i> <i class="fas fa-plus"></i> Ajouter un dommage
                            </a>
                        </div>

                        <div class="d-flex flex-row flex-wrap">
                            @if($reservation->vehicule?->dommages->count() || $inspection->dommages->count())
                                @if($reservation->vehicule?->dommages->count() && $inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
                                    @foreach($reservation->vehicule?->dommages as $dommage)
                                        @if($dommage->inspection->id != $inspection->id && $dommage->inspection->id != $reservation->inspection_initiale->id)
                                            @include('IpsumReservation::reservation.etat_des_lieux.step._dommage')
                                        @endif
                                    @endforeach
                                @endif

                                @if($inspection->dommages && $inspection->dommages->count())
                                    @foreach($inspection->dommages as $dommage)
                                        @include('IpsumReservation::reservation.etat_des_lieux.step._dommage')
                                    @endforeach
                                @endif
                            @else
                                <p class="alert alert-info">Aucun dommage enregistré pour cette inspection.</p>
                            @endif

                        </div>

                        {{-- Dommages précédents (Inspection initiale) --}}
                        @if($inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale)
                            <div id="accordion">
                                <h5 class="mb-0">
                                    <button class="btn btn-link text-warning" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        Voir les {{--photos et--}} dommages de l’inspection initiale
                                    </button>
                                </h5>

                                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                    <div class="card-body alert-warning">
                                        {{--@php
                                            $photos = $reservation->inspection_initiale->medias()->groupe('photos')->get();
                                        @endphp--}}

                                        @if($reservation->vehicule?->dommages->count() || $photos->count())
                                            <div class="d-flex flex-row flex-wrap">
                                                @foreach($reservation->vehicule->dommages as $dommage)
                                                    @php
                                                        $dommage->protected = true;
                                                    @endphp
                                                    @if($dommage->inspection->id != $inspection->id)
                                                        @include('IpsumReservation::reservation.etat_des_lieux.step._dommage')
                                                    @endif
                                                @endforeach

                                                {{--@foreach($photos as $media)
                                                        @php
                                                            $media->protected = true;
                                                        @endphp
                                                        @include('IpsumReservation::reservation.etat_des_lieux.step._photo')
                                                @endforeach--}}
                                            </div>
                                        @else
                                            <p class="alert alert-info">Aucun dommage enregistré lors de l’inspection initiale.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{--<hr class="my-4">

                        <h2 class="mt-1 mb-4">Ajout rapide de photo</h2>
                        <div class="upload"
                             data-uploadendpoint="{{ route('admin.media.store') }}"
                             data-uploadmedias="{{ route('admin.media.publication', ['toolbar' => ['editable' => false, 'sortable' => false, 'title' => false, 'link' => true, 'pad' => true], 'publication_type' => \Ipsum\Reservation\app\Models\Inspection\Inspection::class, 'publication_id' => $inspection->exists ? $inspection->id : '', "groupe" => "photos"]) }}"
                             data-uploadrepertoire="inspection"
                             data-uploadpublicationid="{{ $inspection->id }}"
                             data-uploadpublicationtype="{{ \Ipsum\Reservation\app\Models\Inspection\Inspection::class }}"
                             data-uploadgroupe="photos"
                             data-uploadnote="Images et documents, poids maximum {{ config('ipsum.media.upload_max_filesize') }} Ko"
                             data-uploadmaxfilesize="{{ config('ipsum.media.upload_max_filesize') }}"
                             data-uploadmmaxnumberoffiles=""
                             data-uploadminnumberoffiles=""
                             data-uploadallowedfiletypes=""
                             data-uploadcsrftoken="{{ csrf_token() }}">
                            <div class="upload-DragDrop"></div>
                            <div class="upload-ProgressBar"></div>
                            <div class="upload-alerts mt-3"></div>
                            <div class="mt-3">
                                <h3>Médias associés :</h3>
                                <div class="d-flex flex-row flex-wrap sortable upload-files" data-sortableurl="{{ route('admin.media.changeOrder') }}" data-sortablecsrftoken="{{ csrf_token() }}">
                                </div>
                            </div>
                        </div>--}}

                    </div>

                    <!-- Navigation -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.inspection.checklist', [$reservation, $type]) }}" id="prevBtn" class="btn btn-secondary">Retour</a>
                        <a href="{{ route('admin.inspection.recapitulatif', [$reservation, $type]) }}" id="nextBtn" class="btn btn-primary">Suivant</a>
                    </div>

                </div>
            </div>

        </div>

    </div>


    <link href="{{ asset('ipsum/admin/dist/uppy.css') }}" rel="stylesheet">
    <script src="{{ asset('ipsum/admin/dist/uppy.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function initUploads() {
                document.querySelectorAll('.upload').forEach(function (el) {
                    console.log(el, el.dataset.initialize)
                    if (el.dataset.initialize === "false") {
                        window.uppyInit(el);
                        el.dataset.initialize = "true";
                    }
                });
            }

            // Init au chargement
            //initUploads();

            document.getElementById('dommages-add').addEventListener('click', function () {
                setTimeout(initUploads, 100);
            });
        });
    </script>

@endsection