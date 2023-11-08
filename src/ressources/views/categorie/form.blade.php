@extends('IpsumAdmin::layouts.app')
@section('title', 'Catégories')

@section('content')

    <h1 class="main-title">Catégorie</h1>

    {{ Aire::open()->route($categorie->exists ? 'admin.categorie.update' : 'admin.categorie.store', $categorie->exists ? [$categorie, request()->route('locale')] : '')->bind($categorie)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreCategorie::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $categorie->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                @if ($categorie->exists and count(config('ipsum.translate.locales')) > 1)
                    <ul class="nav nav-tabs mr-5" role="tablist">
                        @foreach(config('ipsum.translate.locales') as $locale)
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->route('locale') == $locale['nom'] or (request()->route('locale') === null and config('ipsum.translate.default_locale') == $locale['nom'])) ? 'active' : '' }}" href="{{ route('admin.categorie.edit', [$categorie, $locale['nom']]) }}" aria-selected="true">{{ $locale['intitule'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($categorie->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.categorie.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-danger" href="{{ route('admin.categorie.destroy', $categorie) }}" data-toggle="tooltip" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::input('nom', 'Nom*')->groupAddClass('col-md-6') }}
                {{ Aire::input('modeles', 'Modéles*')->groupAddClass('col-md-6') }}
            </div>
            @if ($types->count() > 1)
                {{ Aire::select(collect(['' => '---- Types -----'])->union($types), 'type_id', 'Type*') }}
            @else
                {{ Aire::hidden('type_id', $types->take(1)->keys()->first()) }}
            @endif

            {{ Aire::textArea('description', 'Description')->class('tinymce-simple') }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">
                        Caractéristiques
                    </h2>
                </div>
                <div class="box-body">

                    <div class="form-row">
                        {{ Aire::number('place', 'Place*')->groupAddClass('col-md-4') }}
                        {{ Aire::number('porte', 'Porte*')->groupAddClass('col-md-4') }}
                        {{ Aire::number('bagage', 'Nbr bagages')->groupAddClass('col-md-4') }}
                    </div>
                    <div class="form-row">
                        {{ Aire::number('volume', 'Volume (m³)')->groupAddClass('col-md-4') }}
                    </div>
                    <div class="form-row">
                        {{ Aire::select(collect(['' => '---- Transmissions -----'])->union($transmissions), 'transmission_id', 'Transmission*')->required()->groupAddClass('col-md-4') }}
                        {{ Aire::select(collect(['' => '---- Motorisations -----'])->union($motorisations), 'motorisation_id', 'Motorisation*')->required()->groupAddClass('col-md-4') }}
                        {{ Aire::select($carrosseries, 'carrosseries', 'Carrosserie*')->multiple()->required()->groupAddClass('col-md-4')->class('js-example-basic-single form-control') }}
                    </div>
                    <div class="form-row">
                        {{ Aire::radioGroup([0 => 'non', 1 => 'oui'], 'climatisation', 'Climatisation')->defaultValue(1)->groupAddClass('col-md-4') }}</div>
                    <div class="form-row">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            @if (config('ipsum.reservation.categorie.custom_fields'))
                <div class="box">
                    <div class="box-header">
                        <h2 class="box-title">
                            Informations complémentaires
                        </h2>
                    </div>
                    <div class="box-body">
                        @foreach(config('ipsum.reservation.categorie.custom_fields') as $field)
                            @php
                                $field_name = 'custom_fields['.$field['name'].']';
                                $field_value = old('custom_fields.'.$field['name'], $categorie->custom_fields->{$field['name']} ?  : ($field['type'] == "repeater" ? [] : '') );
                            @endphp
                            <x-admin::custom :field="$field" :name="$field_name" :value="$field_value"/>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">
                        Réservation
                    </h2>
                </div>
                <div class="box-body">
                    <div class="form-row">
                        {{ Aire::number('age_minimum', 'Age minimum*')->groupAddClass('col-md-6') }}
                        {{ Aire::number('annee_permis_minimum', "Années minimum*")->groupAddClass('col-md-6')->helpText("Nombre d'années de permis minimum") }}
                        {{ Aire::number('caution', 'Caution (€)')->setAttribute('step', 0.01)->groupAddClass('col-md-6') }}
                        {{ Aire::number('franchise', 'Franchise (€)')->setAttribute('step', 0.01)->groupAddClass('col-md-6') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Médias</h2>
        </div>
        <div class="box-body">
            <div class="upload"
                 data-uploadendpoint="{{ route('admin.media.store') }}"
                 data-uploadmedias="{{ route('admin.media.publication', ['publication_type' => \Ipsum\Reservation\app\Models\Categorie\Categorie::class, 'publication_id' => $categorie->exists ? $categorie->id : '']) }}"
                 data-uploadrepertoire="categorie"
                 data-uploadpublicationid="{{ $categorie->id }}"
                 data-uploadpublicationtype="{{ \Ipsum\Reservation\app\Models\Categorie\Categorie::class }}"
                 data-uploadgroupe=""
                 data-uploadnote="Images et documents, poids maximum {{ config('ipsum.media.upload_max_filesize') }} Ko"
                 data-uploadmaxfilesize="{{ config('ipsum.media.upload_max_filesize') }}"
                 data-uploadmmaxnumberoffiles=""
                 data-uploadminnumberoffiles=""
                 data-uploadallowedfiletypes=""
                 data-uploadcsrftoken="{{ csrf_token() }}">
                <div class="upload-DragDrop"></div>
                <div class="upload-ProgressBar"></div>
                <div class="upload-alerts mt-3"></div>
                <div class="mt-3">
                    <h3>Médias associés :</h3>
                    <div class="d-flex flex-row flex-wrap sortable upload-files" data-sortableurl="{{ route('admin.media.changeOrder') }}" data-sortablecsrftoken="{{ csrf_token() }}">
                    </div>
                </div>
            </div>
        </div>
        <link href="{{ asset('ipsum/admin/dist/uppy.css') }}" rel="stylesheet">
        <script src="{{ asset('ipsum/admin/dist/uppy.js') }}"></script>
    </div>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Seo</h2>
        </div>
        <div class="box-body">
            {{ Aire::textArea('texte', 'Texte')->class('tinymce')->data('medias', route('admin.media.popin')) }}
            @if(auth()->user()->isSuperAdmin())
                {{ Aire::input('seo_title', 'Balise title') }}
                {{ Aire::input('seo_description', 'Balise description') }}
            @endif
        </div>
    </div>
    {{ Aire::close() }}

    <script src="{{ asset('ipsum/admin/dist/tinymce.js') }}"></script>

@endsection
