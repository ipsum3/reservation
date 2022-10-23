@extends('IpsumAdmin::layouts.app')
@section('title', 'Réservation')

@section('content')

    <h1 class="main-title">Réglements</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $paiements->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.paiement.export', request()->all()) }}">
                    <i class="fas fa-upload"></i>
                    Export
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.paiement.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            <label class="sr-only" for="paiement_moyen_id">Moyen</label>
            {{ Aire::select(collect(['' => '---- Moyens -----'])->union($moyens), 'paiement_moyen_id')->value(request()->get('paiement_moyen_id'))->id('paiement_moyen_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <label class="sr-only" for="date_creation">Date de création</label>
            {{ Aire::input('date_creation')->value(request()->get('date_creation'))->id('date_creation')->placeholder('Date de création')->style('width: 200px')->class('form-control mb-2 mr-sm-2 datepicker-range')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'reference'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Date', 'champ' => 'created_at'])</th>
                    <th>Réservation</th>
                    <th>Client</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Moyen', 'champ' => 'paiement_moyen_id'])</th>
                    <th>Total</th>
                    <th>Note</th>
                    <th width="50px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($paiements as $paiement)
                    <tr>
                        <td>{{ $paiement->id }}</td>
                        <td>{{ $paiement->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if ($paiement->reservation)
                                <a href="{{ route('admin.reservation.edit', $paiement->reservation) }}">{{ $paiement->reservation->reference }}</a>
                            @endif
                        </td>
                        <td>
                            @if ($paiement->reservation)
                                @if ($paiement->reservation->client)
                                    <a href="{{ route('admin.client.edit', $paiement->reservation->client) }}">{{ $paiement->reservation->prenom }} {{ $paiement->nom }}</a>
                                @else
                                    {{ $paiement->reservation->civilite }} {{ $paiement->reservation->prenom }} {{ $paiement->reservation->nom }}
                                @endif
                            @endif
                        </td>
                        <td>
                            {{ $paiement->moyen ? $paiement->moyen->nom : '' }}
                            @if ($paiement->transaction_ref or $paiement->autorisation_ref)
                                <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="auto" title="{{ $paiement->transaction_ref ? 'Réf transaction : '.$paiement->transaction_ref : '' }} {{ $paiement->autorisation_ref ? 'Réf autorisation : '.$paiement->autorisation_ref : '' }}"></i>
                            @endif
                        </td>
                        <td>@prix($paiement->montant) €</td>
                        <td>{!! nl2br(e($paiement->note )) !!}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.paiement.destroy', $paiement) }}" method="POST">
                                {{--<a class="btn btn-outline-secondary" href="{{ route('admin.reservation.edit', [$paiement]) }}"><i class="fa fa-edit"></i></a>--}}
                                @can('delete', $paiement)
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                @endcan
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $paiements->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection