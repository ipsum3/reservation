<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>
        Devis
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

        th {
            background-color: {{ config('ipsum.reservation.contrat.couleur') }};
            color: #fff;

            text-align: center;
            font-weight: normal;
        }

        td.total {
            background-color: #ccc;
            font-weight: bold;
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
    </style>
</head>
<body>
<div>
    <table style="padding-bottom: 5mm;">
        <tr>
            <td style="width:50%; padding: 0 5mm 0 0; border: none;">

                <div style="text-align: center; padding-bottom: 10mm;">
                    <h1>
                        @if (config('ipsum.reservation.devis.logo'))
                            <img src="{{ config('ipsum.reservation.devis.logo') }}" alt="{{ config('settings.nom_site') }}" width="150" style="width: 150px;">
                        @else
                            {{ config('settings.nom_site') }}
                        @endif
                    </h1>
                    <h2>Devis</h2>
                    <p>
                        {{ _('Date de création') }} : {{ \Carbon\Carbon::now()->format('d/m/Y') }}<br/>
                        @if( config('settings.devis.date_expiration') )
                            {{ _('Date d\'expiration') }} : {{ \Carbon\Carbon::now()->addDays(config('settings.devis.date_expiration'))->format('d/m/Y') }}
                        @endif
                    </p>
                    <div style="border: 1px solid #acacac; padding: 10px 20px;font-size: 14px; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">
                        @if( config('settings.societe') )
                            {{ _('Société') }} {{ config('settings.societe') }}<br>
                        @endif
                        {{ config('settings.nom_site') }}<br>
                        {{ config('settings.adresse') }}<br>
                        {{ config('settings.cp') }} {{ config('settings.ville') }}<br>
                        @if( config('settings.telephone') )
                            Tél : {{ config('settings.telephone') }}<br>
                        @endif
                        @if( config('settings.contact_email') )
                            {{ config('settings.contact_email') }}
                        @endif
                    </div>
                </div>
            </td>

            <td style="width:50%; padding: 0; border: none;">
                <table>
                    <tr>
                        <th style="width: 100%; text-align: left;">{{ _('Dates et lieux') }}</th>
                    </tr>
                    <tr>
                        <td>
                            <strong>{{ _('Départ') }} :</strong><br/>
                            {{ _('Le') }} {{ $reservation->debut_at->format('d/m/Y') }} {{ _('à') }} {{ $reservation->debut_at->format('H\hi') }}<br />
                            {{ _('à') }} {{ $reservation->debut_lieu_nom }}<br />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>{{ _('Retour') }} :</strong><br/>
                            {{ _('Le') }} {{ $reservation->fin_at->format('d/m/Y') }} {{ _('à') }} {{ $reservation->fin_at->format('H\hi') }}<br />
                            {{ _('à') }} {{ $reservation->fin_lieu_nom }}<br />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table>
        <thead>
        <tr>
            <th style="width: 15%;text-align: center;">{{ _('Référence') }}</th>
            <th style="width: 45%;text-align: center;">{{ _('Désignation') }}</th>
            <th style="width: 15%;text-align: center;">{{ _('Total TTC') }}</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>{{ $reservation->reference }}</td>
            <td>{{ _('Location véhicule de catégorie') }} {{ $reservation->categorie_nom }}</td>
            <td style="text-align: right;">@prix($reservation->total)€</td>
        </tr>
        </tbody>
    </table>

    <table style="padding-top: 5mm;">
        <tr>
            <td style="padding: 0; border: none; padding-right: 5mm">

                <table style="margin-top: 5mm;">
                    <tbody>
                    <tr>
                        <th style="width: 35%; text-align: right;">{{ _('Condition de paiement') }}</th>
                        <td style="width: 65%;">{{ $reservation->condition->nom }}</td>
                    </tr>
                    </tbody>
                </table>

            </td>

            <td style="padding: 0; border: none;padding-top: 19px">
                <table>

                    <tbody>
                    <tr>
                        <td style="text-align: right;" class="total">{{ _('Total TTC') }}</td>
                        <td style="text-align: right;" class="total">@prix($reservation->total)€</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

</div>
<div id="footer">
    <table>
        <tr>
            <td>{{{ Config::get('settings.nom_site') }}}</td>
            <td style="text-align: right; width: 10%"><div class="page-number"></div></td>
        </tr>
    </table>
</div>
</body>
</html>
