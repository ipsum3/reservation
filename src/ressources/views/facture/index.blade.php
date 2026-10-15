@extends('IpsumAdmin::layouts.app')
@section('title', 'Factures')

@section('content')

    <h1 class="main-title">Factures</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $factures->total() }})</h2>
        </div>
        <div class="box-body">
            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.facture.index') }}
                <label class="sr-only" for="search">Recherche</label>
                {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
                {{ Aire::select(collect(['' => '---- Types -----'])->union(collect(\Ipsum\Reservation\app\Enum\FactureType::pluck())), 'type')->value(request()->get('type'))->id('type')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
                <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <div class="table-wrapper">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Date', 'champ' => 'date'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Numéro', 'champ' => 'numero'])</th>
                        <th>Résa.</th>
                        <th>Client</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Type', 'champ' => 'type'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Etat', 'champ' => 'etat'])</th>
                        <th>Montant</th>
                        <th>Reste à payer</th>
                        <th>Envoyé au client</th>
                        <th width="240px">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="sortable">
                    @foreach ($factures as $facture)
                        <tr class="sortable-item" data-sortable="{{ $facture->id }}">
                            <td>{{ $facture->id }}</td>
                            <td>{{ $facture->emission_at?->format('d/m/Y') }}</td>
                            <td>{{ $facture->numero }}</td>
                            <td><a href="{{ route('admin.reservation.edit', $facture->reservation) }}">{{ $facture->reservation?->reference }}</a></td>
                            <td><a href="{{ route('admin.client.edit', $facture->client) }}">{{ $facture->client->prenom }} {{ $facture->client->nom }}</a></td>
                            <td>{{ $facture->type->label() }}</td>
                            <td><span class="badge {{ $facture->etat->badge() }}">{{ $facture->etat->label() }}</span></td>
                            <td>@prix($facture->total)&nbsp;€</td>
                            <td><x-reservation::reste_a_payer total="{{ $facture->total }}"  montant_paye="{{ $facture->montant_paye }}" /></td>
                            <td>{{ $facture->send_at?->format('d/m/Y') }}</td>
                            <td class="text-right">
                                @if(!$facture->is_brouillon)
                                    <a class="btn btn-outline-secondary" href="{{ route('admin.facture.pdf', [$facture]) }}" target="_blank"><i class="fa fa-file-pdf"></i></a>
                                @endif
                                <a class="btn btn-primary" href="{{ route($facture->is_brouillon ? 'admin.facture.edit.brouillon' : 'admin.facture.edit', [$facture]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                @can('delete', $facture)
                                    <a class="btn btn-outline-danger" href="{{ route('admin.facture.destroy', $facture) }}" data-toggle="tooltip" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {!! $factures->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection