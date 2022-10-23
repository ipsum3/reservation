@extends('IpsumAdmin::layouts.app')
@section('title', 'Interventions')

@section('content')

    <h1 class="main-title">Interventions sur des véhicules</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $interventions->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.intervention.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.intervention.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            <label class="sr-only" for="type_id">Type</label>
            {{ Aire::select(collect(['' => '---- Types -----'])->union($types), 'type_id')->value(request()->get('type_id'))->id('type_id')->class('form-control mb-2 mr-sm-2')->withoutGroup() }}
            <label class="sr-only" for="dates">Dates</label>
            {{ Aire::input('dates')->value(request()->get('dates'))->id('dates')->placeholder('Dates')->style('width: 200px')->class('form-control mb-2 mr-sm-2 datepicker-range')->withoutGroup() }}
            <label class="sr-only" for="immatriculation">Immatriculation</label>
            {{ Aire::input('immatriculation')->id('immatriculation')->class('form-control mb-2 mr-sm-2')->value(request()->get('immatriculation'))->placeholder('Immatriculation')->withoutGroup() }}

            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Véhicule', 'champ' => 'vehicule_id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Type', 'champ' => 'type_id'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Début', 'champ' => 'debut_at'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Fin', 'champ' => 'fin_at'])</th>
                    <th>@include('IpsumAdmin::partials.tri', ['label' => 'Intervenant', 'champ' => 'intervenant'])</th>
                    <th width="160px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($interventions as $intervention)
                    <tr>
                        <td>{{ $intervention->id }}</td>
                        <td>
                            <a href="{{ $intervention->vehicule ? route('admin.vehicule.edit', $intervention->vehicule) : '#' }}">
                                {{ $intervention->vehicule ? $intervention->vehicule->immatriculation : '' }}
                            </a>
                        </td>
                        <td>{{ $intervention->type ? $intervention->type->nom : '' }}</td>
                        <td>{{ $intervention->debut_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $intervention->fin_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $intervention->intervenant }}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.intervention.destroy', $intervention) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-primary" href="{{ route('admin.intervention.edit', [$intervention]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $interventions->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection