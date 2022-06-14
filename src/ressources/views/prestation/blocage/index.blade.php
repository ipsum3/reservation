@extends('IpsumAdmin::layouts.app')
@section('title', 'Blocage des prestations')

@section('content')

    <h1 class="main-title">Blocage des prestations</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $blocages->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.prestationBlocage.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.prestationBlocage.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            <label class="sr-only" for="prestation_id">Catégorie</label>
            {{ Aire::select(collect(['' => '---- Catégories -----'])->union($prestations), 'prestation_id')->value(request()->get('prestation_id'))->id('prestation_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Prestations', 'champ' => 'prestation_id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'nom'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Début', 'champ' => 'debut_at'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Fin', 'champ' => 'fin_at'])</th>
                    <th width="160px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($blocages as $blocage)
                    <tr>
                        <td>{{ $blocage->id }}</td>
                        <td>{{ $blocage->prestation ? $blocage->prestation->nom : '' }}</td>
                        <td>{{ $blocage->nom }}</td>
                        <td>{{ $blocage->debut_at->format('d/m/Y') }}</td>
                        <td>{{ $blocage->fin_at->format('d/m/Y') }}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.prestationBlocage.destroy', $blocage) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-primary" href="{{ route('admin.prestationBlocage.edit', [$blocage]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $blocages->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection