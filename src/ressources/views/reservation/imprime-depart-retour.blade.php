<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>
        Départs et retours
    </title>

    <style type="text/css">
        @page {
            margin: 1cm;
        }
        .page-number:before {
            content: "Page " counter(page);
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 11px;
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
            <td style="text-align: right; width: 10%"><div class="page-number"></div></td>
        </tr>
    </table>
</div>
<div>

    @if( count( $departs ) )
        <table style="padding-bottom: 5mm;">
            <tr>
                <td style="width:50%; padding: 0 5mm 0 0; border: none;">

                    <div style="text-align: center; padding-bottom: 0mm;">
                        <h1>
                            Départs du {{ $date->format('d/m/Y') }}
                        </h1>
                    </div>

                </td>
            </tr>
        </table>

        <table>
            <tr style="font-weight: bold;text-align: center">
                <td style="width: 30px">Heure</td>
                <td>Véhicule</td>
                <td>Lieu</td>
                <td>Client</td>
                <td>Contrat</td>
                <td>Vol</td>
                <td>Prestation</td>
            </tr>
            @foreach($departs as $reservation)
                <tr>
                    <td>
                        {{ $reservation->debut_at->format('H:i') }}
                    </td>
                    <td>
                        @if ($reservation->vehicule)
                            {{ $reservation->vehicule->marque_modele }}<br>
                            {{ $reservation->vehicule->immatriculation }}<br>
                        @endif
                        Catégorie {{ $reservation->categorie_nom }}
                    </td>
                    <td>
                        {{ $reservation->debut_lieu_nom }}
                    </td>
                    <td>
                        @if ($reservation->client)
                            {{ $reservation->prenom }} {{ $reservation->nom }}<br/>
                        @else
                            {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}<br/>
                        @endif
                        @if( $reservation->telephone )
                            Tél : {{ $reservation->telephone }}
                        @endif
                    </td>
                    <td>
                        @if( $reservation->contrat )
                            Réf : {{ $reservation->contrat }}<br/>
                        @endif
                        <x-reservation::reste_a_payer total="{{ $reservation->total }}"  montant_paye="{{ $reservation->montant_paye }}" /><br>
                        {{ $reservation->condition ? $reservation->condition->nom : '' }}
                    </td>
                    <td>
                        @if ($reservation->custom_fields->vol)
                            {{ $reservation->custom_fields->vol }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($reservation->prestations->count())
                            @foreach ($reservation->prestations as $prestation)
                                - {{ $prestation->quantite }} {{ strtolower($prestation->nom) }} {{ !empty($prestation->choix) ? '('.$prestation->choix.')' : '' }} <br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    @if( count( $retours ) )
        <table class="{{ count( $departs ) ? 'page_break' : '' }}" style="margin-top: 20px;padding-bottom: 5mm;">
            <tr>
                <td style="width:50%; padding: 0 5mm 0 0; border: none;">

                    <div style="text-align: center; padding-bottom: 0mm;">
                        <h1>
                            Retours du {{ $date->format('d/m/Y') }}
                        </h1>
                    </div>

                </td>
            </tr>
        </table>

        <table>
            <tr style="font-weight: bold;text-align: center">
                <td style="width: 30px">Heure</td>
                <td>Véhicule</td>
                <td>Lieu</td>
                <td>Client</td>
                <td>Contrat</td>
                <td>Vol</td>
                <td>Prestation</td>
            </tr>
            @foreach($retours as $reservation)
                <tr>
                    <td>{{ $reservation->fin_at->format('H:i') }}</td>
                    <td>
                        @if ($reservation->vehicule)
                            {{ $reservation->vehicule->marque_modele }}<br>
                            {{ $reservation->vehicule->immatriculation }}<br>
                        @endif
                        Catégorie {{ $reservation->categorie_nom }}
                    </td>
                    <td>
                        {{ $reservation->fin_lieu_nom }}
                    </td>
                    <td>
                        @if ($reservation->client)
                            {{ $reservation->prenom }} {{ $reservation->nom }}<br/>
                        @else
                            {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}<br/>
                        @endif
                        @if( $reservation->telephone )
                            Tél : {{ $reservation->telephone }}
                        @endif
                    </td>
                    <td>
                        @if( $reservation->contrat )
                            Réf : {{ $reservation->contrat }}<br/>
                        @endif
                        <x-reservation::reste_a_payer total="{{ $reservation->total }}"  montant_paye="{{ $reservation->montant_paye }}" /><br>
                        {{ $reservation->condition ? $reservation->condition->nom : '' }}
                    </td>
                    <td>
                        @if ($reservation->custom_fields->vol)
                            {{ $reservation->custom_fields->vol }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($reservation->prestations->count())
                            @foreach ($reservation->prestations as $prestation)
                                * {{ $prestation->quantite }} {{ strtolower($prestation->nom) }} {{ !empty($prestation->choix) ? '('.$prestation->choix.')' : '' }} <br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

</div>
</body>
</html>
