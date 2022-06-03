@extends('IpsumAdmin::layouts.app')
@section('title', 'Jours fériés')

@section('content')

    <h1 class="main-title">Jours fériés</h1>

    {{ Aire::open()->route($ferie->exists ? 'admin.lieuFerie.update' : 'admin.lieuFerie.store', $ferie->exists ? [$ferie] : '')->bind($ferie)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreLieuFerie::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $ferie->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($ferie->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.lieuFerie.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-danger" href="{{ route('admin.lieuFerie.destroy', $ferie) }}" data-toggle="tooltip" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::input('nom', 'Nom (raison)')->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Lieux -----'])->union($lieux), 'lieu_id', 'Lieux')->groupAddClass('col-md-6') }}
                {{ Aire::date('jour_at', 'Date*')->groupAddClass('col-md-6') }}
            </div>
        </div>
    </div>
    {{ Aire::close() }}


@endsection
