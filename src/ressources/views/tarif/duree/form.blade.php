@extends('IpsumAdmin::layouts.app')
@section('title', 'Durée')

@section('content')

    <h1 class="main-title">{{ $duree->is_special ? 'Tarif spécial (exemple : forfait weekend, forfait semaine, forfait 1/2 journée, forfait noctambule)' : 'Tranche de durée' }}</h1>

    {{ Aire::open()->route($duree->exists ? 'admin.duree.update' : 'admin.duree.store', $duree->exists ? $duree : null)->bind($duree)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreDuree::class) }}
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">{{ $duree->exists ? 'Modification' : 'Ajout' }}</h3>
                <div class="btn-toolbar">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                    <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                    @if ($duree->exists)
                        <a class="btn btn-outline-secondary" href="{{ route('admin.duree.create') }}" data-toggle="tooltip" title="Ajouter">
                            <i class="fas fa-plus"></i>
                        </a>&nbsp;
                        <a class="btn btn-outline-danger" href="{{ route('admin.duree.destroy', $duree) }}" data-toggle="tooltip" title="Supprimer">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    @endif
                </div>
            </div>
            <div class="box-body">
                <div class="form-row">
                    {{ Aire::input('min', 'Mini (jour)*')->groupAddClass('col-md-6') }}
                    {{ Aire::input('max', 'Maxi (jour)')->groupAddClass('col-md-6') }}
                </div>

                @if ($duree->is_special)
                    <div class="form-row">
                        {{ Aire::hidden('is_special', 1) }}
                        {{ Aire::input('nom', 'Nom')->groupAddClass('col-md-6') }}
                        {{ Aire::select(array_combine(\Ipsum\Reservation\app\Models\Tarif\Duree::TARIFICATION, Ipsum\Reservation\app\Models\Tarif\Duree::TARIFICATION), 'tarification', 'Tarification')->groupAddClass('col-md-6') }}
                    </div>
                @endif
            </div>
        </div>


        @if ($duree->is_special)
            <div class="row">
                <div class="col-sm-6">
                    <div class="box">
                        <div class="box-header">
                            <h2 class="box-title">Conditions sur le jour du début</h2>
                        </div>
                        <div class="box-body">
                            @foreach(\Ipsum\Reservation\app\Models\Tarif\Jour::VALEURS as $key => $jour)
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="jours_debut-{{ $key }}">
                                        {{ $jour }}
                                    </label>
                                    <div class="col-sm-1">
                                        <input type="hidden" name="jours_debut[{{ $key }}][is_debut]" value="1">
                                        <input class="form-check-input" type="checkbox" name="jours_debut[{{ $key }}][value]" value="{{ $key }}" id="jours_debut-{{ $key }}" {{ old('jours_debut.'.$key.'.value', $duree->jours->where('is_debut', 1)->where('value', $key)->count() ? 'checked' : '') }}>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="time" class="form-control" name="jours_debut[{{ $key }}][heure]" value="{{ old('jours_debut.'.$key.'.heure', $duree->jours->where('is_debut', 1)->where('value', $key)->count() ? $duree->jours->where('is_debut', 1)->where('value', $key)->first()->heure : null) }}">
                                        <span class="form-text text-muted">Heure minimum (inclus)</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="box">
                        <div class="box-header">
                            <h2 class="box-title">Conditions sur le jour de fin</h2>
                        </div>
                        <div class="box-body">
                            @foreach(\Ipsum\Reservation\app\Models\Tarif\Jour::VALEURS as $key => $jour)
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="jours_fin-{{ $key }}">
                                        {{ $jour }}
                                    </label>
                                    <div class="col-sm-1">
                                        <input type="hidden" name="jours_fin[{{ $key }}][is_debut]" value="0">
                                        <input class="form-check-input" type="checkbox" name="jours_fin[{{ $key }}][value]" value="{{ $key }}" id="jours_fin-{{ $key }}" {{ old('jours_fin.'.$key.'.value', $duree->jours->where('is_debut', 0)->where('value', $key)->count() ? 'checked' : '') }}>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="time" class="form-control" name="jours_fin[{{ $key }}][heure]" value="{{ old('jours_fin.'.$key.'.heure', $duree->jours->where('is_debut', 0)->where('value', $key)->count() ? $duree->jours->where('is_debut', 0)->where('value', $key)->first()->heure : null) }}">
                                        <span class="form-text text-muted">Heure minimum (inclus)</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    {{ Aire::close() }}

@endsection
