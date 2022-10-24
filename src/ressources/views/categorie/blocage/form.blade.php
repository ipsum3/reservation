@extends('IpsumAdmin::layouts.app')
@section('title', 'Blocages')

@section('content')

    <h1 class="main-title">Blocage | Stop sell</h1>

    {{ Aire::open()->route($blocage->exists ? 'admin.categorieBlocage.update' : 'admin.categorieBlocage.store', $blocage->exists ? [$blocage] : '')->bind($blocage)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreCategorieBlocage::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $blocage->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($blocage->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.categorieBlocage.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-danger" href="{{ route('admin.categorieBlocage.destroy', $blocage) }}" data-toggle="tooltip" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::input('nom', 'Nom')->groupAddClass('col-md-6')->helpText('Raison du blocage. Facultatif et non visible par les clients.') }}
                {{ Aire::select(collect(['' => '---- Catégories -----'])->union($categories), 'categorie_id', 'Catégorie*')->groupAddClass('col-md-6') }}
                {{ Aire::date('debut_at', 'Début*')->groupAddClass('col-md-6')->helpText('Date incluse') }}
                {{ Aire::date('fin_at', 'Fin*')->groupAddClass('col-md-6')->helpText('Date incluse') }}
            </div>
        </div>
    </div>
    {{ Aire::close() }}


@endsection
