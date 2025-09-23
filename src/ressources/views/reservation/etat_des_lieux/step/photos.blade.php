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
                            <li>Véhicule</li>
                            <li>Client / Réservation</li>
                        @endif
                        <li>Kilométrage / Carburant / Checklist</li>
                        <li>Dommages</li>
                        <li class="active">Photos</li>
                        <li>Récapitulatif</li>
                        <li>Signature client</li>
                        <li>Signature agent</li>
                    </ul>
                </div>
                <div class="box-body">

                    <!-- STEP 6 -->
                    <div class="step active">
                        <div class="box-body">
                            @if($inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale)
                                @php
                                    $photos = $reservation->inspection_initiale->medias()->groupe('photos')->get();
                                @endphp
                                @if($photos->count())
                                    <h3 class="text-xl font-semibold mb-2 mb-2">Médias de l'inspection initiale</h3>
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

                                    <h3 class="text-xl font-semibold mt-4 mb-4">Médias de l'inspection finale</h3>
                                @endif
                            @endif

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
                    </div>

                            <!-- Navigation -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ URL::previous() }}" id="prevBtn" class="btn btn-secondary">Retour</a>
                                <a href="{{ route('admin.inspection.recapitulatif', [$reservation, $type]) }}" id="nextBtn" class="btn btn-primary">Suivant</a>
                            </div>

                </div>
            </div>

        </div>

    </div>

@endsection