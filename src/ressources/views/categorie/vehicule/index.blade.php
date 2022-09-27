@extends('IpsumAdmin::layouts.app')
@section('title', 'Catégories')

@section('content')

    <h1 class="main-title">Véhicule</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $vehicules->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.vehicule.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.vehicule.index') }}
                <label class="sr-only" for="search">Recherche</label>
                {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
                <label class="sr-only" for="categorie_id">Catégories</label>
                {{ Aire::select(collect(['' => '---- Catégories -----'])->union($categories), 'categorie_id')->value(request()->get('categorie_id'))->id('categorie_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}

                <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Immatriculation', 'champ' => 'immatriculation'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Marque modéle', 'champ' => 'marque_modele'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Catégorie', 'champ' => 'categorie_id'])</th>
                    <th>Réservations</th>
                    <th width="160px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($vehicules as $vehicule)
                    <tr>
                        <td>{{ $vehicule->id }}</td>
                        <td>{{ $vehicule->immatriculation }}</td>
                        <td>{{ $vehicule->marque_modele }}</td>
                        <td>{{ $vehicule->categorie ? $vehicule->categorie->nom : '' }}</td>
                        <td>
                            <a href="{{ route('admin.reservation.index') }}?etat_id=2&vehicule_id={{ $vehicule->id }}">{{ $vehicule->reservations_count }} reservation{{ $vehicule->reservations_count > 1 ? 's' : '' }}</a>
                        </td>
                        <td class="text-right">
                            <form action="{{ route('admin.vehicule.destroy', $vehicule) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-primary" href="{{ route('admin.vehicule.edit', [$vehicule]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $vehicules->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection