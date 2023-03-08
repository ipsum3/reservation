@extends('IpsumAdmin::layouts.app')
@section('title', 'Catégories')

@section('content')

    <h1 class="main-title">Catégories</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $categories->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.categorie.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.categorie.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            @if ($types->count() > 1)
                <label class="sr-only" for="type_id">Type</label>
                {{ Aire::select(collect(['' => '---- Types -----'])->union($types), 'type_id')->value(request()->get('type_id'))->id('type_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            @endif
            <label class="sr-only" for="carrosserie_id">Carrosserie</label>
            {{ Aire::select(collect(['' => '---- Carrosseries -----'])->union($carrosseries), 'carrosserie_id')->value(request()->get('carrosserie_id'))->id('carrosserie_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'nom'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Modéle', 'champ' => 'modeles'])</th>
                    <th>Véhicules</th>
                    <th>Blocages</th>
                    <th>Illustration</th>
                    <th width="160px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($categories as $categorie)
                    <tr>
                        <td>{{ $categorie->id }}</td>
                        <td>{{ $categorie->nom }}</td>
                        <td>{{ $categorie->modeles }}</td>
                        <td>
                            <a href="{{ route('admin.vehicule.index') }}?categorie_id={{ $categorie->id }}" class="badge badge-info">
                                {{ $categorie->vehicules_count }} véhicule{{ $categorie->vehicules_count > 1 ? 's' : '' }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.categorieBlocage.index') }}?categorie_id={{ $categorie->id }}" class="badge {{ $categorie->blocages_count ? 'badge-danger' : 'badge-light' }}">
                                {{ $categorie->blocages_count }} blocage{{ $categorie->blocages_count > 1 ? 's' : '' }}
                            </a>
                        </td>
                        <td>
                            @if ($categorie->illustration)
                                <img src="{{ Croppa::url($categorie->illustration->cropPath, 130, 130) }}" alt="{{ $categorie->illustration->tagAlt }}" />
                            @endif
                        </td>
                        <td class="text-right">
                            <form action="{{ route('admin.categorie.destroy', $categorie) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-primary" href="{{ route('admin.categorie.edit', [$categorie]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $categories->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection