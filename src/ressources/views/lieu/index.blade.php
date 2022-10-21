@extends('IpsumAdmin::layouts.app')
@section('title', 'Lieux')

@section('content')

    <h1 class="main-title">Lieux</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $lieux->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.lieu.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.lieu.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'nom'])</th>
                    <th>Fermetures</th>
                    <th width="240px">Actions</th>
                </tr>
                </thead>
                <tbody class="sortable" data-sortableurl="{{ route('admin.lieu.changeOrder') }}" data-sortablecsrftoken="{{ csrf_token() }}">
                @foreach ($lieux as $lieu)
                    <tr class="sortable-item" data-sortable="{{ $lieu->id }}">
                        <td>{{ $lieu->id }}</td>
                        <td>{{ $lieu->nom }}</td>
                        <td>
                            <a href="{{ route('admin.lieuFermeture.index') }}?lieu_id={{ $lieu->id }}" class="badge {{ $lieu->fermetures_count ? 'badge-danger' : 'badge-light' }}">
                                {{ $lieu->fermetures_count }} fermeture{{ $lieu->fermetures_count > 1 ? 's' : '' }}
                            </a>
                        </td>
                        <td class="text-right">
                            <form action="{{ route('admin.lieu.destroy', $lieu) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="btn sortable-move" data-toggle="tooltip" title="Trier"><span class="fa fa-arrows-alt"></span></div>
                                <a class="btn btn-primary" href="{{ route('admin.lieu.edit', [$lieu]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                <a class="btn btn-outline-{{ $lieu->is_actif ? 'success' : 'gray' }}" href="{{ route('admin.lieu.activation', [$lieu->id]) }}"><i class="fa {{ $lieu->is_actif ? 'fa-check' : 'fa-check' }}"></i></a>
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $lieux->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection