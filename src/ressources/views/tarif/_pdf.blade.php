<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>
        Grille des tarifs
    </title>

    <style type="text/css">
        @page {
            margin: 1cm;
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 13px;
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

        .tableau2 th {
            padding: 2px 0;

            text-align: center;
            font-weight: normal;
        }

        .tableau3 td {
            padding: 0;
            border: 0 none;
        }
    </style>
</head>
<body>
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
                    <h2>{{ $saison->nom }}</h2>
                    <p>Tarifs du {{ $saison->debut_at->format('d/m/Y') }} au {{ $saison->fin_at->format('d/m/Y') }}</p>
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
        <tr>
            <th style="width: 100px;"></th>
            @foreach ($durees as $duree)
                <th>
                    @if ($duree->max)
                        {{ $duree->min }} à {{ $duree->max }} jours
                    @else
                        {{ $duree->min }} jours et plus
                    @endif
                    @if ($duree->nom)
                        <br>{{ $duree->nom }}
                    @endif
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $categorie)
            <tr class="{{ $loop->even ? " pair" : "impair" }}">
                <td>{{ $categorie->nom }}</td>
                @foreach ($durees as $duree)
                    <td class="center" style="text-align: right">
                        {{ $tarifs[(isset($condition) ? $condition->id : null)][$categorie->id][$duree->id] ?? null }} €
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
</body>
</html>
