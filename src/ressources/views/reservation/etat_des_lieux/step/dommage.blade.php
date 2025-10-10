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

                        {{-- Dommages précédents (Inspection initiale) --}}
                        @if($inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale)
                            <h2 class="h4 mb-3">Dommage(s) de l’inspection initiale</h2>

                            @if($reservation->inspection_initiale?->dommages && $reservation->inspection_initiale->dommages->count())
                                <div class="d-flex flex-row flex-wrap">
                                    @foreach($reservation->inspection_initiale->dommages as $dommage)
                                        @include('IpsumReservation::reservation.etat_des_lieux.step._dommage')
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Aucun dommage enregistré lors de l’inspection initiale.</p>
                            @endif

                            <hr class="my-4">
                        @endif

                        {{-- Dommages de l’inspection actuelle --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h4 mb-0">Dommage(s) de l’inspection actuelle</h2>
                            <a href="{{ route('admin.inspection.dommage.create', [$reservation, $type]) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-plus-circle"></i> <i class="fas fa-plus"></i> Ajouter un dommage
                            </a>
                        </div>

                        <div class="d-flex flex-row flex-wrap">
                            @if($reservation->vehicule?->dommages && $inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
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
                        </div>
                        @else
                            <p class="text-muted">Aucun dommage enregistré pour cette inspection.</p>
                        @endif


                        @if($inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale)
                            @php
                                $photos = $reservation->inspection_initiale->medias()->groupe('photos')->get();
                            @endphp
                            @if($photos->count())
                                <h2 class="text-xl font-semibold mb-2 mb-2">Médias de l'inspection initiale</h2>
                                <div class="d-flex flex-row flex-wrap sortable upload-files">
                                    @foreach($photos as $media)
                                        <div class="media sortable-item" data-sortable="{{ $media->id }}">
                                            <div class="media-img">
                                                @if ($media->isImage)
                                                    <img src="{{ Croppa::url($media->cropPath, 200, 200) }}" alt="{{ $media->tagAlt }}" />
                                                @else
                                                    <span class="media-icone {{ $media->icone }}"></span>
                                                @endif
                                            </div>
                                            <div class="media-title">
                                                {{ $media->titre }}
                                            </div>
                                            <div class="media-toolbar">
                                                <ul>
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <h2 class="h4 mb-3">Médias de l'inspection finale</h2>
                            @endif
                        @endif
                        <h3 class="text-xl font-semibold mt-4 mb-4">Ajout rapide de photo</h3>
                        <div class="upload"
                             data-uploadendpoint="{{ route('admin.media.store') }}"
                             data-uploadmedias="{{ route('admin.media.publication', ['publication_type' => \Ipsum\Reservation\app\Models\Inspection\Inspection::class, 'publication_id' => $inspection->exists ? $inspection->id : '', "groupe" => "photos"]) }}"
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
                        </div>

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