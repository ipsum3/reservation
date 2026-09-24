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
                        <th>Envoyé au client</th>
                        <th>pdf</th>
                        {{--<th width="240px">Actions</th>--}}
                    </tr>
                    </thead>
                    <tbody class="sortable">
                    @foreach ($factures as $facture)
                        <tr class="sortable-item" data-sortable="{{ $facture->id }}">
                            <td>{{ $facture->id }}</td>
                            <td>TODO</td>
                            <td>{{ $facture->numero }}</td>
                            <td><a href="{{ route('admin.reservation.edit', $facture->reservation) }}">{{ $facture->reservation?->reference }}</a></td>
                            <td>{{ $facture->reservation?->prenom }} {{ $facture->reservation?->nom }} TODO rajouter un champ name dans la table facture ?</td>
                            <td>{{ $facture->type->label() }}</td>
                            <td>{{ $facture->send_at?->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.facture.pdf', [$facture]) }}" target="_blank">Télécharger</a>
                            </td>
                            {{--<td class="text-right">
                                <form action="{{ route('admin.facture.destroy', $facture) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <a class="btn btn-primary" href="{{ route('admin.facture.edit', [$facture]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                    @if( $facture->id != $facture::FACTURE_SITE_INTERNET and $facture->id != $facture::FACTURE_AGENCE )
                                        <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                    @endif
                                </form>
                            </td>--}}
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {!! $factures->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection