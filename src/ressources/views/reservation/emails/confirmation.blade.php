<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Confirmation de la réservation</title>
    <style type="text/css">

        /* Utilisation de emailframe.work : https://github.com/g13nn/Email-Framework */

        /* Outlines the grids, remove when sending
        table td { border: 1px solid cyan; }*/

        /* CLIENT-SPECIFIC STYLES */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }

        /* RESET STYLES */
        img { border: 0; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] { margin: 0 !important; }

        /* MEDIA QUERIES */
        @media all and (max-width:639px){
            .wrapper{ width:320px!important; padding: 0 !important; }
            .container{ width:300px!important;  padding: 0 !important; }
            .mobile{ width:300px!important; display:block!important; padding: 0 !important; }
            .img{ width:100% !important; height:auto !important; }
            *[class="mobileOff"] { width: 0px !important; display: none !important; }
            *[class*="mobileOn"] { display: block !important; max-height:none !important; }
        }

    </style>

    <style>
        .table th,
        .table td {
            padding: 5px 0;
        }
        .table th {
            text-align: left;
            padding-right: 5px;
        }
    </style>

    @php($couleur = config('ipsum.reservation.confirmation.couleur'))
</head>
<body style="margin:0; padding:0;">

<span style="display: block; width: 640px !important; max-width: 640px; height: 1px" class="mobileOff"></span>

