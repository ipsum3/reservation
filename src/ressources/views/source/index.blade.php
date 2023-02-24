@extends('IpsumAdmin::layouts.app')
@section('title', 'Sources')

@section('content')

    <h1 class="main-title">Sources</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $sources->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.source.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">
            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.source.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            {{ Aire::select(collect(['' => '---- Types -----'])->union($types), 'type_id')->value(request()->get('type_id'))->id('type_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'nom'])</th>
                    <th>Type</th>
                    <th>Ref. tracking</th>
                    <th width="240px">Actions</th>
                </tr>
                </thead>
                <tbody class="sortable">
                @foreach ($sources as $source)
                    <tr class="sortable-item" data-sortable="{{ $source->id }}">
                        <td>{{ $source->id }}</td>
                        <td>{{ $source->nom }}</td>
                        <td>{{ $source->type->nom }}</td>
                        <td>
                            <a href="{{ url('/') }}?origin={{ $source->ref_tracking }}" target="_blank">{{ $source->ref_tracking }}</a>
                        </td>
                        <td class="text-right">
                            <form action="{{ route('admin.source.destroy', $source) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-primary" href="{{ route('admin.source.edit', [$source]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                @if( $source->id != $source::SOURCE_SITE_INTERNET and $source->id != $source::SOURCE_AGENCE )
                                    <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $sources->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection