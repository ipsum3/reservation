@extends('IpsumAdmin::layouts.app')
@section('title', 'Reservations')

@section('content')

    <h1 class="main-title">Reservations</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $reservations->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.create', $type) }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.reservation.index', $type) }}
                <label class="sr-only" for="search">Recherche</label>
                {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}

                @if (config('ipsum.reservation.types.'.$type.'.has_categorie'))
                    <label class="sr-only" for="categorie_id">Catégorie</label>
                    <select id="categorie_id" name="categorie_id" class="form-control mb-2 mr-sm-2" style="max-width: 300px;">
                        <option value="">---- Catégories -----</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" {{ $categorie->id == request()->get('categorie_id') ? 'selected' : '' }}>{{ $categorie->nom }}</option>
                            @foreach($categorie->children as $sous_categorie)
                                <option value="{{ $sous_categorie->id }}" {{ $sous_categorie->id == request()->get('categorie_id') ? 'selected' : '' }}>-- {{ $sous_categorie->nom }}</option>
                            @endforeach
                        @endforeach
                    </select>
                @endif

                <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'État', 'champ' => 'etat'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Date', 'champ' => 'created_at'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Titre', 'champ' => 'titre'])</th>
                        <th>Extrait</th>
                        @if (config('ipsum.reservation.types.'.$type.'.has_categorie'))
                        <th>Catégorie</th>
                        @endif
                        <th>Illustration</th>
                        <th width="160px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->id }}</td>
                        <td>{{ $reservation->etatToString }}</td>
                        <td>{{ $reservation->created_at->format('d/m/Y') }}</td>
                        <td>{{ $reservation->nom }}</td>
                        <td>{{ Str::limit(strip_tags($reservation->extrait)) }}</td>
                        @if (config('ipsum.reservation.types.'.$type.'.has_categorie'))
                        <td>{{ $reservation->categorie ? $reservation->categorie->nom : '' }}</td>
                        @endif
                        <td>
                            @if ($reservation->illustration)
                            <img src="{{ Croppa::url($reservation->illustration->cropPath, 130, 130) }}" alt="{{ $reservation->illustration->tagAlt }}" />
                            @endif
                        </td>
                        <td class="text-right">
                            <form action="{{ route('admin.reservation.destroy', $reservation) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a class="btn btn-primary" href="{{ route('admin.reservation.edit', [$type, $reservation]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                @if ($reservation->is_deletable)
                                    <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {!! $reservations->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection