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
        debug: false,
        autoProceed: true,
        locale: fr_FR,
        restrictions: {
            maxFileSize: 2000000,
            maxNumberOfFiles: maxNumberOfFiles,
            allowedFileTypes: ['image/*']
        },
        meta: {
            publication_id: publicationId,
            publication_type: publicationType,
            groupe: groupe,
            repertoire: repertoire,
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
            note: 'Uniquement des photos, maximum ' + maxNumberOfFiles + ' fichier(s) de 2 MB.',
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

    /*uppy.on('restriction-failed', function (file, error) {
        document.querySelector('#upload-alerts').insertAdjacentHTML('beforeend', '<div class="alert alert-warning">' + file.name +' : ' + error +'</div>')
    });*/

</script>
