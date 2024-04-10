@extends('IpsumAdmin::layouts.app')
@section('title', 'Lieux')

@section('content')

    <h1 class="main-title">Lieux</h1>

    {{ Aire::open()->route($lieu->exists ? 'admin.lieu.update' : 'admin.lieu.store', $lieu->exists ? [$lieu, request()->route('locale')] : '')->bind($lieu)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreLieu::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $lieu->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                @if ($lieu->exists and count(config('ipsum.translate.locales')) > 1)
                    <ul class="nav nav-tabs mr-5" role="tablist">
                        @foreach(config('ipsum.translate.locales') as $locale)
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->route('locale') == $locale['nom'] or (request()->route('locale') === null and config('ipsum.translate.default_locale') == $locale['nom'])) ? 'active' : '' }}" href="{{ route('admin.lieu.edit', [$lieu, $locale['nom']]) }}" aria-selected="true">{{ $locale['intitule'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($lieu->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.lieu.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-danger" href="{{ route('admin.lieu.destroy', $lieu) }}" data-toggle="tooltip" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::input('nom', 'Nom*')->groupAddClass('col-md-6') }}
                @if ($types->count() > 1)
                    {{ Aire::select(collect(['' => '---- Types -----'])->union($types), 'type_id', 'Type*')->groupAddClass('col-md-6') }}
                @else
                    {{ Aire::hidden('type_id', $types->take(1)->keys()->first()) }}
                @endif
                {{ Aire::select(array_combine(old('emails', $lieu->emails ?? []), old('emails', $lieu->emails ?? [])), 'emails', 'Emails contact*')->multiple()->groupAddClass('col-md-6')->addClass('js-example-basic-single js-states')->data('tags', '1')->data('token-separators', "[',', ' ', ';]") }}
                {{ Aire::select(array_combine(old('emails_reservation', $lieu->emails_reservation ?? []), old('emails_reservation', $lieu->emails_reservation ?? [])), 'emails_reservation', 'Emails réservation*')->multiple()->groupAddClass('col-md-6')->addClass('js-example-basic-single js-states')->data('tags', '1')->data('token-separators', "[',', ' ', ';]") }}
                {{ Aire::input('telephone', 'Téléphone*')->groupAddClass('col-md-6') }}
                {{ Aire::input('gps', 'Coordonnées GPS')->groupAddClass('col-md-6') }}
                {{ Aire::textArea('adresse', 'Adresse*')->groupAddClass('col-md-6') }}
                {{ Aire::textArea('instruction', 'Instruction')->groupAddClass('col-md-6') }}
                {{ Aire::textArea('horaires_txt', 'Horaires*')->groupAddClass('col-md-6') }}
            </div>

        </div>
    </div>
    @if (config('ipsum.reservation.lieu.custom_fields'))
        <div class="box">
            <div class="box-header">
                <h2 class="box-title">
                    Informations complémentaires
                </h2>
            </div>
            <div class="box-body">
                @foreach(config('ipsum.reservation.lieu.custom_fields') as $field)
                    @php
                        $field_name = 'custom_fields['.$field['name'].']';
                        $field_value = old('custom_fields.'.$field['name'], $lieu->custom_fields->{$field['name']} ?? ($field['type'] == "repeater" ? [] : '') );
                    @endphp
                    <x-admin::custom :field="$field" :name="$field_name" :value="$field_value"/>
                @endforeach
            </div>
        </div>
    @endif
    @if(auth()->user()->isSuperAdmin())
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Seo</h2>
        </div>
        <div class="box-body">
            {{ Aire::input('seo_title', 'Balise title') }}
            {{ Aire::input('seo_description', 'Balise description') }}
            {{ Aire::input('slug', 'Slug') }}
        </div>
    </div>
    @endif
    {{ Aire::close() }}

    @if ($lieu->exists)
        <div class="row">
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header">
                        <h2 class="box-title">
                            Créneaux horaires
                        </h2>
                    </div>
                    <div class="box-body">
                        @foreach($lieu->horaires()->orderBy('jour')->get() as $horaire)
                            {{ Aire::open()->route('admin.lieu.updateHoraire', $horaire) }}
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <select name="jour" class="form-control" required>
                                            <option value="">----- Jours -----</option>
                                            @foreach(\Ipsum\Reservation\app\Models\Lieu\Horaire::JOURS as $key => $jour)
                                                <option value="{{ $key }}" {{ $horaire->jour == $key ? 'selected'  : '' }}>{{ $jour }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="time" class="form-control" name="debut" value="{{ $horaire->debut }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="time" class="form-control" name="fin" value="{{ $horaire->fin }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-outline-primary" type="submit" data-toggle="tooltip" title="Enregistrer"><i class="fas fa-save"></i></button>&nbsp;
                                        <a class="btn btn-outline-danger" href="{{ route('admin.lieu.destroyHoraire', $horaire) }}" data-toggle="tooltip" title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            {{ Aire::close() }}
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header">
                        <h2 class="box-title">
                            Ajouter un créneau horaire
                        </h2>
                    </div>
                    <div class="box-body">
                        {{ Aire::open()->route('admin.lieu.storeHoraire', $lieu) }}
                            <div class="form-row">
                                <div class="col-md-4">
                                    <select name="jour" class="form-control" id="lieu-{{ $lieu->id }}" required>
                                        <option value="">----- Jours -----</option>
                                        @foreach(\Ipsum\Reservation\app\Models\Lieu\Horaire::JOURS as $key => $jour)
                                            <option value="{{ $key }}" >{{ $jour }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="time" class="form-control" name="debut" value="08:00" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="time" class="form-control" name="fin" value="21:00" required>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-primary" type="submit" data-toggle="tooltip" title="Créer"><i class="fas fa-plus"></i></button>&nbsp;
                                </div>
                            </div>
                        </div>
                    {{ Aire::close() }}
                </div>
            </div>
        </div>
    @endif

    <script src="{{ asset('ipsum/admin/dist/tinymce.js') }}"></script>
@endsection
