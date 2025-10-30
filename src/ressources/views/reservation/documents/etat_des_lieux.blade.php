<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>
        @yield('title')
    </title>

    <style type="text/css">
        @page {
            margin: 0.5cm 1cm;
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 12px;
        }

        .page-number:before {
            content: "Page " counter(page);
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td,th {
            vertical-align: top;
            padding: 1.5mm;
            border: 0.2mm solid #888;
        }

        .tableau1 th {
            background-color: {{ config('ipsum.reservation.etat_des_lieux.couleur') }};
            color: #fff;

            text-align: center;
            font-weight: normal;
        }

        .tableau2 {
            font-size: 11px;
        }

        .tableau2 th {
            background-color: {{ config('ipsum.reservation.etat_des_lieux.couleur') }};
            color: #fff;
            border-color: {{ config('ipsum.reservation.etat_des_lieux.couleur') }};

            padding: 2px 0;

            text-align: center;
        }

        .tableau3 td {
            padding: 0;
            border: 0 none;
        }

        td.total {
            background-color: #ccc;
            font-weight: bold;
        }
        .tiret {
            color: #bbb;
        }

        #footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
        }
        #footer table td {
            border: none;
        }

        .page_break {
            page-break-before: always;
        }

        h2{
            font-size: 14px;
        }

        h3{
            font-size: 13px;
        }
        .cgv h2{
            font-size: 6px;
            margin: 2px 0 0 0;
        }
        .cgv ul{
            padding-left: 5px;
            font-size: 7px;
            margin: 0;
        }
        .cgv p{
            margin: 0;
            font-size: 7px;
        }

        input {
            width: 20px;
            height: 13px;
            padding: 0;
            margin:0;
            vertical-align: middle;
            position: relative;
            top: -5px;
            *overflow: hidden;
        }
    </style>
</head>
<body>
<div id="footer">
    <table>
        <tr>
            <td>{{ Config::get('settings.nom_site') }} - {{ Config::get('settings.entreprise_identification') }}</td>
            <td style="text-align: right; width: 10%"><div class="page-number"></div></td>
        </tr>
    </table>
