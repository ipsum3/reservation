@extends('IpsumAdmin::layouts.app')
@section('title', 'Carrosseries')

@section('content')

    <h1 class="main-title">Carrosserie</h1>

    {{ Aire::open()->route($carrosserie->exists ? 'admin.carrosserie.update' : 'admin.carrosserie.store', $carrosserie->exists ? [$carrosserie, request()->route('locale')] : '')->bind($carrosserie)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreCarrosserie::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $carrosserie->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                @if ($carrosserie->exists and count(config('ipsum.translate.locales')) > 1)
                    <ul class="nav nav-tabs mr-5" role="tablist">
                        @foreach(config('ipsum.translate.locales') as $locale)
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->route('locale') == $locale['nom'] or (request()->route('locale') === null and config('ipsum.translate.default_locale') == $locale['nom'])) ? 'active' : '' }}" href="{{ route('admin.carrosserie.edit', [$carrosserie, $locale['nom']]) }}" aria-selected="true">{{ $locale['intitule'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($carrosserie->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.carrosserie.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-danger" href="{{ route('admin.carrosserie.destroy', $carrosserie) }}" data-toggle="tooltip" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::input('nom', 'Nom')->groupAddClass('col-md-6') }}
            </div>
        </div>
    </div>
    {{ Aire::close() }}


@endsection
