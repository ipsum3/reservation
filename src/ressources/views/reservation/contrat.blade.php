<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>
        @yield('title')
    </title>

    <style type="text/css">
        @page {
            margin: 1cm;
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 13px;
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
            background-color: {{ config('ipsum.reservation.contrat.couleur') }};
            color: #fff;

            text-align: center;
            font-weight: normal;
        }

        .tableau2 {
            font-size: 11px;
        }

        .tableau2 th {
            padding: 2px 0;

            text-align: center;
            font-weight: normal;
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
    </style>
</head>
<body>
<div id="footer">
    <table>
        <tr>
            <td>{{ config('settings.nom_site') }} - {{ Config::get('settings.entreprise_identification') }}</td>
            <td style="text-align: right; width: 10%"><div class="page-number"></div></td>
        </tr>
    </table>
</div>
<div>
    <table style="padding-bottom: 5mm;">
        <tr>
            <td style="width:50%; padding: 0 5mm 0 0; border: none;">

                <div style="text-align: center; padding-bottom: 0mm;">
                    <h1>
                        @if (config('ipsum.reservation.contrat.logo'))
                            <img src="{{ config('ipsum.reservation.contrat.logo') }}" alt="{{ config('settings.nom_site') }}" width="150" style="width: 150px;">
                        @else
                            {{ config('settings.nom_site') }}
                        @endif
                    </h1>
                    <p>
                        {{ Config::get('settings.adresse') }} - {{ config('settings.cp') }} {{ Config::get('settings.ville') }} - France<br>
                        Téléphone : {{ config('settings.telephone') }}
                    </p>
                    <h2>Contrat de location {{ $reservation->contrat }}</h2>
                </div>


            </td>

            <td style="width:50%; padding: 0; border: none;">
                <table class="tableau1" style="width: 100%">
                    <tr>
                        <th>N° contrat</th>
                        <th>N° réservation</th>
                        <th>Date</th>
                    </tr>
                    <tr>
                        <td>{{ $reservation->contrat }}</td>
                        <td>{{ $reservation->reference }}</td>
                        <td>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</td>
                    </tr>
                </table>

                <table class="tableau1" style="margin-top: 30px">
                    <tr>
                        <th style="width: 100%; text-align: left;">Locataire</th>
                    </tr>

                    <tr>
                        <td style="text-transform: uppercase;">
                            {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}<br />
                            {{ $reservation->adresse }}<br />
                            {{ $reservation->cp }} {{ $reservation->ville }} {{ $reservation->pays_nom }}<br />
                            {{ $reservation->telephone }}<br />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


    <table>
        <tr>
            <td style="width:50%; border: none;">


                <table class="tableau2">
                    <tr>
                        <th>Conducteur 1</th>
                    </tr>
                    <tr>
                        <td>
                            {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}<br />
                            {{ _('Né le') }} {!! $reservation->naissance_at ? $reservation->naissance_at->format('d/m/Y') : '<span class="tiret">__________________</span>' !!}
                            à {!! $reservation->naissance_lieu ? e($reservation->naissance_lieu) : '<span class="tiret">__________________</span>' !!}<br>
                            {{ _('Permis') }} {{ _('n°') }} {!! $reservation->permis_numero ? e($reservation->permis_numero) : '<span class="tiret">______________</span>' !!}
                            {{ _('délivré le') }} {!! $reservation->permis_at ? $reservation->permis_at->format('d/m/Y') : '<span class="tiret">____________</span>' !!}<br>
                            {{ _('par') }} {!! $reservation->permis_delivre ? e($reservation->permis_delivre) : '<span class="tiret">__________________</span>' !!}<br>
                        </td>
                    </tr>
                </table>

                <table class="tableau2" style="margin-top: 10px">
                    <tr>
                        <th>Conducteur 2</th>
                    </tr>
                    <tr>
                        <td>
                            <span class="tiret">__________________________________________</span><br />
                            {{ _('Né le') }} <span class="tiret">__________________</span> à <span class="tiret">__________________</span><br>
                            {{ _('Permis') }} {{ _('n°') }} <span class="tiret">______________</span> {{ _('délivré le') }} <span class="tiret">____________</span><br>
                            {{ _('par') }} <span class="tiret">__________________</span><br>
                        </td>
                    </tr>
                </table>

                <table class="tableau2" style="margin-top: 10px">
                    <tr>
                        <th>Conducteur 3</th>
                    </tr>
                    <tr>
                        <td>
                            <span class="tiret">__________________________________________</span><br />
                            {{ _('Né le') }} <span class="tiret">__________________</span> à <span class="tiret">__________________</span><br>
                            {{ _('Permis') }} {{ _('n°') }} <span class="tiret">______________</span> {{ _('délivré le') }} <span class="tiret">____________</span><br>
                            {{ _('par') }} <span class="tiret">__________________</span><br>
                        </td>
                    </tr>
                </table>

                @if ($reservation->prestations)
                    <table class="tableau2" style="margin-top: 10px">
                        <tr>
                            <th>Prestation{{ count($reservation->prestations) > 1 ? 's' : '' }}</th>
                        </tr>
                        <tr>
                            <td>
                                <table class="tableau3">
                                    @foreach ($reservation->prestations as $prestation)
                                        <tr>
                                            <td>{{ $prestation->quantite }} {{ strtolower($prestation->nom) }} {{ !empty($prestation->choix) ? '('.$prestation->choix.')' : '' }}</td>
                                            <td align="right">
                                                {{ $prestation->tarif_libelle }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                @endif

            </td>
            <td style="width:50%; border: none;">
                <table class="tableau2">
                    <tr>
                        <th>Véhicule</th>
                    </tr>
                    <tr>
                        <td>
                            {{ _('Catégorie') }} {{ $reservation->categorie_nom }}<br>
                            @if ($reservation->vehicule)
                                {{ _('Marque et modéle') }} : {{ $reservation->vehicule->marque_modele }}<br>
                                {{ _('Immatriculation') }} : {{ $reservation->vehicule->immatriculation }}<br>
                            @endif
                        </td>
                    </tr>
                </table>

                <table class="tableau2" style="margin-top: 10px">
                    <tr>
                        <th>Période</th>
                    </tr>
                    <tr>
                        <td>
                            {{ _('Départ') }} :<br>
                            {{ $reservation->debut_lieu_nom }}
                            {{ _('le') }} {{ $reservation->debut_at->format('d/m/Y') }} {{ _('à') }} {{ $reservation->debut_at->format('H\hi') }}
                            <br><br>
                            {{ _('Retour') }} :<br>
                            {{ $reservation->fin_lieu_nom }}
                            {{ _('le') }} {{ $reservation->fin_at->format('d/m/Y') }} {{ _('à') }} {{ $reservation->fin_at->format('H\hi') }}
                            <br><br>
                            Nombre de jours : {{ $reservation->nb_jours }}
                        </td>
                    </tr>
                </table>

                @if ($reservation->promotions->count())
                    <table class="tableau2" style="margin-top: 10px">
                        <tr>
                            <th>Promotion{{ $reservation->promotions->count() > 1 ? 's' : '' }}</th>
                        </tr>
                        <tr>
                            <td>

                                <table class="tableau3">
                                    @foreach ($reservation->promotions as $promotion)
                                        <tr>
                                            <td>{{ _('Offre') }} {{ strtolower($promotion->nom) }}</td>
                                            <td align="right">
                                                -@prix($promotion->reduction)&nbsp;€
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>

                            </td>
                        </tr>
                    </table>
                @endif

                <table class="tableau2" style="margin-top: 10px">
                    <tr>
                        <th>Tarification</th>
                    </tr>
                    <tr>
                        <td>

                            <table class="tableau3">
                                @if ($reservation->franchise)
                                    <tr>
                                        <td>{{ _('Franchise') }}</td>
                                        <td align="right">
                                            @prix($reservation->franchise)&nbsp;€
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>{{ _('Total (TTC)') }}</td>
                                    <td align="right">
                                        @prix($reservation->total)&nbsp;€
                                    </td>
                                </tr>
                                @if (!$reservation->is_payed)
                                    <tr>
                                        <td>{{ _('Reste à régler') }}</td>
                                        <td align="right">
                                            @prix($reservation->total - $reservation->montant_paye)&nbsp;€
                                        </td>
                                    </tr>
                                @endif
                            </table>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="border: none;">

            </td>
        </tr>
    </table>


    <table>
        <tr>
            <td style="border: none">
                Par ma signature, je reconnais être d'accord avec le contrat avec le montant estimé de la location. Je reconnais avoir lu et approuvé les conditions de location figurant au verso de mon contrat de location.

                <div style="float: right; margin-top: 5mm">
                    A&nbsp;<span style="display: inline-block; width: 150px;"></span>,le<br>
                    Signature, précédée de la mention "Lu et approuvé"
                </div>
            </td>


        </tr>
    </table>





    @if ($cgl)
        <div class="page_break"></div>

        <div style="font-size: 8px">
            <h2>{{ $cgl->titre }}</h2>
            {!! $cgl->texte !!}
        </div>
    @endif



</div>
</body>
</html>
