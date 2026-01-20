@extends('IpsumAdmin::layouts.app')
@section('title', 'Inspection')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux.step._progressbar')

    <h1 class="main-title">État des lieux - Inspection {{ $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'initiale': 'finale' }}</h1>

    {{ Aire::open()->id('reservation')->route('admin.inspection.signature.locataire.store', [$reservation, $type])->bind($inspection)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionSignatureLocataire::class) }}

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Signature client</h2>
                </div>
                <div class="box-body">

                    {{-- Signatures --}}
                    <div class="form-row">
                        <div class="col-md-12 mb-2">
                            <p>
                                Par ma signature, je reconnais être d'accord avec l'état des lieux
                            </p>
                            <div style="width: 335px">
                                <canvas id="signature-client-pad" class="border rounded w-full h-32 {{ $inspection->locataire_signature ? '' : '' }}"></canvas>
                            </div>
                            <div class="signature-client-error"></div>
                            <input type="hidden" name="locataire_signature" id="locataire_signature" value="{{ $inspection->locataire_signature ??  '' }}">
                            <button type="button" class="btn btn-outline-danger" id="clear-signature-client"><i class="fas fa-trash-alt"></i> Effacer la signature</button>
                        </div>
                    </div>

                </div>
                <div class="box-footer">
                    <div><a href="{{ route('admin.inspection.recapitulatif', [$reservation, $type]) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div>
                        <button type="submit" id="nextBtn" class="btn btn-primary">Suivant</button>
                    </div>
                </div>
            </div>
        </div>
        {{ Aire::close() }}

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.3.4/signature_pad.min.js" integrity="sha512-Mtr2f9aMp/TVEdDWcRlcREy9NfgsvXvApdxrm3/gK8lAMWnXrFsYaoW01B5eJhrUpBT7hmIjLeaQe0hnL7Oh1w==" crossorigin="anonymous"
            referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const signatures = [
                {
                    canvas: document.getElementById('signature-client-pad'),
                    input: document.getElementById('locataire_signature'),
                    clearBtn: document.getElementById('clear-signature-client')
                }
            ];

            signatures.forEach(({canvas, input, clearBtn}) => {
                if (!canvas) return;

                const signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(0, 0, 0, 0.05)',
                    penColor: 'rgb(0, 0, 0)',
                });

                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    const width = canvas.parentElement.offsetWidth;
                    const height = 200;

                    const data = input.value || null;

                    canvas.width = width * ratio;
                    canvas.height = height * ratio;
                    canvas.style.width = width + 'px';
                    canvas.style.height = height + 'px';

                    canvas.getContext('2d').scale(ratio, ratio);
                    signaturePad.clear();

                    if (data) {
                        signaturePad.fromDataURL(data);
                        disableSignature();
                    }
                }

                function disableSignature() {
                    canvas.style.pointerEvents = 'none';
                }

                function enableSignature() {
                    canvas.style.pointerEvents = 'auto';
                }

                function updateInput() {
                    input.value = !signaturePad.isEmpty()
                        ? signaturePad.toDataURL('image/png')
                        : '';
                }

                // Init
                resizeCanvas();
                window.addEventListener('resize', resizeCanvas);

                // Mise à jour après dessin
                canvas.addEventListener('mouseup', updateInput);
                canvas.addEventListener('touchend', updateInput);

                // Si signature déjà existante → verrouillée
                if (input.value) {
                    disableSignature();
                }

                // Bouton effacer
                if (clearBtn) {
                    clearBtn.addEventListener('click', () => {
                        signaturePad.clear();
                        input.value = '';
                        enableSignature();
                    });
                }
            });
        });
    </script>

@endsection