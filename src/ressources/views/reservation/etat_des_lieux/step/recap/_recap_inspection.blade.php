<div class="row">
    <div class="col-md-6">
        <!-- Véhicule -->
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Véhicule</div>
            <div class="card-body p-2" id="ajax-vehicule">
                <strong>{{ _('Catégorie') }} {{ $reservation->categorie_nom }}</strong><br>
                @if ($reservation->vehicule)
                    <strong>{{ _('Marque et modéle') }} :</strong> {{ $reservation->vehicule->marque_modele }}<br>
                    <strong>{{ _('Immatriculation') }} :</strong> {{ $reservation->vehicule->immatriculation }}<br>
                @endif
                <strong>{{ _('Kilométrage') }} :</strong> {{ $inspection?->kilometrage }} km
                @if (!$inspection->type->is_initial && $reservation->inspection_initiale)
                    <span class="text-muted">(initial : {{ $reservation->inspection_initiale->kilometrage }} km)</span>
                @endif
                <br>
                <strong>{{ _('Carburant') }} :</strong> {{ $inspection?->carburant }}/8
                @if (!$inspection->type->is_initial && $reservation->inspection_initiale)
                    <span class="text-muted">(initial : {{ $reservation->inspection_initiale->carburant }}/8)</span>
                @endif
                <br>
                @if($inspection->observations)
                    <strong>Observations :</strong> {!! nl2br(e($inspection->observations)) !!}
                @endif
                @if(!$inspection->type->is_initial && $reservation->inspection_initiale && $reservation->inspection_initiale->observations)
                    <br>
                    <span class="text-muted">Observations initial : {!! nl2br(e($reservation->inspection_initiale->observations)) !!}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Checklist -->
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Checklist</div>
            <div class="card-body p-2">
                @foreach($checklists as $checklist)
                    <div class="mt-2">
                        @if( in_array($checklist->id, $inspection->checklists->pluck('id')->toArray() ?? []) )
                            <i class="fa fa-check-square text-success"></i>
                        @else
                            <i class="fa fa-window-close text-danger"></i>
                        @endif
                        <label class="form-check-label" for="checklist_{{ $checklist->id }}">
                            {{ $checklist->nom }}
                            @if(!$type->is_initial && $reservation->inspection_initiale)
                                <span class="text-muted">(initial
                                    @if(in_array($checklist->id, $reservation->inspection_initiale->checklists->pluck('id')->toArray()))
                                        <i class="fa fa-check-square text-success"></i>
                                    @else
                                        <i class="fa fa-window-close text-danger"></i>
                                    @endif
                                    )
                                </span>
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <!-- Dommages -->
        {{--TODO mettre les info sur l'inspection initiale si présente pas possible actuelement avec la bdd --}}
        @php
            // TODO pour la page show, impossible de connaitre les dommages de l'inspection iitiale après qu'une autre inspection soit faite. Il faudrait ajouter une table dommage_inspections
             $dommages = $inspection->type->is_initial ? $reservation->vehicule->dommages : $inspection->dommages;
        @endphp
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Dommage{{ $dommages->count() > 1 ? 's' : '' }} constaté{{ $dommages->count() > 1 ? 's' : '' }}</div>
            <div class="card-body p-2">
                <div class="d-flex flex-row flex-wrap">
                    @if($dommages->count())
                        @foreach($dommages as $dommage)
                            <div class="d-flex flex-row flex-wrap justify-content-center">
                                @include('IpsumReservation::reservation.etat_des_lieux.step._dommage', ['protected' => true])
                            </div>
                        @endforeach
                    @else
                        <p>Aucun dommage constaté</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @php
        $photos = $inspection->medias()->groupe('photos')->get();
    @endphp
    @if($photos->count())
        <div class="col-md-12">
            <!-- Photos -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-secondary text-white p-1">Photo{{ $photos->count() > 1 ? 's' : '' }} rapide{{ $photos->count() > 1 ? 's' : '' }}</div>
                <div class="card-body p-2">
                    <div class="d-flex flex-row flex-wrap">
                        @foreach($photos as $media)
                            <div class="media" style="min-width: 200px">
                                <div class="media-img">
                                    <a href="{{ asset($media->path) }}" target="_blank" title="Voir">
                                        <img src="{{ Croppa::url($media->cropPath, 300) }}" alt="{{ $media->tagAlt }}">
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>