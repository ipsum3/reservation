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

                    <div class="row justify-content-center">

                        <div class="col-md-4 mt-3 mb-5">

                                <div id="upload-DragDrop"></div>

                        </div>
                    </div>
                    <div class="row">

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



    <link href="https://releases.transloadit.com/uppy/v5.2.1/uppy.min.css" rel="stylesheet">
    {{--<script src="https://releases.transloadit.com/uppy/locales/v5.1.0/fr_FR.min.js"></script>--}}
    <script type="module">
        var medias = [];

        import { Uppy, Dashboard, Webcam, XHRUpload  } from "https://releases.transloadit.com/uppy/v5.2.1/uppy.min.mjs"

        const uppy = new Uppy({
            debug: false,
            autoProceed: true,
            //locale: Uppy.locales.fr_FR, // todo locales fr_FR
            restrictions: {
                maxFileSize: 10000000,
                maxNumberOfFiles: 1,
                allowedFileTypes: ['image/*']
            },
            meta: {
                publication_id: '{{ $dommage->id ?? '' }}',
                publication_type: "Ipsum\\Reservation\\app\\Models\\Dommage\\Dommage",
                //repertoire: 'dommages',
                _token: '{{ csrf_token() }}'
            }})

        uppy.use(
            Dashboard, {
                inline: true,
                target: '#upload-DragDrop',
                replaceTargetContent: true,
                showLinkToFileUploadResult: false,
                showProgressDetails: true,
                showRemoveButtonAfterComplete: true,
                note: 'Uniquement des photos, maximum un fichier de 1 MB.',
                height: 400,
                width: "100%",
                browserBackButtonClose: true,
                proudlyDisplayPoweredByUppy: false
            });

        uppy.use(
            Webcam, {
                countdown: false,
                target: Uppy.Dashboard,
                modes: [
                    'picture'
                ],
                mirror: true,
                showRecordingLength: false,
                preferredVideoMimeType: null,
                preferredImageMimeType: null,
                locale: {},
                videoConstraints: {
                    facingMode: 'environment',
                    width: { min: 1280, ideal: 1280, max: 1920 },
                    height: { min: 720, ideal: 720, max: 1080 },
                },
            });

        uppy.use(XHRUpload, {
            endpoint: '{{ route('admin.media.store') }}',
            formData: true,
            fieldName: 'media',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        uppy.on('upload-success', function (file, response) {
            medias.push(response.body.media);
        });

        uppy.on('upload-error', function (file, error, response) {
            console.log('error with file:', file.id);
            console.log('error message:', error);
            console.log('error message:', response);
        });
        uppy.on('file-removed', function (file) {
            console.log('file removed', file)
            medias.forEach(function(media, index, medias){
                console.log( media)
                if (media.nom === file.name) {
                    medias.splice(index, 1);

                    $.ajax({
                        url: "/signalement/media/" + media.tmp_file + "/destroy" // TODO A FAIRE
                    });

                    return false;
                }
            });
        });
        uppy.on('restriction-failed', function (file, error) {
            /*document.querySelector('#upload-alerts').insertAdjacentHTML('beforeend', '<div class="alert alert-warning">' + file.name +' : ' + error +'</div>')*/
        });
    </script>

@endsection