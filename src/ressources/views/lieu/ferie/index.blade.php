@extends('IpsumAdmin::layouts.app')
@section('title', 'Jours fériés')

@section('content')

    <h1 class="main-title">Jours fériés</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $feries->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.lieuFerie.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.lieuFerie.index') }}
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
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Jour', 'champ' => 'jour_at'])</th>
                    <th width="160px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($feries as $ferie)
                    <tr>
                        <td>{{ $ferie->id }}</td>
                        <td>{{ $ferie->lieu ? $ferie->lieu->nom : '' }}</td>
                        <td>{{ $ferie->nom }}</td>
                        <td>{{ $ferie->jour_at->format('d/m/Y') }}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.lieuFerie.destroy', $ferie) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-primary" href="{{ route('admin.lieuFerie.edit', [$ferie]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $feries->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection