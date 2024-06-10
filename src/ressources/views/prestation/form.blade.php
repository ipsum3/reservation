@extends('IpsumAdmin::layouts.app')
@section('title', 'Prestations')

@section('content')

    <h1 class="main-title">Prestations</h1>

    {{ Aire::open()->route($prestation->exists ? 'admin.prestation.update' : 'admin.prestation.store', $prestation->exists ? [$prestation, request()->route('locale')] : '')->bind($prestation)->formRequest(\Ipsum\Reservation\app\Http\Requests\StorePrestation::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $prestation->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                @if ($prestation->exists and count(config('ipsum.translate.locales')) > 1)
                    <ul class="nav nav-tabs mr-5" role="tablist">
                        @foreach(config('ipsum.translate.locales') as $locale)
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->route('locale') == $locale['nom'] or (request()->route('locale') === null and config('ipsum.translate.default_locale') == $locale['nom'])) ? 'active' : '' }}" href="{{ route('admin.prestation.edit', [$prestation, $locale['nom']]) }}" aria-selected="true">{{ $locale['intitule'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($prestation->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.prestation.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-danger" href="{{ route('admin.prestation.destroy', $prestation) }}" data-toggle="tooltip" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::input('nom', 'Nom*')->required()->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Types -----'])->union($types), 'type_id', 'Type*')->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Tarifications -----'])->union($tarifications), 'tarification_id', 'Tarification*')->groupAddClass('col-md-6') }}
                {{ Aire::textArea('description', 'Description')->groupAddClass('col-md-6') }}
                {{ Aire::number('montant', 'Montant')->step(.01)->groupAddClass('col-md-6') }}
                {{ Aire::number('quantite_max', 'Quantité max*')->required()->defaultValue(1)->groupAddClass('col-md-6') }}
                {{ Aire::number('gratuit_apres', 'Gratuit après x jours')->groupAddClass('col-md-6') }}
                {{ Aire::number('jour_fact_max', 'Nombre de jour maximum facturé')->groupAddClass('col-md-6') }}
                {{ Aire::number('quantite_gratuite', 'Gratuit pour x quantité')->groupAddClass('col-md-6') }}
            </div>
        </div>
    </div>

    @if (config('ipsum.reservation.prestation.custom_fields'))
        <div class="box">
            <div class="box-header">
                <h2 class="box-title">
                    Informations complémentaires
                </h2>
            </div>
            <div class="box-body">
                @foreach(config('ipsum.reservation.prestation.custom_fields') as $field)
                    @php
                        $field_name = 'custom_fields['.$field['name'].']';
                        $field_value = old('custom_fields.'.$field['name'], $prestation->custom_fields->{$field['name']} ?? ($field['type'] == "repeater" ? [] : '') );
                    @endphp
                    <x-admin::custom :field="$field" :name="$field_name" :value="$field_value"/>
                @endforeach
            </div>
        </div>
    @endif

    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Conditions</h2>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::number('age_max', 'Age inférieur à')->groupAddClass('col-md-6') }}
                <div class="form-group col-md-6" data-aire-component="group" data-aire-for="jour">
                    <label class=" cursor-pointer" data-aire-component="label" for="jour">
                        Jour
                    </label>
                    <select name="jour" class="form-control" id="jour">
                        <option value="">----- Jours -----</option>
                        @foreach(\Ipsum\Reservation\app\Models\Lieu\Horaire::JOURS as $key => $jour)
                            <option value="{{ $key }}" @selected(old('jour', $prestation->jour) === $key) >{{ $jour }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                {{ Aire::time('heure_max', 'Heure inférieur à')->groupAddClass('col-md-6') }}
                {{ Aire::time('heure_min', 'Heure supérieur à')->groupAddClass('col-md-6') }}
            </div>
            <div class="form-row">
                {{ Aire::number('duree_min', 'Durée minimum de réservation')->groupAddClass('col-md-6') }}
                {{ Aire::number('duree_max', 'Durée maximum de réservation')->groupAddClass('col-md-6') }}
            </div>
            <div class="form-row">
                {{ Aire::select(collect(['' => '---- Conditions -----'])->union(\Ipsum\Reservation\app\Models\Prestation\Prestation::$LISTE_CONDITION), 'condition', 'Condition')->groupAddClass('col-md-6') }}
                @if ($categorie_types->count() > 1)
                    {{ Aire::select(collect(['' => '---- Types de catégorie -----'])->union($categorie_types), 'categorie_type_id', 'Type de catégorie')->groupAddClass('col-md-6') }}
                @endif
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Conditions par catégories</h2>
                </div>
                <div class="box-body">
                    @foreach($categories as $categorie)
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="categorie-{{ $categorie->id }}">
                                Catégorie {{ $categorie->nom }}
                            </label>
                            <div class="col-sm-1">
                                <input class="form-check-input" type="checkbox" name="categories[{{ $categorie->id }}][has]" value="{{ $categorie->id }}" id="categorie-{{ $categorie->id }}" {{ old('categories.'.$categorie->id.'.has', $prestation->categories->contains($categorie)) ? 'checked' : '' }}>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="categories[{{ $categorie->id }}][montant]" step=".001" value="{{ old('categories.'.$categorie->id.'.montant', $prestation->categories->contains($categorie) ? $prestation->categories->find($categorie)->pivot->montant : null) }}">
                                <span id="emailHelp" class="form-text text-muted">Montant à ajouter en &nbsp;&euro;</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Conditions par lieux</h2>
                </div>
                <div class="box-body">
                    @foreach($lieux as $lieu)
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="lieu-{{ $lieu->id }}">
                                {{ $lieu->nom }}
                            </label>
                            <div class="col-sm-1">
                                <input class="form-check-input" type="checkbox" name="lieux[{{ $lieu->id }}][has]" value="{{ $lieu->id }}" id="lieu-{{ $lieu->id }}" {{ old('lieux.'.$lieu->id.'.has', $prestation->lieux->contains($lieu)) ? 'checked' : '' }}>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="lieux[{{ $lieu->id }}][montant]" step=".001" value="{{ old('lieux.'.$lieu->id.'.montant', $prestation->lieux->contains($lieu) ? $prestation->lieux->find($lieu)->pivot->montant : null) }}">
                                <span id="emailHelp" class="form-text text-muted">Montant à ajouter en &nbsp;&euro;</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('ipsum/admin/dist/tinymce.js') }}"></script>

    {{ Aire::close() }}
    
@endsection