<center>
    <table width="100%" border="0" cellspacing="0" cellpadding="10">
        <tr>
            <td align="center" valign="top">
                <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                    <tr>
                        <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <table width="600" cellpadding="0" cellspacing="0" border="0" class="container" style="font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">
                                <tr>
                                    <td width="300" class="mobile" align="left" valign="top" style="font-size: 25px; font-weight: bold">
                                        @if (config('ipsum.reservation.confirmation.logo'))
                                            <img src="{{ config('ipsum.reservation.confirmation.logo') }}" alt="{{ config('settings.nom_site') }}" width="250" style="width: 250px;">
                                        @else
                                            {{ config('settings.nom_site') }}
                                        @endif
                                    </td>
                                    <td width="400" class="mobile" align="right" valign="top">
                                        <h1 style="font-size: 18px;color: #8c8c8c;">
                                            {{ _('Confirmation de réservation') }}<br>
                                        </h1>
                                        <div style="padding: 0 10px; font-size: 14px; font-weight: bold; color: white; background: {{ $couleur }};">
                                            {{ _('Référence') }} : <span style="padding: 10px 0; font-size: 30px;">{{ $reservation->reference }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size:10px; line-height:10px;">&nbsp;</div>
                <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                    <tr>
                        <td height="20" style="font-size:20px; line-height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                <tr>
                                    <td align="center" valign="top">
                                        <table width="350" cellpadding="0" cellspacing="0" border="0" class="container">
                                            <tr>
                                                <td align="center" valign="top">
                                                    <div style="border: 1px solid #acacac; padding: 10px 20px;font-size: 14px; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">
                                                        Réservation {{ $reservation->modalite ? strtolower($reservation->modalite->nom) : '' }} {{ $reservation->etat ? strtolower($reservation->etat->nom) : '' }}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top" style="font-size: 12px; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">
                                        {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}, {{ _("nous vous remercions d'avoir choisi") }} {{  config('settings.nom_site') }}. {{ _('Votre réservation a bien été enregistrée sous la référence') }} {{ $reservation->reference }}. {{ _("Veuillez vous présenter à l'agence de départ muni de ce document.") }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="20" style="font-size:20px; line-height:20px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size:10px; line-height:10px;">&nbsp;</div>
                <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                    <tr>
                        <td align="center" valign="top">
                            <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                <tr>
                                    <td width="300" class="mobile" align="center" valign="top">
                                        <h2 style="margin: 12px 0; padding: 5px 10px; font-size: 15px; line-height: 20px; text-align: center; background-color: {{ $couleur }}; color: white; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">{{ _('Informations départ') }}</h2>
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table" style="font-size: 12px; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">
                                            <tr>
                                                <th valign="top">{{ _('Date') }}</th>
                                                <td valign="top">{{ _('Le') }} {{ $reservation->debut_at->format('d/m/Y') }} {{ _('à') }} {{ $reservation->debut_at->format('H\hi') }}</td>
                                            </tr>
                                            <tr>
                                                <th valign="top">{{ _('Lieu') }}</th>
                                                <td valign="top">{{ $reservation->debut_lieu_nom }}</td>
                                            </tr>
                                            @if ($reservation->lieuDebut)
                                                <tr>
                                                    <th valign="top">{{ _('Téléphone') }}</th>
                                                    <td valign="top">{{ $reservation->lieuDebut->telephone }}</td>
                                                </tr>
                                                <tr>
                                                    <th valign="top">{{ _('Adresse') }}</th>
                                                    <td valign="top">{!! nl2br(e($reservation->lieuDebut->adresse)) !!}</td>
                                                </tr>
                                                @if($reservation->lieuDebut->instruction)
                                                    <tr>
                                                        <th valign="top">{{ _('Instruction') }}</th>
                                                        <td valign="top">{!! nl2br(e($reservation->lieuDebut->instruction)) !!}</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th valign="top">{{ _('Horaires') }}</th>
                                                    <td valign="top">{!! nl2br(e($reservation->lieuDebut->horaires_txt)) !!}</td>
                                                </tr>
                                            @endif
                                            @if ($reservation->observation)
                                                <tr>
                                                    <th valign="top">{{ _('Observations') }}</th>
                                                    <td valign="top">{!! nl2br(e($reservation->observation)) !!}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                    <td width="300" class="mobile" align="center" valign="top" style="border-left: 5px solid white;">
                                        <h2 style="margin: 12px 0; padding: 5px 10px; font-size: 15px; line-height: 20px; text-align: center; background-color: {{ $couleur }}; color: white; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">{{ _('Informations retour') }}</h2>
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table" style="font-size: 12px; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">
                                            <tr>
                                                <th valign="top">{{ _('Date') }}</th>
                                                <td valign="top">{{ _('Le') }} {{ $reservation->fin_at->format('d/m/Y') }} {{ _('à') }} {{ $reservation->fin_at->format('H\hi') }}</td>
                                            </tr>
                                            <tr>
                                                <th valign="top">{{ _('Lieu') }}</th>
                                                <td valign="top">{{ $reservation->fin_lieu_nom }}</td>
                                            </tr>
                                            @if ($reservation->lieuFin)
                                                <tr>
                                                    <th valign="top">{{ _('Téléphone') }}</th>
                                                    <td valign="top">{{ $reservation->lieuFin->telephone }}</td>
                                                </tr>
                                                <tr>
                                                    <th valign="top">{{ _('Adresse') }}</th>
                                                    <td valign="top">{!! nl2br(e($reservation->lieuFin->adresse)) !!}</td>
                                                </tr>
                                                <tr>
                                                    <th valign="top">{{ _('Horaires') }}</th>
                                                    <td valign="top">{!! nl2br(e($reservation->lieuFin->horaires_txt)) !!}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size:10px; line-height:10px;">&nbsp;</div>
                <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                    <tr>
                        <td align="center" valign="top">
                            <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                <tr>
                                    <td width="300" class="mobile" align="center" valign="top">
                                        <h2 style="margin: 12px 0; padding: 5px 10px; font-size: 15px; line-height: 20px; text-align: center; background-color: {{ $couleur }}; color: white; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">{{ _('Informations conducteur') }}</h2>
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table" style="font-size: 12px; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">
                                            <tr>
                                                <th valign="top">{{ _('Nom') }}</th>
                                                <td valign="top">{{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}</td>
                                            </tr>
                                            <tr>
                                                <th valign="top">{{ _('Email') }}</th>
                                                <td valign="top">{{ $reservation->email }}</td>
                                            </tr>
                                            <tr>
                                                <th valign="top">{{ _('Téléphone') }}</th>
                                                <td valign="top">{{ $reservation->telephone }}</td>
                                            </tr>
                                            @if ($reservation->adresse or $reservation->cp or $reservation->ville or $reservation->pays_nom)
                                                <tr>
                                                    <th valign="top">{{ _('Adresse') }}</th>
                                                    <td valign="top">{{ $reservation->adresse }}<br>{{ $reservation->cp }} - {{ $reservation->ville }} - {{ $reservation->pays_nom }}</td>
                                                </tr>
                                            @endif
                                            @if ($reservation->naissance_at)
                                                <tr>
                                                    <th valign="top">{{ _('Né le') }}</th>
                                                    <td valign="top">{{ $reservation->naissance_at->format('d/m/Y') }}</td>
                                                </tr>
                                            @endif
                                            @if ($reservation->permis_numero or $reservation->permis_at)
                                                <tr>
                                                    <th valign="top">{{ _('Permis') }}</th>
                                                    <td valign="top">
                                                        @if ($reservation->permis_numero)
                                                            {{ _('n°') }}{{ $reservation->permis_numero }}
                                                        @endif
                                                        @if ($reservation->permis_at)
                                                            {{ _('délivré le') }} {{ $reservation->permis_at->format('d/m/Y') }}
                                                        @endif
                                                        @if ($reservation->permis_delivre)
                                                            {{ _('par') }} {{ $reservation->permis_delivre }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                    <td width="300" class="mobile" align="center" valign="top" style="border-left: 5px solid white;">
                                        <h2 style="margin: 12px 0; padding: 5px 10px; font-size: 15px; line-height: 20px; text-align: center; background-color: {{ $couleur }}; color: white; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">{{ _('Informations véhicule') }}</h2>
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table" style="font-size: 12px; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">
                                            <tr>
                                                <th valign="top">{{ _('Catégorie') }}</th>
                                                <td valign="top">{{ $reservation->categorie_nom }}</td>
                                            </tr>
                                            @if ($reservation->categorie)
                                                <tr>
                                                    <th valign="top">{{ _('Modéle') }}</th>
                                                    <td valign="top">{{ $reservation->categorie->modeles }} {{ _('ou équivalent') }}</td>
                                                </tr>
                                                @if($reservation->franchise)
                                                    <tr>
                                                        <th valign="top">{{ _('Franchise') }}</th>
                                                        <td valign="top">@prix($reservation->franchise)&nbsp;€</td>
                                                    </tr>
                                                @endif
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                    </tr>
                </table>
                @if ($reservation->custom_fields->fields)
                    <div style="font-size:10px; line-height:10px;">&nbsp;</div>
                    <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                        <tr>
                            <td align="center" valign="top">
                                <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                    <tr>
                                        <td align="center" valign="top">
                                            <h2 style="margin: 12px 0; padding: 5px 10px; font-size: 15px; line-height: 20px; text-align: center; background-color: {{ $couleur }}; color: white; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">{{ _('Informations complémentaires') }}</h2>
                                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table" style="font-size: 12px; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">
                                                @foreach($reservation->custom_fields->fields as $key => $value)
                                                    <tr>
                                                        <th valign="top">{{ $key }}</th>
                                                        <td align="top">{{ $value }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                        </tr>
                    </table>
                @endif
                <div style="font-size:10px; line-height:10px;">&nbsp;</div>
                <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                    <tr>
                        <td align="center" valign="top">
                            <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                <tr>
                                    <td align="center" valign="top">
                                        <h2 style="margin: 12px 0; padding: 5px 10px; font-size: 15px; line-height: 20px; text-align: center; background-color: {{ $couleur }}; color: white; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">{{ _('Détails du paiement') }}</h2>
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table" style="font-size: 12px; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">
                                            @if ($reservation->modalite)
                                                <tr>
                                                    <th valign="top">{{ _('Modalité de paiement') }}</th>
                                                    <td align="right">{{ $reservation->modalite->nom }}</td>
                                                </tr>
                                            @endif
                                                @if ($reservation->has_echeancier)
                                                    <tr>
                                                        <th valign="top">{{ _('Échéancier') }}</th>
                                                        <td align="right">
                                                            @foreach($reservation->echeancier as $echeance)
                                                                {{ \Carbon\Carbon::make($echeance['date'])->format('d/m/Y') }} : @prix($echeance['montant']) €<br>
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                @endif
                                            <tr>
                                                <th valign="top">{{ _('Prix de base') }} ({{ $reservation->nb_jours }} {{ _('jours') }})</th>
                                                <td align="right">@prix($reservation->montant_base)&nbsp;€</td>
                                            </tr>
                                            @if ($reservation->prestations)
                                                @foreach ($reservation->prestations as $prestation)
                                                    <tr>
                                                        <th>{{ $prestation['quantite'] }} {{ strtolower($prestation['nom']) }} {{ !empty($prestation['choix']) ? '('.$prestation['choix'].')' : '' }}</th>
                                                        <td align="right">
                                                            {{ $prestation['tarif_libelle'] }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            @if ($reservation->promotions)
                                                @foreach ($reservation->promotions as $promotion)
                                                    <tr>
                                                        <th valign="top">
                                                            {{ _('Offre') }} {{ strtolower($promotion['nom']) }}
                                                        </th>
                                                        <td align="right"><b>-@prix($promotion['reduction'])&nbsp;€</b></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            <tr>
                                                <th valign="top">{{ _('Total (TTC)') }}</th>
                                                <td align="right"><strong {{ $reservation->is_payed ?  'style="padding: 5px 5px; line-height: 22px;  background-color: #333; color: white;"' : '' }}>@prix($reservation->total)&nbsp;€</strong></td>
                                            </tr>
                                            @if (!$reservation->is_payed)
                                                <tr>
                                                    <th valign="top">{{ _('Reste à régler') }}</th>
                                                    <td align="right"><strong style="padding: 5px 5px; line-height: 22px;  background-color: #333; color: white;">@prix($reservation->total - $reservation->montant_paye)&nbsp;€</strong></td>
                                                </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size:10px; line-height:10px;">&nbsp;</div>
                <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                    <tr>
                        <td align="center" valign="top">
                            <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                <tr>
                                    <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top" style="font-size: 13px; font-family: Verdana, 'Bitstream Vera Sans', 'Lucida Grande', sans-serif">
                                        @if ($reservation->lieuDebut)
                                            {{ _('Pour tout renseignement, nous contacter au') }} {{ $reservation->lieuDebut->telephone }}.<br>
                                        @endif
                                        <a href="{{ config('app.url') }}">{{  config('settings.nom_site') }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</center>
</body>
</html>