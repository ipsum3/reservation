<!doctype html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Devis</title>

    <link rel="stylesheet" href="@asset_versioned('ipsum/admin/dist/main.css')">

    <style>

    </style>

</head>

<body style="background-color: white">

    <div class="container mt-5" style="max-width: 800px">

        {!! $pdf !!}

        <div class="text-center mt-5">
            <a href="{{ URL::signedRoute('devis.redirectBanque', $reservation) }}" class="btn btn-lg btn-primary">Procéder au paiement de @prix($montant) €</a>
        </div>

    </div>

</body>

</html>