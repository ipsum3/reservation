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
                        <li><a href="{{ route('admin.inspection.dommage', [$reservation, $type]) }}">Dommages / Photos</a></li>
                        <li><a href="{{ route('admin.inspection.recapitulatif', [$reservation, $type]) }}">Récapitulatif</a></li>
                        <li><a href="{{ route('admin.inspection.signature.locataire', [$reservation, $type]) }}">Signature client</a></li>
                        <li class="active">Signature agent</li>
                    </ul>
                </div>
                <div class="box-body">

                    {{ Aire::open()->id('reservation')->route('admin.inspection.signature.agent.store', [$reservation, $type])->bind($inspection)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionSignatureAgent::class) }}

                            <!-- STEP 8 -->
                            <div class="step active">
                                {{-- Signatures --}}
                                <div class="form-row">
                                    <div class="col-md-12 mb-2">
                                        <h2 class="text-xl font-semibold mb-2">Signature agent</h2>
                                        @if($reservation->contrat && !config('ipsum.reservation.contrat.disable'))
                                            <p>
                                                Validation du  <a href="{{ route('admin.reservation.contrat', [$reservation]) }}" target="_blank">contrat</a> avec le client.
                                            </p>
                                        @endif
                                        <canvas id="signature-agent-pad" class="border rounded w-full h-32"></canvas>
                                        <div class="signature-agent-error"></div>
                                        <input type="hidden" name="agent_signature" id="agent_signature" value="{{ $inspection->agent_signature ??  '' }}" >
                                        <button type="button" class="btn btn-outline-danger" id="clear-signature-agent"><i class="fas fa-trash-alt"></i> Effacer la signature</button>

                                    </div>
                                </div>
                            </div>

                            <!-- Navigation -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('admin.inspection.signature.locataire', [$reservation, $type]) }}" id="prevBtn" class="btn btn-secondary">Retour</a>
                                <button type="submit" id="nextBtn" class="btn btn-success">Valider</button>
                            </div>

                        {{ Aire::close() }}
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.3.4/signature_pad.min.js" integrity="sha512-Mtr2f9aMp/TVEdDWcRlcREy9NfgsvXvApdxrm3/gK8lAMWnXrFsYaoW01B5eJhrUpBT7hmIjLeaQe0hnL7Oh1w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Gestion signatures
            const signatures = [
                { canvas: document.getElementById('signature-agent-pad'), input: document.getElementById('agent_signature'), clearBtn: document.getElementById('clear-signature-agent') },
                { canvas: document.getElementById('signature-client-pad'), input: document.getElementById('locataire_signature'), clearBtn: document.getElementById('clear-signature-client') }
            ];

            signatures.forEach(({ canvas, input, clearBtn }) => {
                if (!canvas) return;

                const parentWidth = 302;
                canvas.setAttribute('width', parentWidth);

                const signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(0, 0, 0, 0.05)',
                    penColor: 'rgb(0, 0, 0)',
                });

                const updateSignatureInput = () => {
                    input.value = !signaturePad.isEmpty() ? signaturePad.toDataURL() : '';
                };

                if (input.value) {
                    signaturePad.fromDataURL(input.value);
                    signaturePad._isEmpty = false;
                }

                ['mouseup', 'touchend'].forEach(evt => {
                    canvas.addEventListener(evt, updateSignatureInput);
                });

                if (clearBtn) {
                    clearBtn.addEventListener('click', () => {
                        signaturePad.clear();
                        updateSignatureInput();
                    });
                }
            });
        });
    </script>

@endsection