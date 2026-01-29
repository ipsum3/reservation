@extends('IpsumAdmin::layouts.app')
@section('title', 'État des lieux')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux.step._progressbar')

    <h1 class="main-title">État des lieux {{ strtolower($type->nom) }} <a href="{{ route('admin.reservation.edit', $reservation) }}"><small class="text-muted">(résa. {{ $reservation->reference }})</small></a></h1>

    {{ Aire::open()->id('reservation')->route($dommage->exists ? 'admin.inspection.dommage.update' : 'admin.inspection.dommage.store', $dommage->exists ? [$reservation, $type, $dommage] : [$reservation, $type])->bind($dommage)/*->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionDommage::class)*/ }}

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">{{ $dommage->exists ? 'Modifier' : 'Ajouter' }} un dommage</h2>
                </div>
                <div class="box-body">

                    <div class="row justify-content-center">

                        <div class="col-md-8 mt-3 mb-5">

                            <div id="upload-DragDrop" class=""
                                 data-uploadmedias="{{ route('admin.media.publication', ['toolbar' => ['editable' => false, 'sortable' => false, 'title' => false, 'link' => true, 'pad' => true], 'publication_type' => \Ipsum\Reservation\app\Models\Dommage\Dommage::class, 'publication_id' => $dommage->exists ? $dommage->id : ''  ]) }}"
                            ></div>

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
                    <div><a href="{{ route('admin.inspection.dommages', [$reservation, $type]) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div>
                        <button type="submit" id="nextBtn" class="btn btn-primary">{{ $dommage->exists ? 'Modifier' : 'Ajouter' }}</button>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{ Aire::close() }}



    <link href="https://releases.transloadit.com/uppy/v5.2.1/uppy.min.css" rel="stylesheet">
    <style>
        .uppy-StatusBar-actionBtn--done {
            display: none;
        }
    </style>
    <script type="module">
        var medias = [];

        import {Uppy, Dashboard, Webcam, XHRUpload} from "https://releases.transloadit.com/uppy/v5.2.1/uppy.min.mjs"

        import fr_FR from 'https://cdn.jsdelivr.net/npm/@uppy/locales@3.4.0/lib/fr_FR.js'

        const uppy = new Uppy({
            debug: true,
            autoProceed: true,
            locale: fr_FR,
            restrictions: {
                maxFileSize: 2000000,
                maxNumberOfFiles: 1,
                allowedFileTypes: ['image/*']
            },
            meta: {
                publication_id: '{{ $dommage->id ?? '' }}',
                publication_type: "Ipsum\\Reservation\\app\\Models\\Dommage\\Dommage",
                //repertoire: 'dommages', / TODO
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
                note: 'Uniquement des photos, maximum un fichier de 2 MB.',
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

        const existingMedias = @json($dommage->medias ?? []);

        existingMedias.forEach(media => {
            const media_url = "{{ asset($dommage->illustration?->path) }}";
            //console.log(media, media_url)
            if (!media_url) return;

            fetch(media_url)
                .then((response) => response.blob())
                .then((blob) => {

                    uppy.addFile({
                        id: media.id,
                        name: media.fichier,
                        type: blob.type,
                        data: blob,
                        source: 'Server',
                        isRemote: true,
                        meta: {
                            existing: true,
                            publication_id: media.id,
                            publication_type: media.publication_type,
                            titre: media.titre,
                            alt: media.tag_alt,
                            previewUrl: media_url,
                        },
                    });
                });

            // Ajouter dans le tableau pour pouvoir supprimer
            medias.push(media);
        });

        uppy.on('file-added', (file) => {
            if (file.meta.existing === true) {
                uppy.setFileState(file.id, {
                    preview: file.meta.previewUrl,
                    progress: {uploadComplete: true}
                })
            }
        })

        uppy.on('upload-success', function (file, response) {
            medias.push(response.body.media);
        });

        uppy.on('upload-error', function (file, error, response) {
            console.log('error with file:', file.id);
            console.log('error message:', error);
            console.log('error message:', response);
        });

        uppy.on('file-removed', function (file) {
            medias.forEach(function (media, index, medias) {
                //console.log(media, file)
                if (media.fichier === file.name) {

                    medias.splice(index, 1);

                    fetch("/administration/media/" + media.id + "/destroy", {
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
                }
            });
        });

        uppy.on('restriction-failed', function (file, error) {
            /*document.querySelector('#upload-alerts').insertAdjacentHTML('beforeend', '<div class="alert alert-warning">' + file.name +' : ' + error +'</div>')*/
        });

    </script>

@endsection