@extends('IpsumAdmin::layouts.app')
@section('title', 'Produits')

@section('content')

    <h1 class="main-title">Produits</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $produits->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.produit.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.produit.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            <label class="sr-only" for="type_id">Type</label>
            {{ Aire::select(collect(['' => '---- Types -----'])->union($types), 'type_id')->value(request()->get('type_id'))->id('type_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <div class="table-wrapper">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'nom'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Type', 'champ' => 'type_id'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Taxe', 'champ' => 'taxe_id'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Prix TTC', 'champ' => 'montant'])</th>
                        <th width="240px">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($produits as $produit)
                        <tr>
                            <td>{{ $produit->id }}</td>
                            <td>{{ $produit->nom }}</td>
                            <td>{{ $produit->type ? $produit->type->nom : '' }}</td>
                            <td>{{ $produit->taxe ? $produit->taxe->taux.'%' : '' }}</td>
                            <td>{{ $produit->montant ? prix($produit->montant).' €' : '' }}</td>
                            <td class="text-right">
                                <form action="{{ route('admin.produit.destroy', $produit) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <a class="btn btn-primary" href="{{ route('admin.produit.edit', [$produit]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                    <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {!! $produits->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection