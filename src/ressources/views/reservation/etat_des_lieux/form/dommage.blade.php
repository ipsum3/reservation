@extends('IpsumAdmin::layouts.app')
@section('title', 'Inspection')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux._progressbar')

    <h1 class="main-title">État des lieux - Inspection {{ $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'initiale': 'finale' }}</h1>

    {{ Aire::open()->id('reservation')->route($dommage->exists ? 'admin.inspection.dommage.update' : 'admin.inspection.dommage.store', $dommage->exists ? [$reservation, $type, $dommage] : [$reservation, $type])->bind($dommage)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionDommage::class) }}

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">{{ $dommage->exists ? 'Modifier' : 'Ajouter' }} un dommage</h2>
                    <div></div>

                    {{--@include('IpsumReservation::reservation.etat_des_lieux._progressbar')

                    <!-- Progress bar -->
                    <ul class="progressbar mt-2 clearfix overflow-auto">
                        @if($type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
                            <li><a href="{{ route('admin.inspection.vehicule', [$reservation, $type]) }}">Véhicule</a></li>
                            <li><a href="{{ route('admin.inspection.client', [$reservation, $type]) }}">Client / Réservation</a></li>
                        @endif
                        <li><a href="{{ route('admin.inspection.checklist', [$reservation, $type]) }}">Kilométrage / Carburant / Checklist</a></li>
                        <li class="active">Dommages</li>
                        <li>Photos</li>
                        <li>Récapitulatif</li>
                        <li>Signature client</li>
                        <li>Signature agent</li>
                    </ul>--}}
                </div>
                <div class="box-body">

                    <div class="row">

                        <div class="col-md-12 mt-3">
                            <div class="upload"
                                 data-initialize="true"
                                 data-uploadendpoint="{{ route('admin.media.store') }}"
                                 data-uploadmedias="{{ route('admin.media.publication', ['toolbar' => ['editable' => false, 'sortable' => false, 'title' => false, 'link' => true, 'pad' => true], 'publication_type' => \Ipsum\Reservation\app\Models\Dommage\Dommage::class, 'publication_id' => $dommage->exists ? $dommage->id : ''  ]) }}"
                                 data-uploadrepertoire=""
                                 data-uploadpublicationid="{{ $dommage->id ?? '' }}"
                                 data-uploadpublicationtype="{{ \Ipsum\Reservation\app\Models\Dommage\Dommage::class }}"
                                 data-uploadgroupe=""
                                 data-uploadnote="Une seule image (max {{ config('ipsum.media.upload_max_filesize') }} Ko)"
                                 data-uploadmaxfilesize="{{ config('ipsum.media.upload_max_filesize') }}"
                                 data-uploadmmaxnumberoffiles="1"
                                 data-uploadminnumberoffiles="0"
                                 data-uploadallowedfiletypes="image/*"
                                 data-uploadcsrftoken="{{ csrf_token() }}">
                                <div class="upload-DragDrop {{ $dommage->illustration ? 'd-none' : '' }}"></div>
                                <div class="upload-ProgressBar"></div>
                                <div class="upload-alerts mt-3"></div>
                                <div class="mt-3 mb-3">
                                    <div class="d-flex flex-row flex-wrap sortable upload-files"
                                         data-sortableurl="{{ route('admin.media.changeOrder') }}"
                                         data-sortablecsrftoken="{{ csrf_token() }}">
                                    </div>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const uploadFiles = document.querySelector('.upload-files');
                                        const uploadZone = document.querySelector('.upload-DragDrop');

                                        if (!uploadFiles || !uploadZone) return;

                                        function updateUploadZoneVisibility() {
                                            const hasMedia = uploadFiles.querySelector('.media') !== null;

                                            console.log(hasMedia)
                                            if (hasMedia) {
                                                uploadZone.classList.add('d-none');
                                            } else {
                                                uploadZone.classList.remove('d-none');
                                            }
                                        }

                                        function initObserver() {
                                            // Lancer l’observation des changements
                                            const observer = new MutationObserver(updateUploadZoneVisibility);
                                            observer.observe(uploadFiles, { childList: true, subtree: false });

                                            // Vérifier une première fois
                                            updateUploadZoneVisibility();
                                        }

                                        // Délai avant initialisation pour laisser Uppy injecter le DOM
                                        setTimeout(initObserver, 1500); // Ajuste le délai (ms) selon le temps de rendu d’Uppy
                                    });
                                </script>
                            </div>
                        </div>

                        <div class="col-md-4">
                            {{ Aire::select(collect(['' => '---- Types -----'])->union($dommage_types->pluck('nom','id')), 'type_id', 'Type de dommage*')->required() }}
                        </div>

                        <div class="col-md-4">
                            {{ Aire::select(collect(['' => '---- Emplacements -----'])->union($dommage_emplacements->pluck('nom','id')), 'emplacement_id', 'Emplacement*')->required() }}
                        </div>

                        <div class="col-md-4">
                            {{ Aire::select(collect(['' => '---- Éléments -----'])->union($dommage_elements->pluck('nom','id')), 'element_id', 'Élément*')->required() }}
                        </div>

                        <div class="col-md-12 mt-3">
                            {{ Aire::textArea('observations', 'Observation(s)')->rows(5) }}
                        </div>


                    </div>
                </div>

                <div class="box-footer">
                    <div><a href="{{ route('admin.inspection.dommage', [$reservation, $type]) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div><button type="submit" id="nextBtn" class="btn btn-primary">Suivant</button></div>
                </div>
            </div>

        </div>

    </div>

    {{ Aire::close() }}


    <link href="{{ asset('ipsum/admin/dist/uppy.css') }}" rel="stylesheet">
    <script src="{{ asset('ipsum/admin/dist/uppy.js') }}"></script>

@endsection