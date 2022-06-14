@extends('IpsumAdmin::layouts.app')
@section('title', 'Carrosserie')

@section('content')

    <h1 class="main-title">Carrosserie</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $carrosseries->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.carrosserie.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.carrosserie.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'nom'])</th>
                    <th width="200px">Actions</th>
                </tr>
                </thead>
                <tbody class="sortable" data-sortableurl="{{ route('admin.carrosserie.changeOrder') }}" data-sortablecsrftoken="{{ csrf_token() }}">
                @foreach ($carrosseries as $carrosserie)
                    <tr class="sortable-item" data-sortable="{{ $carrosserie->id }}">
                        <td>{{ $carrosserie->id }}</td>
                        <td>{{ $carrosserie->nom }}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.carrosserie.destroy', $carrosserie) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="btn sortable-move" data-toggle="tooltip" title="Trier"><span class="fa fa-arrows-alt"></span></div>
                                <a class="btn btn-primary" href="{{ route('admin.carrosserie.edit', [$carrosserie]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $carrosseries->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection