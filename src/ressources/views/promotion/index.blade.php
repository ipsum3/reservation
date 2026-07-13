@extends('IpsumAdmin::layouts.app')
@section('title', 'Promotions')

@section('content')

    <h1 class="main-title">Promotions</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $promotions->total() }})</h2>
            <div class="btn-toolbar">
                <a class="btn btn-outline-secondary" href="{{ route('admin.promotion.create') }}">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </a>
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.promotion.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>
            {{ Aire::close() }}

            <div class="table-wrapper">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Nom', 'champ' => 'nom'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Code', 'champ' => 'code'])</th>
                        <th>Réservations</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Du', 'champ' => 'debut_at'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Au', 'champ' => 'fin_at'])</th>
                        <th>Statut</th>
                        <th width="240px">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($promotions as $promotion)
                        <tr>
                            <td>{{ $promotion->id }}</td>
                            <td>
                                {{ $promotion->nom }}
                            </td>
                            <td>
                                @if($promotion->code)
                                    <span class="badge badge-primary">{{ $promotion->code }} </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $reservations_count = $promotion->reservations()->count();
                                @endphp

                                <a href="{{ route('admin.reservation.index', ['promotion' => $promotion->id]) }}"
                                   class="badge badge-info">
                                    {{ $reservations_count }} Réservation{{ $reservations_count > 1 ? 's' : '' }}
                                </a>
                            </td>
                            <td>{{ $promotion->debut_at?->format('d/m/Y') }}</td>
                            <td>{{ $promotion->fin_at?->format('d/m/Y') }}</td>
                            <td>
                                @if($promotion->is_en_cours)
                                    <span class="badge badge-success">
                                        <i class="fa fa-check-circle"></i> En cours
                                    </span>
                                @else
                                    <span class="badge badge-light text-muted">
                                        <i class="fa fa-clock"></i> Désactivé
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <form action="{{ route('admin.promotion.destroy', $promotion) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <a class="btn btn-primary" href="{{ route('admin.promotion.edit', [$promotion]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                    @if ($promotion->is_en_cours)
                                        <a class="btn btn-outline-primary" href="{{ route('admin.promotion.desactivation', [$promotion]) }}"><i class="fa fa-power-off" title="Désactiver"></i></a>
                                    @endif
                                    <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {!! $promotions->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection