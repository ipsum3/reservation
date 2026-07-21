@extends('IpsumAdmin::layouts.app')
@section('title', 'Véhicules')

@section('content')

    <h1 class="main-title">Véhicules</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $vehicules->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.vehicule.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>

                <a class="btn btn-outline-secondary" href="{{ route('admin.vehicule.export', request()->all()) }}">
                    <i class="fas fa-upload"></i>
                    Export
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.vehicule.index') }}
                <label class="sr-only" for="search">Recherche</label>
                {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
                <label class="sr-only" for="categorie_id">Catégories</label>
                {{ Aire::select(collect(['' => '---- Catégories -----'])->union($categories), 'categorie_id')->value(request()->get('categorie_id'))->id('categorie_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
                <label class="sr-only" for="etat">État</label>
                {{ Aire::select(['' => '---- État -----', 'parc' => 'En parc', 'hors_parc' => 'Hors parc'], 'etat')->value(request()->get('etat'))->id('etat')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <div class="table-wrapper">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Immatriculation', 'champ' => 'immatriculation'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Marque modéle', 'champ' => 'marque_modele'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Catégorie', 'champ' => 'categorie_id'])</th>
                        <th>Réservations</th>
                        <th>État</th>
                        <th width="200px">Actions</th>
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
                                <a class="badge badge-info" href="{{ route('admin.reservation.index') }}?etat_id=2&vehicule_id={{ $vehicule->id }}">{{ $vehicule->reservations_count }} reservation{{ $vehicule->reservations_count > 1 ? 's' : '' }}</a>
                            </td>
                            <td>
                                @if($vehicule->is_hors_parc)
                                    <span class="badge badge-danger">Hors parc</span>
                                @else
                                    <span class="badge badge-success">En parc</span>
                                @endif
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
            </div>

            {!! $vehicules->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection