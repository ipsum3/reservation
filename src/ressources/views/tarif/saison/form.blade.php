@extends('IpsumAdmin::layouts.app')
@section('title', 'Saisons')

@section('content')

    <h1 class="main-title">Saison</h1>

    {{ Aire::open()->route($saison->exists ? 'admin.saison.update' : 'admin.saison.store', $saison->exists ? [$saison] : '')->bind($saison)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreSaison::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $saison->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($saison->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.saison.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-secondary" href="{{ route('admin.saison.cloner', $saison) }}" data-toggle="tooltip" title="Cloner">
                        <i class="fas fa-clone"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-danger" href="{{ route('admin.saison.destroy', $saison) }}" data-toggle="tooltip" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::input('nom', 'Nom (raison)')->groupAddClass('col-md-12') }}
                {{ Aire::date('debut_at', 'Début*')->groupAddClass('col-md-6') }}
                {{ Aire::date('fin_at', 'Fin*')->groupAddClass('col-md-6') }}
            </div>
        </div>
    </div>
    {{ Aire::close() }}


@endsection
