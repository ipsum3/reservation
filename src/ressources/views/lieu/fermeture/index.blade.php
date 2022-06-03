@extends('IpsumAdmin::layouts.app')
@section('title', 'Fermeture des lieux')

@section('content')

    <h1 class="main-title">Fermeture des lieux</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $fermetures->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.lieuFermeture.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.lieuFermeture.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            <label class="sr-only" for="lieu_id">Lieu</label>
            {{ Aire::select(collect(['' => '---- Lieux -----'])->union($lieux), 'lieu_id')->value(request()->get('lieu_id'))->id('lieu_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Lieu', 'champ' => 'lieu_id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'nom'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Début', 'champ' => 'debut_at'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Fin', 'champ' => 'fin_at'])</th>
                    <th width="160px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($fermetures as $fermeture)
                    <tr>
                        <td>{{ $fermeture->id }}</td>
                        <td>{{ $fermeture->lieu ? $fermeture->lieu->nom : '' }}</td>
                        <td>{{ $fermeture->nom }}</td>
                        <td>{{ $fermeture->debut_at->format('d/m/Y') }}</td>
                        <td>{{ $fermeture->fin_at ? $fermeture->fin_at->format('d/m/Y') : '' }}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.lieuFermeture.destroy', $fermeture) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-primary" href="{{ route('admin.lieuFermeture.edit', [$fermeture]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $fermetures->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection