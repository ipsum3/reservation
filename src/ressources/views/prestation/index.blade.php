@extends('IpsumAdmin::layouts.app')
@section('title', 'Prestations')

@section('content')

    <h1 class="main-title">Prestations</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $prestations->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.prestation.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.prestation.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            <label class="sr-only" for="type_id">Type</label>
            {{ Aire::select(collect(['' => '---- Types -----'])->union($types), 'type_id')->value(request()->get('type_id'))->id('type_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'nom'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Type', 'champ' => 'type_id'])</th>
                    <th>Blocages</th>
                    <th width="240px">Actions</th>
                </tr>
                </thead>
                <tbody class="sortable" data-sortableurl="{{ route('admin.prestation.changeOrder') }}" data-sortablecsrftoken="{{ csrf_token() }}">
                @foreach ($prestations as $prestation)
                    <tr class="sortable-item" data-sortable="{{ $prestation->id }}">
                        <td>{{ $prestation->id }}</td>
                        <td>{{ $prestation->nom }}</td>
                        <td>{{ $prestation->type ? $prestation->type->nom : '' }}</td>
                        <td>
                            <a href="{{ route('admin.prestationBlocage.index') }}?prestation_id={{ $prestation->id }}" class="badge {{ $prestation->blocages_count ? 'badge-danger' : 'badge-light' }}">
                                {{ $prestation->blocages_count }} blocage{{ $prestation->blocages_count > 1 ? 's' : '' }}
                            </a>
                        </td>
                        <td class="text-right">
                            <form action="{{ route('admin.prestation.destroy', $prestation) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="btn sortable-move" data-toggle="tooltip" title="Trier"><span class="fa fa-arrows-alt"></span></div>
                                <a class="btn btn-primary" href="{{ route('admin.prestation.edit', [$prestation]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $prestations->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection