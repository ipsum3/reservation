@extends('IpsumAdmin::layouts.app')
@section('title', 'Saisons')

@section('content')

    <h1 class="main-title">Saisons</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $saisons->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.saison.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.saison.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'nom'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Début', 'champ' => 'debut_at'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Fin', 'champ' => 'fin_at'])</th>
                    <th>Tarifs</th>
                    <th width="160px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($saisons as $saison)
                    <tr>
                        <td>{{ $saison->id }}</td>
                        <td>{{ $saison->nom }}</td>
                        <td>{{ $saison->debut_at->format('d/m/Y') }}</td>
                        <td>{{ $saison->fin_at->format('d/m/Y') }}</td>
                        <td><a href="{{ route('admin.tarif.edit', $saison) }}">Grilles de tarifs</a></td>
                        <td class="text-right">
                            <form action="{{ route('admin.saison.destroy', $saison) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-primary" href="{{ route('admin.saison.edit', [$saison]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $saisons->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection