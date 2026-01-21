<div class="row">

    <div class="col-md-6">
        <!-- Informations Client -->
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-2">Informations client</div>
            <div class="card-body p-2">
                {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}<br/>
                {{ $reservation->email }}<br>
                @if ($reservation->naissance_at)
                    {{ _('Né le') }} {{ $reservation->naissance_at->format('d/m/Y') }}
                    @if ($reservation->naissance_lieu)
                        à {{ $reservation->naissance_lieu }}
                    @endif
                    <br>
                @endif
                @if ($reservation->permis_numero or $reservation->permis_at)
                    {{ _('Permis') }}
                    @if ($reservation->permis_numero)
                        {{ _('n°') }}{{ $reservation->permis_numero }}
                    @endif
                    @if ($reservation->permis_at)
                        {{ _('délivré le') }} {{ $reservation->permis_at->format('d/m/Y') }}
                    @endif
                    @if ($reservation->permis_delivre)
                        {{ _('par') }} {{ $reservation->permis_delivre }}
                    @endif
                @endif
             </div>
        </div>
    </div>

    @if($reservation->conducteurs)
        @foreach($reservation->conducteurs as $conducteur)
            <div class="col-md-6">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-secondary text-white p-2">Conducteur additionnel #{{ $loop->iteration }}</div>
                    <div class="card-body p-2">
                        {{ $conducteur->prenom }} {{ $conducteur->nom }}<br/>
                        @if ($conducteur->naissance_at)
                            {{ _('Né le') }} {{ $conducteur->naissance_at->format('d/m/Y') }}
                            @if ($conducteur->naissance_lieu)
                                à {{ $conducteur->naissance_lieu }}
                            @endif
                            <br>
                        @endif
                        @if ($conducteur->permis_numero or $conducteur->permis_at)
                            {{ _('Permis') }}
                            @if ($conducteur->permis_numero)
                                {{ _('n°') }}{{ $conducteur->permis_numero }}
                            @endif
                            @if ($conducteur->permis_at)
                                {{ _('délivré le') }} {{ $conducteur->permis_at->format('d/m/Y') }}
                            @endif
                            @if ($conducteur->permis_delivre)
                                {{ _('par') }} {{ $conducteur->permis_delivre }}
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="col-md-6">
        <!-- Véhicule -->
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-2">Véhicule</div>
            <div class="card-body p-2" id="ajax-vehicule">
                @include('IpsumReservation::reservation.etat_des_lieux.step.recap._vehicule')
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <!-- Dommages -->
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-2">Dommages constatés</div>
            <div class="card-body p-2">
                <div class="d-flex flex-row flex-wrap">
                    @include('IpsumReservation::reservation.etat_des_lieux.step.recap._dommage')
                </div>
            </div>
        </div>
    </div>

    <!-- Photos -->
    {{--@php
        $photos = $inspection->medias()->groupe('photos')->get();
    @endphp
    @if($photos->count())
        <div class="card mb-3 shadow-sm col-md-12">
            <div class="card-header bg-secondary text-white p-2">Photos</div>
            <div class="card-body p-2">
                <div class="d-flex flex-row flex-wrap sortable upload-files">
                    @foreach($photos as $media)
                        @include('IpsumReservation::reservation.etat_des_lieux.step._photo')
                    @endforeach
                </div>
            </div>
        </div>
    @endif--}}

    <div class="col-md-4">
        <!-- Checklist -->
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-2">Checklist</div>
            <div class="card-body p-2">
                @include('IpsumReservation::reservation.etat_des_lieux.step.recap._checklist')
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-2">Détails de la tarification</div>
            <div class="card-body p-2">
                <table class="table table-sm table-billing mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th>Désignation</th>
                        <th class="text-right">Montant</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($reservation->prestations->count())
                        @foreach ($reservation->prestations as $prestation)
                            <tr>
                                <td class="pl-3">{{ $prestation->quantite }} x {{ $prestation->nom }} <small class="text-muted">{{ $prestation->choix }}</small></td>
                                <td class="text-right pr-3">{{ $prestation->tarif_libelle }}</td>
                            </tr>
                        @endforeach
                    @endif

                    @if($reservation->promotions->count())
                        @foreach ($reservation->promotions as $promotion)
                            <tr class="text-success small">
                                <td class="pl-3">Offre : {{ $promotion->nom }}</td>
                                <td class="text-right pr-3">-@prix($promotion->reduction)&nbsp;€</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                    <tfoot class="border-top">
                    @if ($reservation->remise)
                        <tr class="text-danger">
                            <td class="pl-3">Remise</td>
                            <td class="text-right pr-3">-@prix($reservation->remise)&nbsp;€</td>
                        </tr>
                    @endif
                    <tr class="table-info font-weight-bold">
                        <td class="pl-3">TOTAL (TTC)</td>
                        <td class="text-right pr-3" style="font-size: 1.2rem;">@prix($reservation->total)&nbsp;€</td>
                    </tr>
                    @if (!$reservation->is_payed)
                        <tr class="bg-white border-top">
                            <td class="pl-3 text-danger font-weight-bold">Reste à régler</td>
                            <td class="text-right pr-3 text-danger font-weight-bold">@prix($reservation->total - $reservation->montant_paye)&nbsp;€</td>
                        </tr>
                    @endif
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Observations -->
    @if($inspection->observations)
        <div class="col-md-4">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-secondary text-white p-2">Observations</div>
                <div class="card-body p-2" id="ajax-observation">
                    <p class="text-muted">
                        {!! $inspection->observations !!}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if($inspection->locataire_signature && $inspection->agent_signature)
        <div class="col-md-4">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-secondary text-white p-2">Signatures</div>
                <div class="card-body p-2">
                    <div class="row" id="ajax-photos">
                        <div class="col-md-6">
                            <h3>Client</h3>
                            <img src="{{ $inspection->locataire_signature }}" alt="Signature agent" style="width:200px; height:auto; border:1px solid #000;">
                            <p class="mt-2">Signé le : {{ $inspection->locataire_signature_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h3>Loueur</h3>
                            <img src="{{ $inspection->agent_signature }}" alt="Signature agent" style="width:200px; height:auto; border:1px solid #000;">
                            <p class="mt-2">Signé le : {{ $inspection->agent_signature_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>