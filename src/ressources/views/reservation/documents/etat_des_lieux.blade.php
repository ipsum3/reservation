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
            border: 0.2mm solid #ccc;
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
                        État des lieux {{ strtolower($type->nom) }}<br>
                        {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                    </h2>
                    <p>Annexe au contrat de location {{ $reservation->contrat  }}</p>
                </div>
            </td>
            <td style="width:32%; padding: 0 5mm 0 0; border: none;">

                <div style="text-align: left; padding-bottom: 0mm;">
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


    <table style="margin-top: 10px;">
        <tr>
            <td style="width:35%; border: none;">

                <table class="tableau2">
                    <tr>
                        <th>Locataire</th>
                    </tr>
                    <tr>
                        <td>
                            <strong>Nom :</strong> {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}<br>
                            <strong>Téléphone :</strong> {{ $reservation->telephone }}<br><br>
                            <strong>Adresse :</strong> {{ $reservation->adresse }} {{ $reservation->cp }} {{ $reservation->ville }} {{ $reservation->pays_nom }} <br>
                            @if ($reservation->naissance_at)
                                <strong>{{ _('Né le') }} :</strong> {{ $reservation->naissance_at->format('d/m/Y') }}
                                @if ($reservation->naissance_lieu)
                                    à {{ $reservation->naissance_lieu }}
                                @endif
                                <br>
                            @endif
                            @if ($reservation->permis_numero or $reservation->permis_at)
                                <strong>{{ _('Permis') }} :</strong>
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
                        </td>
                    </tr>
                </table>

                <table class="tableau2" style="margin-top: 10px;">
                    <tr>
                        <th style="width: 49%">Véhicule réservé</th>
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
                            <strong>Retour :</strong> {{ $reservation->fin_lieu_nom }}, le {{ $reservation->fin_at->format('d/m/Y') }} {{ _('à') }} {{ $reservation->fin_at->format('H\hi') }}<br>
                        </td>
                    </tr>
                </table>

            </td>
            <td style="width:65%; border: none;">

                <table class="tableau2" style="">
                    <tr>
                        <th style="width: 49%">Initial</th>
                        @if(!$inspection->type->is_initial )
                            <th style="width: 49%">Final</th>
                        @endif
                    </tr>
                    <tr>
                        <td>
                            <table class="tableau3">
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
                                    <td colspan="2" style="height: 60px">Observations : {!! nl2br(e($reservation->inspection_initiale?->observations)) !!}</td>
                                </tr>
                            </table>
                        </td>
                        @if(!$inspection->type->is_initial)
                            <td>
                                <table class="tableau3">
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
                                        <td colspan="2" style="height: 60px">Observations : {!! nl2br(e($reservation->inspection_finale?->observations)) !!}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            @php
                                                $count = $reservation->inspection_finale?->dommages->count() ?? 0;
                                            @endphp
                                            <strong>{{ $count }} dommage{{ $count > 1 ? 's' : '' }} déclaré{{ $count > 1 ? 's' : '' }}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </td>

                        @endif
                    </tr>
                </table>


            </td>
        </tr>
    </table>

    <div style="page-break-inside: avoid;">
        <table class="tableau2" style="margin-top:10px;margin-bottom:10px;">
            <tr>
                <th colspan="5" class="section-title">Dommage(s) (au départ)</th>
            </tr>
        </table>

        <table style="width:100%; border-collapse:collapse; padding-bottom: 2mm;">
            @php
                $allDommages = $reservation->vehicule->dommages->filter(function ($dommage) use ($inspection, $reservation) {
                    return $dommage->inspection->id != $reservation->inspection_finale?->id;
                });
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

    @if(!$inspection->type->is_initial)

        <div style="page-break-inside: avoid;">
            <table class="tableau2" style="margin-top:10px;margin-bottom:10px;">
                <tr>
                    <th colspan="5" class="section-title">Dommage(s) (au retour)</th>
                </tr>
            </table>

            <table style="width:100%; border-collapse:collapse; padding-bottom: 2mm;">
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

    <div style="page-break-inside: avoid; /*page-break-before: always;*/ margin-top: 10px;">
        <table style="margin-top: 6mm; width:100%; border-collapse:collapse;">
            <tr>
                <td style="border: none;">
                    Par ma signature, je reconnais être d'accord l'état des lieux.
                </td>
            </tr>
        </table>

        <table style="margin-top: 2mm; width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; border: none; text-align: center">
                    <h3>Signature locataire</h3>
                </td>
                <td style="width:50%; border: none; text-align: center">
                    <h3>Signature loueur</h3>
                </td>
            </tr>
            <tr>
                <td style="border:none;text-align: center; height:120px; vertical-align:top;">
                    @if($inspection->locataire_signature)
                        <img src="{{ $inspection->locataire_signature }}" alt="Signature client" style="width:200px; height:auto;">
                        <p style="margin-top: 10px;">Signé le : {{ $inspection->locataire_signature_at->format('d/m/Y à H:i') }}</p>
                    @else
                        <div style="display:inline-block; width:195px; height:90px; border:1px solid #000;"></div>
                    @endif
                </td>
                <td style="border:none;text-align: center; height:120px; vertical-align:top;">
                    @if($inspection->agent_signature)
                        <img src="{{ $inspection->agent_signature }}" alt="Signature agent" style="width:200px; height:auto;">
                        <p style="margin-top: 10px;">Signé le : {{ $inspection->agent_signature_at->format('d/m/Y à H:i') }} par {{ $inspection->admin?->firstname }} {{ $inspection->admin?->name }}</p>
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
