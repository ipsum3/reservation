@extends('IpsumAdmin::layouts.app')
@section('title', 'Clients')

@section('content')

    <h1 class="main-title">Clients</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $clients->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.client.export', request()->all()) }}">
                    <i class="fas fa-upload"></i>
                    Export
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.client.index') }}
                <label class="sr-only" for="search">Recherche</label>
                {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}

                <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'prenom'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Email', 'champ' => 'email'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Réservations', 'champ' => 'reservations_count'])</th>
                        <th width="250px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($clients as $client)
                    <tr>
                        <td>{{ $client->id }}</td>
                        <td>{{ $client->prenom }} {{ $client->nom }}</td>
                        <td>{{ $client->email }}</td>
                        <th><a class="badge badge-info" href="{{ route('admin.reservation.index', ['client_id' => $client->id]) }}">{{ $client->reservations_count }} réservation{{ $client->reservations_count > 1 ? 's' : '' }}</a></th>
                        <td class="text-right">
                            <form action="{{ route('admin.client.destroy', $client->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.create', ['client_id' => $client->id]) }}"><i class="fa fa-plus"></i> Résa.</a>
                                <a class="btn btn-primary" href="{{ route('admin.client.edit', [$client->id]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $clients->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection
