@extends('IpsumAdmin::layouts.app')
@section('title', 'Inspection')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux._progressbar')

    <h1 class="main-title">État des lieux - Inspection {{ $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'initiale': 'finale' }}</h1>

    {{ Aire::open()->id('reservation')->route($dommage->exists ? 'admin.inspection.dommage.update' : 'admin.inspection.dommage.store', $dommage->exists ? [$reservation, $type, $dommage] : [$reservation, $type])->bind($dommage)/*->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionDommage::class)*/ }}

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
                    <div><a href="{{ route('admin.inspection.dommage', [$reservation, $type]) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div><button type="submit" id="nextBtn" class="btn btn-primary">Suivant</button></div>
                </div>
            </div>

        </div>

    </div>

    {{ Aire::close() }}



    <link href="https://releases.transloadit.com/uppy/v5.2.1/uppy.min.css" rel="stylesheet">
    <style>
        .uppy-StatusBar-actionBtn--done{
            display: none;
        }
    </style>
    <script type="module">
        var medias = [];

        import { Uppy, Dashboard, Webcam, XHRUpload  } from "https://releases.transloadit.com/uppy/v5.2.1/uppy.min.mjs"

        const fr_FR = {
            strings: {
                addBulkFilesFailed: {
                    '0': 'L’ajout de %{smart_count} fichier a échoué en raison d’une erreur interne',
                    '1': 'L’ajout de %{smart_count} fichiers a échoué en raison d’erreurs internes',
                },
                addedNumFiles: '%{numFiles} fichier(s) ajouté(s)',
                addingMoreFiles: 'Ajout de fichiers',
                additionalRestrictionsFailed:
                    '%{count} restrictions supplémentaires n’ont pas été respectées',
                addMore: 'Ajouter d’autres',
                addMoreFiles: 'Ajouter d’autres fichiers',
                aggregateExceedsSize:
                    'Vous avez sélectionné %{size} de fichiers, mais la taille maximale autorisée est %{sizeAllowed}',
                allFilesFromFolderNamed: 'Tous les fichiers du dossier %{name}',
                allowAccessDescription:
                    'Pour prendre des photos ou enregistrer une vidéo, veuillez autoriser l’accès à votre caméra pour ce site.',
                allowAccessTitle: 'Veuillez autoriser l’accès à votre caméra',
                allowAudioAccessDescription:
                    'Pour enregistrer de l’audio, veuillez autoriser l’accès au microphone pour ce site.',
                allowAudioAccessTitle: 'Veuillez autoriser l’accès au microphone',
                aspectRatioLandscape: 'Recadrer en paysage (16:9)',
                aspectRatioPortrait: 'Recadrer en portrait (9:16)',
                aspectRatioSquare: 'Recadrer pour obtenir une photo carrée',
                authAborted: 'Authentification interrompue',
                authenticateWith: 'Se connecter à %{pluginName}',
                authenticateWithTitle:
                    'Veuillez vous authentifier avec %{pluginName} pour sélectionner les fichiers',
                back: 'Retour',
                browse: 'naviguer',
                browseFiles: 'naviguer dans les fichiers',
                browseFolders: 'naviguer dans les dossiers',
                cancel: 'Annuler',
                cancelUpload: 'Annuler le téléversement',
                closeModal: 'Fermer la fenêtre',
                companionError: 'Connexion à Companion a échoué',
                companionUnauthorizeHint:
                    'Pour vous déconnecter de votre compte %{provider}, veuillez aller à %{url}',
                complete: 'Terminé',
                compressedX: '%{size} économisé(s) par la compression',
                compressingImages: 'Compression des images…',
                connectedToInternet: 'Connecté à Internet',
                copyLink: 'Copier le lien',
                copyLinkToClipboardFallback: 'Copier le lien ci-dessous',
                copyLinkToClipboardSuccess: 'Lien copié',
                creatingAssembly: 'Préparation du téléversement…',
                creatingAssemblyFailed: 'Transloadit: Impossible de créer Assembly',
                dashboardTitle: 'Téléverseur de fichiers',
                dashboardWindowTitle:
                    'Fenêtre de téléversement de fichiers (Appuyez sur Échap pour fermer)',
                dataUploadedOfTotal: '%{complete} sur %{total}',
                discardRecordedFile: 'Supprimer le fichier enregistré',
                done: 'Terminé',
                dropHint: 'Déposez vos fichiers ici',
                dropPasteBoth: 'Déposer les fichiers ici, coller ou %{browse}',
                dropPasteFiles: 'Déposer les fichiers ici, coller ou %{browse}',
                dropPasteFolders: 'Déposer les fichiers ici, coller ou %{browse}',
                dropPasteImportBoth:
                    'Déposer les fichiers ici, coller, %{browse} ou importer de',
                dropPasteImportFiles:
                    'Déposer les fichiers ici, coller, %{browse} ou importer de',
                dropPasteImportFolders:
                    'Déposer les fichiers ici, coller, %{browse} ou importer de',
                editFile: 'Modifier le fichier',
                editImage: 'Modifier l’image',
                editFileWithFilename: 'Modifier le fichier %{file}',
                editing: 'Modification en cours de %{file}',
                emptyFolderAdded: 'Aucun fichier n’a été ajouté depuis un dossier vide',
                encoding: 'Traitement…',
                enterCorrectUrl:
                    'Lien incorrect: Assurez-vous que vous entrez un lien direct vers le fichier',
                enterTextToSearch: 'Entrez un texte pour rechercher des images',
                enterUrlToImport: 'Entrez le lien pour importer un fichier',
                error: 'Erreur',
                exceedsSize:
                    'Le fichier %{file} dépasse la taille maximale autorisée de %{size}',
                failedToFetch:
                    'Companion a échoué à récupérer ce lien, assurez-vous qu’il est correct',
                failedToUpload: 'Le téléversement de %{file} a échoué',
                fileSource: 'Fichier source: %{name}',
                filesUploadedOfTotal: {
                    '0': '%{complete} sur %{smart_count} fichier téléversé',
                    '1': '%{complete} sur %{smart_count} fichiers téléversés',
                },
                filter: 'Filtrer',
                finishEditingFile: 'Terminer l’édition du fichier',
                flipHorizontal: 'Retourner horizontalement',
                folderAdded: {
                    '0': '%{smart_count} fichier ajouté de %{folder}',
                    '1': '%{smart_count} fichiers ajoutés de %{folder}',
                },
                folderAlreadyAdded: 'Le dossier "%{folder}" a déjà été ajouté',
                generatingThumbnails: 'Génération des vignettes…',
                import: 'Importer',
                importFiles: 'Importer des fichiers depuis :',
                importFrom: 'Importer de %{name}',
                inferiorSize: 'Ce fichier est plus petit que la taille autorisée de %{size}',
                loadedXFiles: 'Chargé %{numFiles} fichiers',
                loading: 'Chargement…',
                logOut: 'Déconnexion',
                micDisabled: 'Accès au micro refusé par l’utilisateur',
                missingRequiredMetaField: 'Champ méta requis manquant',
                missingRequiredMetaFieldOnFile:
                    'Champs méta requis manquants dans %{fileName}',
                missingRequiredMetaFields: {
                    '0': 'Champ méta requis manquant : %{fields}.',
                    '1': 'Champs méta requis manquants : %{fields}.',
                },
                myDevice: 'Mon Appareil',
                noAudioDescription:
                    'Pour enregistrer de l’audio, veuillez connecter un microphone ou un autre appareil d’entrée audio',
                noAudioTitle: 'Microphone non disponible',
                noCameraDescription:
                    'Pour prendre des photos ou enregistrer une vidéo, veuillez connecter une caméra',
                noDuplicates: 'Impossible d’ajouter le fichier "%{fileName}", il existe déjà',
                noFilesFound: 'Vous n’avez aucun fichier ou dossier ici',
                noInternetConnection: 'Pas de connexion à Internet',
                noMoreFilesAllowed:
                    'Impossible d’ajouter de nouveaux fichiers: en cours de chargement ',
                noSearchResults:
                    'Malheureusement, il n’y a aucun résultat pour cette recherche',
                openFolderNamed: 'Ouvrir %{name}',
                pause: 'Pause',
                pauseUpload: 'Mettre en pause le téléversement',
                paused: 'En pause',
                poweredBy: 'Propulsé par %{uppy}',
                processingXFiles: {
                    '0': 'Traitement de %{smart_count} fichier',
                    '1': 'Traitement de %{smart_count} fichiers',
                },
                recording: 'Enregistrement',
                recordingLength: 'Durée d’enregistrement %{recording_length}',
                recordingStoppedMaxSize:
                    'L’enregistrement s’est arrété car la taille du fichier dépasse la limite',
                recordVideoBtn: 'Enregistrer une vidéo',
                recoveredAllFiles:
                    'Nous avons restauré tous les fichiers. Vous pouvez maintenant reprendre le téléversement.',
                recoveredXFiles: {
                    '0': 'Nous n’avons pas pu récupérer entièrement 1 fichier. Veuillez le resélectionner et reprendre le téléversement.',
                    '1': 'Nous n’avons pas pu récupérer entièrement %{smart_count} fichiers. Veuillez les resélectionner et reprendre le téléversement.',
                },
                removeFile: 'Effacer le fichier %{file}',
                resetFilter: 'Réinitialiser filtre',
                resume: 'Reprendre',
                resumeUpload: 'Reprendre le téléversement',
                retry: 'Réessayer',
                retryUpload: 'Réessayer le téléversement',
                reSelect: 'Resélectionner',
                save: 'Sauvegarder',
                saveChanges: 'Sauvegarder les modifications',
                selectFileNamed: 'Sélectionner le fichier %{name}',
                selectX: {
                    '0': 'Sélectionner %{smart_count}',
                    '1': 'Sélectionner %{smart_count}',
                },
                sessionRestored: 'Session restaurée',
                signInWithGoogle: 'Se connecter avec Google',
                smile: 'Souriez !',
                startRecording: 'Commencer l’enregistrement vidéo',
                stopRecording: 'Arrêter l’enregistrement vidéo',
                streamActive: 'Stream actif',
                streamPassive: 'Stream passif',
                submitRecordedFile: 'Envoyer la vidéo enregistrée',
                takePicture: 'Prendre une photo',
                takePictureBtn: 'Prendre une photo',
                timedOut: 'Téléversement bloqué durant %{seconds} secondes, annulation.',
                unselectFileNamed: 'Désélectionner le fichier %{name}',
                upload: 'Téléverser',
                uploadComplete: 'Téléversement terminé',
                uploadFailed: 'Le téléversement a échoué',
                uploadPaused: 'Téléversement mis en pause',
                uploadStalled:
                    'Téléversement bloqué depuis %{seconds} secondes. Il est peut-être nécessaire de recommencer l’opération.',
                uploadXFiles: {
                    '0': 'Téléverser %{smart_count} fichier',
                    '1': 'Téléverser %{smart_count} fichiers',
                },
                uploadXNewFiles: {
                    '0': 'Téléverser +%{smart_count} fichier',
                    '1': 'Téléverser +%{smart_count} fichiers',
                },
                uploading: 'Téléversement en cours',
                uploadingXFiles: {
                    '0': 'Téléversement de %{smart_count} fichier',
                    '1': 'Téléversement de %{smart_count} fichiers',
                },
                xFilesSelected: {
                    '0': '%{smart_count} fichier sélectionné',
                    '1': '%{smart_count} fichiers sélectionnés',
                },
                xMoreFilesAdded: {
                    '0': '%{smart_count} autre fichier ajouté',
                    '1': '%{smart_count} autres fichiers ajoutés',
                },
                xTimeLeft: '%{time} restantes',
                youCanOnlyUploadFileTypes: 'Vous pouvez seulement téléverser: %{types}',
                youCanOnlyUploadX: {
                    '0': 'Vous pouvez seulement téléverser %{smart_count} fichier',
                    '1': 'Vous pouvez seulement téléverser %{smart_count} fichiers',
                },
                youHaveToAtLeastSelectX: {
                    '0': 'Vous devez sélectionner au moins %{smart_count} fichier',
                    '1': 'Vous devez sélectionner au moins %{smart_count} fichiers',
                },
            },
            pluralize(n) {
                return n === 1 ? 0 : 1;
            }
        };

        const uppy = new Uppy({
            debug: true,
            autoProceed: true,
            locale: fr_FR,
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

        const existingMedias = @json($dommage->medias ?? []);

        existingMedias.forEach(media => {
            //const media_url = media.fichier ? "{{ asset('').config('ipsum.media.path') }}" + media.fichier : null;
            const media_url = "{{ asset('').$dommage->illustration?->path }}";
            console.log(media, media_url)
            if (!media_url) return;

            fetch(media_url)
                .then((response) => response.blob())
                .then((blob) => {

            uppy.addFile({
                id: media.id,
                name: media.fichier,
                type: blob.type,
                data: blob,
                source: 'Local',
                meta: {
                    publication_id: media.id,
                    publication_type: media.publication_type,
                    titre: media.titre,
                    alt: media.tag_alt
                },
            });
            });

            // Ajouter dans le tableau pour pouvoir supprimer
            medias.push(media);
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
            medias.forEach(function(media, index, medias){
                console.log(media, file)
                if (media.fichier === file.name) {
                    console.log("/administration/media/"+media.id+"/destroy")
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