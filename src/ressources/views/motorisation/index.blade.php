@extends('IpsumAdmin::layouts.app')
@section('title', 'Carrosserie')

@section('content')

    <h1 class="main-title">Motorisation</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $motorisations->total() }})</h2>
            <div class="btn-toolbar">
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.motorisation.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'nom'])</th>
                    <th>Montant par litre/kw</th>
                    <th width="200px">Actions</th>
                </tr>
                </thead>
                <tbody class="sortable" data-sortableurl="{{ route('admin.carrosserie.changeOrder') }}" data-sortablecsrftoken="{{ csrf_token() }}">
                @foreach ($motorisations as $motorisation)
                    <tr class="sortable-item" data-sortable="{{ $motorisation->id }}">
                        <td>{{ $motorisation->id }}</td>
                        <td>{{ $motorisation->nom }}</td>
                        <td>@prix($motorisation->montant) €</td>
                        <td class="text-right">

                                <div class="btn sortable-move" data-toggle="tooltip" title="Trier"><span class="fa fa-arrows-alt"></span></div>
                                <a class="btn btn-primary" href="{{ route('admin.motorisation.edit', [$motorisation]) }}"><i class="fa fa-edit"></i> Modifier</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $motorisations->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection
