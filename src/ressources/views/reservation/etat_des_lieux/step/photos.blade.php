@extends('IpsumAdmin::layouts.app')
@section('title', 'État des lieux')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux.step._progressbar')

    <h1 class="main-title">État des lieux {{ strtolower($type->nom) }} <a href="{{ route('admin.reservation.edit', $reservation) }}"><small class="text-muted">(résa. {{ $reservation->reference }})</small></a></h1>

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Photos rapides</h2>
                </div>
                <div class="box-body">

                    <div class="row justify-content-center">

                        <div class="col-md-8 mt-3 mb-5">

                            <div id="upload-DragDrop"></div>

                        </div>
                    </div>


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

                <div class="box-footer">
                    <div><a href="{{ route('admin.inspection.dommages', [$reservation, $type]) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div><a href="{{ route('admin.inspection.signature.locataire', [$reservation, $type]) }}" id="nextBtn" class="btn btn-primary">Suivant</a></div>
                </div>

            </div>

        </div>

    </div>

    <link href="https://releases.transloadit.com/uppy/v5.2.1/uppy.min.css" rel="stylesheet">
    <style>
        .uppy-StatusBar-actionBtn--done, .uppy-StatusBar-actionBtn {
            display: none;
        }
    </style>
    <script type="module">
        import {Uppy, Dashboard, Webcam, XHRUpload} from "https://releases.transloadit.com/uppy/v5.2.1/uppy.min.mjs"

        import fr_FR from 'https://cdn.jsdelivr.net/npm/@uppy/locales@3.4.0/lib/fr_FR.js'

        const uppy = new Uppy({
            debug: true,
            autoProceed: true,
            locale: fr_FR,
            restrictions: {
                maxFileSize: 2000000,
                maxNumberOfFiles: 5,
                allowedFileTypes: ['image/*']
            },
            meta: {
                publication_id: '{{ $inspection->id }}',
                publication_type: "Ipsum\\Reservation\\app\\Models\\Inspection\\Inspection",
                groupe: 'photos',
                //repertoire: 'inspection', / TODO
                _token: '{{ csrf_token() }}'
            }
        })

        uppy.use(
            Dashboard, {
                inline: true,
                target: '#upload-DragDrop',
                replaceTargetContent: true,
                showLinkToFileUploadResult: false,
                showProgressDetails: true,
                showRemoveButtonAfterComplete: true,
                note: 'Uniquement des photos, maximum 5 fichiers de 2 MB.',
                height: 400,
                width: 600,
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
                mirror: false,
                showRecordingLength: false,
                preferredVideoMimeType: null,
                preferredImageMimeType: null,
                locale: {},
                videoConstraints: {
                    facingMode: 'environment',
                    width: {min: 1280, ideal: 1280, max: 1920},
                    height: {min: 720, ideal: 720, max: 1080},
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

        const existingMedias = @json($inspection->medias->each(function ($media) {
                $media->url = url(Croppa::url($media->crop_path, 400));
            }));

        existingMedias.forEach(media => {
            fetch(media.url)
                .then((response) => response.blob())
                .then((blob) => {

                    uppy.addFile({
                        id: media.id,
                        name: media.fichier,
                        type: blob.type,
                        data: blob,
                        source: 'Server',
                        isRemote: true,
                        preview: media.url,
                        meta: {
                            id: media.id,
                            existing: true
                        },
                    });
                });
        });

        uppy.on('file-added', (file) => {
            if (file.meta.existing === true) {
                uppy.setFileState(file.id, {
                    progress: {uploadComplete: true}
                })
            }
        })

        uppy.on('upload-success', function (file, response) {
            uppy.setFileMeta(file.id, {
                id: response.body.media.id
            })
        });

        uppy.on('upload-error', function (file, error, response) {
            console.log('error with file:', file.id);
            console.log('error message:', error);
            console.log('error message:', response);
        });

        uppy.on('file-removed', function (file) {
            fetch("/administration/media/" + file.meta.id + "/destroy", {
                method: "GET" // Par défaut fetch fait GET, donc cette ligne est optionnelle
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Erreur lors de la suppression");
                    }
                    return true;
                })
                .then(data => {
                    console.log("Suppression réussie :", data);
                })
                .catch(error => {
                    console.error("Erreur :", error);
                });

            return false;
        });

        uppy.on('restriction-failed', function (file, error) {
            /*document.querySelector('#upload-alerts').insertAdjacentHTML('beforeend', '<div class="alert alert-warning">' + file.name +' : ' + error +'</div>')*/
        });

    </script>


@endsection