</div>
<div>
    <table style="padding-bottom: 2mm;border-bottom: 1px solid #b3b3b3;z-index: 1;">
        <tr style="">
            <td style="width:32%; padding: 0 5mm 0 0; border: none;">
                <div style="text-align: center; padding-bottom: 0mm;">
                    <h1>
                        @if (config('ipsum.reservation.etat_des_lieux.logo'))
                            <img src="{{ config('ipsum.reservation.etat_des_lieux.logo') }}" alt="{{ config('settings.nom_site') }}" width="150" style="width: 150px;">
                        @else
                            {{ config('settings.nom_site') }}
                        @endif
                    </h1>
                </div>
            </td>

            <td style="width:35%; padding: 0 5mm 0 0; border: none;">
                <div style="text-align: center; ">
                    <h2>
                        ETAT DES LIEUX DE {{ $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'DEPART' : 'RETOUR' }}<br>
                        {{ $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? $reservation->debut_at->format('d/m/Y') : $reservation->fin_at->format('d/m/Y') }}
                    </h2>
                </div>
            </td>
            <td style="width:32%; padding: 0 5mm 0 0; border: none;">

                <div style="text-align: center; padding-bottom: 0mm;">
                    <p>
                        {{ Config::get('settings.adresse') }} - {{ Config::get('settings.cp') }} {{ Config::get('settings.ville') }}<br>
                        SIRET : {{ Config::get('settings.entreprise_identification') }}<br>
                        Téléphone : {{ Config::get('settings.telephone') }} @if(config('settings.telephone_secondaire')){{'ou au '.config('settings.telephone_secondaire')}}@endif<br>
                        Email : {{ Config::get('settings.contact_email') }}
                    </p>
                </div>


            </td>
        </tr>

    </table>

    <table style="margin-top: 10px; width:100%; border-collapse:collapse;">
        <tr>
            <td style="border: none; background-color: #e6e6e6;">
                Annexe au contrat de location {{ $reservation->contrat  }} fait par {{ $inspection->admin?->firstname }} {{ $inspection->admin?->name }} ({{ $inspection->admin?->email }})
            </td>
        </tr>
    </table>

    <table style="margin-top: 10px;">
        <tr>
            <td style="width:35%; border: none;">

                <table class="tableau2">
                    <tr>
                        <th style="border:none;"></th>
                    </tr>
                    <tr>
                        <th>LOCATAIRE</th>
                    </tr>
                    <tr>
                        <td>
                            <strong>Nom :</strong> {{ $reservation->nom }} - <strong>Prénom :</strong> {{ $reservation->prenom }}<br>
                            <strong>Téléphone :</strong> {{ $reservation->telephone }}<br><br>
                            <strong>Adresse :</strong> {!! $reservation->adresse && $reservation->adresse !='-' ? $reservation->adresse : '<span class="tiret">_______________________</span><br><span class="tiret">________________________________</span>' !!},<br>
                            <strong>Code Postal :</strong> {!! $reservation->cp && $reservation->cp !='-' ? $reservation->cp : '<span class="tiret"> | _ | _ | _ | _ | _ | </span>' !!},<br>
                            <strong>Ville :</strong> {!! $reservation->ville && $reservation->ville !='-' ? $reservation->ville : '<span class="tiret">__________________</span>' !!},<br>
                            <strong>Pays :</strong> {!! $reservation->cp && $reservation->cp !='-' ? $reservation->pays_nom : '<span class="tiret">__________________</span>' !!},<br><br>
                            <strong>Né le :</strong> {!! $reservation->naissance_at ? $reservation->naissance_at?->format('d/m/Y') : '<span class="tiret">__________________</span>' !!}
                            à {!! $reservation->naissance_lieu ? e($reservation->naissance_lieu) : '<span class="tiret">__________________</span>' !!}<br>
                            <strong>N° de permis :</strong> {!! $reservation->permis_numero ? e($reservation->permis_numero) : '<span class="tiret">______________</span>' !!}
                            {{ _('délivré le') }} {!! $reservation->permis_at ? $reservation->permis_at?->format('d/m/Y') : '<span class="tiret">____________</span>' !!}<br>
                            {{ _('par') }} {!! $reservation->permis_delivre ? e($reservation->permis_delivre) : '<span class="tiret">__________________</span>' !!}<br>
                        </td>
                    </tr>
                </table>

                @if($reservation->conducteurs->count())
                    <table class="tableau2" style="margin-top: 10px;">
                        <tr>
                            <th style="width: 49%">CONDUCTEUR ADDITIONNEL</th>
                        </tr>
                        <tr>
                            <td>

                                @foreach($reservation->conducteurs as $conducteur)
                                    <strong>Nom :</strong> {{ $conducteur->nom }} - <strong>Prénom :</strong> {{ $conducteur->prenom }}<br>
                                    <strong>Date de naissance :</strong> {{ $conducteur->naissance_at }}<br>
                                    <strong>Lieu de naissance :</strong> {{ $conducteur->naissance_lieu }}<br>
                                    <strong>Numéro de permis :</strong> {{ $conducteur->permis_numero }}<br>
                                    <strong>Permis délivré le :</strong> {{ $conducteur->permis_at }}<br>
                                    <strong>Permis délivré par :</strong> {{ $conducteur->permis_delivre }}<br>
                                    <hr>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                @endif

                <table class="tableau2" style="margin-top: 10px;">
                    <tr>
                        <th style="width: 49%">VEHICULE RÉSERVÉ</th>
                    </tr>
                    <tr>
                        <td>
                            <strong>{{ _('Catégorie') }} {{ $reservation->categorie_nom }}</strong><br>
                            @if ($reservation->vehicule)
                                <strong>{{ _('Marque et modéle') }} :</strong> {{ $reservation->vehicule->marque_modele }}<br>
                                <strong>{{ _('Immatriculation') }} :</strong> {{ $reservation->vehicule->immatriculation }}<br>
                            @endif
                            <strong>Durée :</strong> {{ $reservation->nb_jours }} jour(s)<br>
                            <strong>Départ :</strong> {{ $reservation->debut_lieu_nom }}, le {{ $reservation->debut_at->format('d/m/Y') }} {{ _('à') }} {{ $reservation->debut_at->format('H\hi') }}<br>
                            @if($reservation->custom_fields->vol) Numéro de vol : {{ $reservation->custom_fields->vol }}<br> @endif
                            <strong>Retour :</strong> {{ $reservation->fin_lieu_nom }}, le {{ $reservation->fin_at->format('d/m/Y') }} {{ _('à') }} {{ $reservation->fin_at->format('H\hi') }}<br>
                        </td>
                    </tr>
                </table>

            </td>
            <td style="width:65%; border: none;">

                <table class="tableau2" style="">
                    <tr>
                        <th style="width: 49%">DEPART</th>
                        @if($inspection->type_id != \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID )
                            <th style="width: 49%">RETOUR</th>
                        @endif
                    </tr>
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td>Km compteur</td>
                                    <td>{{ $reservation->inspection_initiale?->kilometrage }} km</td>
                                </tr>
                                <tr>
                                    <td>Carburant</td>
                                    <td>{{ $reservation->inspection_initiale?->carburant }}/8</td>
                                </tr>
                                @foreach($checklists as $item)
                                    <tr>
                                        <td>{{ $item->nom }}</td>
                                        <td>
                                            <input style="" type="checkbox" {{ in_array($item->id, ($reservation->inspection_initiale ? $reservation->inspection_initiale->checklists?->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2" style="height: 110px">Observations : {!!  $reservation->inspection_initiale?->observations !!}</td>
                                </tr>
                            </table>
                        </td>
                        @if($inspection->type_id != \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID )
                            <td>
                                <table>
                                    <tr>
                                        <td>Km compteur</td>
                                        <td>{{ $reservation->inspection_finale?->kilometrage }} km</td>
                                    </tr>
                                    <tr>
                                        <td>Carburant</td>
                                        <td>{{ $reservation->inspection_finale?->carburant }}/8</td>
                                    </tr>
                                    @foreach($checklists as $item)
                                        <tr>
                                            <td>{{ $item->nom }}</td>
                                            <td>
                                                <input style="" type="checkbox" {{ in_array($item->id, ($reservation->inspection_finale ? $reservation->inspection_finale->checklists->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2" style="height: 110px">Observations : {!! $reservation->inspection_finale?->observations !!}</td>
                                    </tr>
                                </table>
                            </td>
                        @endif
                    </tr>
                    @if($inspection->type_id != \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID )
                    <tr>
                        <td colspan="2" style="border: none;"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border: none; background-color: #e6e6e6;">
                            @php
                                $count = $reservation->inspection_finale?->dommages->count() ?? 0;
                            @endphp
                            <strong>{{ $count }} dommage{{ $count > 1 ? 's' : '' }} déclaré{{ $count > 1 ? 's' : '' }}</strong>
                        </td>
                    </tr>
                    @endif
                </table>


            </td>
        </tr>
    </table>

    <div style="page-break-before: always; margin-top: 10px;">
    <table class="tableau2" style="margin-top:10px;margin-bottom:10px;">
        <tr>
            <th colspan="5" class="section-title">DOMMAGES (au départ)</th>
        </tr>
    </table>

    <div>
        <table style="width:100%; border-collapse:collapse; padding-bottom: 2mm; border-bottom: 1px solid #b3b3b3;">
            @php
                $allDommages = collect();

                // Dommages du véhicule sauf ceux de cette inspection et de l’inspection initiale
                if ($reservation->vehicule?->dommages && $inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID) {
                    $allDommages = $allDommages->merge(
                        $reservation->vehicule->dommages->filter(function ($d) use ($inspection, $reservation) {
                            return $d->inspection->id != $inspection->id && $d->inspection->id != $reservation->inspection_initiale->id;
                        })
                    );
                }

                // Dommages de l’inspection initiale
                if ($reservation->inspection_initiale?->dommages->count()) {
                    $allDommages = $allDommages->merge($reservation->inspection_initiale->dommages);
                }
            @endphp

            @if($allDommages->count())
                @foreach($allDommages->chunk(3) as $chunk)
                    <tr>
                        @foreach($chunk as $dommage)
                            <td style="
                            width: 33.33%;
                            vertical-align: top;
                            padding: 5px;
                            border: none;
                        ">
                                @include('IpsumReservation::reservation.documents._dommage')
                            </td>
                        @endforeach

                        {{-- complète les cellules vides --}}
                        @for($i = $chunk->count(); $i < 3; $i++)
                            <td style="width: 33.33%; padding: 5px;border: none;"></td>
                        @endfor
                    </tr>
                @endforeach
            @else
                <tr>
                    <td style="text-align:center; font-style:italic; padding:10px;">
                        Aucun dommage constaté
                    </td>
                </tr>
            @endif
        </table>
    </div>

    @if($inspection->type_id != \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID )

        <table class="tableau2" style="margin-top:10px;">
            <tr>
                <th colspan="5" class="section-title">DOMMAGES (au retour)</th>
            </tr>
        </table>

        <div>
            <table style="width:100%; border-collapse:collapse; padding-bottom: 2mm; border-bottom: 1px solid #b3b3b3;">
                @php
                    $allDommages = $reservation->inspection_finale?->dommages;
                @endphp

                @if($allDommages->count())
                    @foreach($allDommages->chunk(3) as $chunk)
                        <tr>
                            @foreach($chunk as $dommage)
                                <td style="
                            width: 33.33%;
                            vertical-align: top;
                            padding: 5px;
                            border: none;
                        ">
                                    @include('IpsumReservation::reservation.documents._dommage')
                                </td>
                            @endforeach

                            {{-- complète les cellules vides --}}
                            @for($i = $chunk->count(); $i < 3; $i++)
                                <td style="width: 33.33%; padding: 5px;border: none;"></td>
                            @endfor
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="text-align:center; font-style:italic; padding:10px;">
                            Aucun dommage constaté
                        </td>
                    </tr>
                @endif
            </table>
        </div>

    @endif
    </div>

    @php
        $photos_depart = $reservation->inspection_initiale?->images()->groupe('photos')->get();
        $photos_retour = $reservation->inspection_finale?->images()->groupe('photos')->get();
    @endphp

    <div style="page-break-before: always; margin-top: 10px;">
    @if($photos_depart->count())
            <table class="tableau2" style="margin-top:10px; width:100%; border-collapse:collapse;">
                <tr>
                    <th colspan="4" class="section-title">
                        PHOTOS (au départ)
                    </th>
                </tr>

                @foreach($photos_depart->chunk(4) as $chunk)
                    <tr style="">
                        @foreach($chunk as $media)
                            <td style="
                            width: 33.33%;
                            vertical-align: top;
                            padding: 5px;
                            border: none;
                        ">
                                @include('IpsumReservation::reservation.documents._photo')
                            </td>
                        @endforeach

                        {{-- Si la dernière ligne a moins de 4 images, on complète les colonnes vides --}}
                        @for($i = $chunk->count(); $i < 4; $i++)
                            <td style="width:25%; padding:6px;border: none;"></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
    @endif

    @if($inspection->type_id != \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID )

        @if($photos_retour->count())
            <table class="tableau2" style="margin-top:10px; width:100%; border-collapse:collapse;">
                <tr>
                    <th colspan="4" class="section-title">
                        PHOTOS (au retour)
                    </th>
                </tr>

                @foreach($photos_retour->chunk(4) as $chunk)
                    <tr style="">
                        @foreach($chunk as $media)
                            <td style="
                            width: 33.33%;
                            vertical-align: top;
                            padding: 5px;
                            border: none;
                        ">
                                @include('IpsumReservation::reservation.documents._photo')
                            </td>
                        @endforeach

                        {{-- Si la dernière ligne a moins de 4 images, on complète les colonnes vides --}}
                        @for($i = $chunk->count(); $i < 4; $i++)
                            <td style="width:25%; padding:6px;border: none;"></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        @endif
    @endif
    </div>



    <!-- PAGE DÉDIÉE POUR SIGNATURES : FORCER UNE NOUVELLE PAGE -->
    <div style="page-break-before: always; margin-top: 10px;">
        <table style="margin-top: 10px; width:100%; border-collapse:collapse;">
            <tr>
                <td style="border: none; background-color: #e6e6e6;">
                    En signant le preneur accepte les conditions générales de location fournies en annexe
                </td>
            </tr>
        </table>

        <table style="margin-top: 6mm; width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; border: none; text-align: center">
                    <h3>SIGNATURE CLIENT</h3>
                </td>
                <td style="width:50%; border: none; text-align: center">
                    <h3>SIGNATURE LOUEUR</h3>
                </td>
            </tr>
            <tr>
                <td style="border:none;text-align: center; height:120px; vertical-align:top;">
                    @if($inspection->locataire_signature)
                        <img src="{{ $inspection->locataire_signature }}" alt="Signature client" style="width:200px; height:80px; border:1px solid #000;">
                        <p style="margin-top: 10px;">Signé le : {{ $inspection->locataire_signature_at->format('d/m/Y à H:i') }}</p>
                    @else
                        <div style="display:inline-block; width:195px; height:90px; border:1px solid #000;"></div>
                    @endif
                </td>
                <td style="border:none;text-align: center; height:120px; vertical-align:top;">
                    @if($inspection->agent_signature)
                        <img src="{{ $inspection->agent_signature }}" alt="Signature agent" style="width:200px; height:80px; border:1px solid #000;">
                        <p style="margin-top: 10px;">Signé le : {{ $inspection->agent_signature_at->format('d/m/Y à H:i') }}</p>
                    @else
                        <div style="display:inline-block; width:195px; height:90px; border:1px solid #000;"></div>
                    @endif
                </td>
            </tr>
        </table>
    </div>


</div>

</body>
</html>
