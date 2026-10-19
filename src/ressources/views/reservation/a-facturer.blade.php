@extends('IpsumAdmin::layouts.app')
@section('title', 'Réservation à facturer')

@section('content')

    <h1 class="main-title">Réservations à facturer</h1>

    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $reservations->total() }})</h2>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.reservation.aFacturer') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->style('width: 180px')->withoutGroup() }}
            <label class="sr-only" for="date_fin">Date de fin</label>
            {{ Aire::input('date_fin')->value(request()->get('date_fin'))->id('date_fin')->placeholder('Date de fin')->style('width: 200px')->class('form-control mb-2 mr-sm-2 datepicker-range')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <div class="table-wrapper">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'reference'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Création', 'champ' => 'created_at'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Retour', 'champ' => 'fin_at'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Lieu', 'champ' => 'fin_lieu_nom'])</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th width="180px">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->reference }}</td>
                            <td>{{ $reservation->created_at->format('d/m/Y') }}</td>
                            <td>{{ $reservation->fin_at->format('d/m/Y') }}</td>
                            <td>{{ $reservation->fin_lieu_nom }}</td>
                            <td>
                                @if ($reservation->client)
                                    <a href="{{ route('admin.client.edit', $reservation->client) }}">{{ $reservation->civilite }}  {{ $reservation->prenom }} {{ $reservation->nom }}</a>
                                @else
                                    {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}
                                @endif
                            </td>
                            <td class="text-right">
                                @if ($reservation->total)
                                    @prix($reservation->total) &nbsp;€
                                @endif
                            </td>
                            <td class="text-right">
                                <a class="btn btn-outline-primary" href="{{ route('admin.reservation.edit', [$reservation]) }}"><i class="fa fa-edit"></i></a>
                                <a class="btn btn-outline-secondary" href="{{ route('admin.facture.create', [$reservation]) }}"><i class="fa fa-file-invoice-dollar"></i> Facturer</a>&nbsp;
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {!! $reservations->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection