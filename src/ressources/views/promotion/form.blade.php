@extends('IpsumAdmin::layouts.app')
@section('title', 'Promotions')

@section('content')

    <h1 class="main-title">Promotions</h1>

    {{ Aire::open()->route($promotion->exists ? 'admin.promotion.update' : 'admin.promotion.store', $promotion->exists ? [$promotion] : '')->bind($promotion)->formRequest(\Ipsum\Reservation\app\Http\Requests\StorePromotion::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $promotion->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($promotion->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.promotion.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-danger" href="{{ route('admin.promotion.destroy', $promotion) }}" data-toggle="tooltip" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::hidden('type', 'reduction')->required() }}
                {{ Aire::input('nom', 'Nom*')->required()->groupAddClass('col-md-6') }}
                {{ Aire::input('reference', 'Réfèrence')->groupAddClass('col-md-6') }}

                {{ Aire::select(\Ipsum\Reservation\app\Models\Promotion\Promotion::REDUCTION_TYPES, 'reduction_type', 'Type de réduction')->required()->groupAddClass('col-md-6') }}
                {{ Aire::number('reduction_valeur', 'Valeur de réduction')->step(.01)->groupAddClass('col-md-6') }}
            </div>


            {{ Aire::textArea('extrait', 'Extrait')->class('tinymce-simple') }}

            {{ Aire::textArea('texte', 'Texte')->class('tinymce')->data('medias', route('admin.media.popin')) }}
            <script src="{{ asset('ipsum/admin/dist/tinymce.js') }}"></script>
        </div>

    </div>

    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Conditions</h2>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::select(collect(['' => '---- Conditions -----'])->union($conditions), 'condition_paiement_id', 'Condition')->groupAddClass('col-md-6') }}
            </div>
            <div class="form-row">
                {{ Aire::input('code', 'Code')->helpText("Code promo à transmettre au client affin de bénéficier de l'offre")->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Clients -----'])->union($clients), 'client_id', 'Client')->groupAddClass('col-md-6') }}
            </div>
            <div class="form-row">
                {{ Aire::date('debut_at', 'Valable à partir du*')->required()->groupAddClass('col-md-6') }}
                {{ Aire::date('fin_at', "Valable jusqu'au*")->required()->groupAddClass('col-md-6') }}
                {{ Aire::date('activation_at', "Date d'activation")->groupAddClass('col-md-6') }}
                {{ Aire::date('desactivation_at', "Date de désactivation")->groupAddClass('col-md-6') }}
            </div>
            <div class="form-row">
                {{ Aire::number('duree_min', 'Durée de réservation minimum')->groupAddClass('col-md-6') }}
                {{ Aire::number('duree_max', 'Durée de réservation maximum')->groupAddClass('col-md-6') }}
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-4">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Conditions par catégories</h2>
                </div>
                <div class="box-body">
                    @foreach($categories as $categorie)
                        <div class="form-group row">
                            <label class="col-sm-7 col-form-label" for="categorie-{{ $categorie->id }}">
                                Catégorie {{ $categorie->nom }}
                            </label>
                            <div class="col-sm-1">
                                <input class="form-check-input" type="checkbox" name="categories[{{ $categorie->id }}][has]" value="{{ $categorie->id }}" id="categorie-{{ $categorie->id }}" {{ old('categories.'.$categorie->id.'.has', $promotion->categories->contains($categorie)) ? 'checked' : '' }}>
                            </div>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="categories[{{ $categorie->id }}][reduction]" step=".001" value="{{ old('categories.'.$categorie->id.'.montant', $promotion->categories->contains($categorie) ? $promotion->categories->find($categorie)->pivot->reduction : null) }}">
                                <span id="emailHelp" class="form-text text-muted">Valeur à déduire</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Conditions par prestations</h2>
                </div>
                <div class="box-body">
                    @error('prestations')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    @foreach($prestations as $prestation)
                        <div class="form-group row">
                            <label class="col-sm-7 col-form-label" for="prestation-{{ $prestation->id }}">
                                {{ $prestation->nom }}
                            </label>
                            <div class="col-sm-1">
                                <input class="form-check-input" type="checkbox" name="prestations[{{ $prestation->id }}][has]" value="{{ $prestation->id }}" id="prestation-{{ $prestation->id }}" {{ old('prestations.'.$prestation->id.'.has', $promotion->prestations->contains($prestation)) ? 'checked' : '' }}>
                            </div>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="prestations[{{ $prestation->id }}][reduction]" step=".001" value="{{ old('prestations.'.$prestation->id.'.montant', $promotion->prestations->contains($prestation) ? $promotion->prestations->find($prestation)->pivot->reduction : null) }}">
                                <span id="emailHelp" class="form-text text-muted">Valeur à déduire</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Conditions par lieux de départ</h2>
                </div>
                <div class="box-body">
                    @foreach($lieux as $lieu)
                        <div class="form-group row">
                            <label class="col-sm-11 col-form-label" for="lieu-{{ $lieu->id }}">
                                {{ $lieu->nom }}
                            </label>
                            <div class="col-sm-1">
                                <input class="form-check-input" type="checkbox" name="lieux[{{ $lieu->id }}][has]" value="{{ $lieu->id }}" id="lieu-{{ $lieu->id }}" {{ old('lieux.'.$lieu->id.'.has', $promotion->lieux->contains($lieu)) ? 'checked' : '' }}>
                            </div>
                            {{--<div class="col-sm-8">
                                <input type="number" class="form-control" name="lieux[{{ $lieu->id }}][reduction]" step=".001" value="{{ old('lieux.'.$lieu->id.'.montant', $promotion->lieux->contains($lieu) ? $promotion->lieux->find($lieu)->pivot->reduction : null) }}">
                                <span id="emailHelp" class="form-text text-muted">Montant à ajouter en &nbsp;&euro;</span>
                            </div>--}}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    @if(auth()->user()->isSuperAdmin())
        <div class="box">
            <div class="box-header">
                <h2 class="box-title">Seo</h2>
            </div>
            <div class="box-body">
                {{ Aire::input('seo_title', 'Balise title') }}
                {{ Aire::input('seo_description', 'Balise description') }}
                {{ Aire::input('slug', 'Slug')->placeholder($promotion->exists ? $promotion->slug : null)->value('')->helpText('En cas de modification, pensez à modifier tous les liens vers cet article.') }}
            </div>
        </div>
    @endif

    {{ Aire::close() }}
    
@endsection
