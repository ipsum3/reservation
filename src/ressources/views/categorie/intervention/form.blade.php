@extends('IpsumAdmin::layouts.app')
@section('title', 'Interventions')

@section('content')

    <h1 class="main-title">Intervention</h1>

    {{ Aire::open()->route($intervention->exists ? 'admin.intervention.update' : 'admin.intervention.store', $intervention->exists ? [$intervention] : '')->bind($intervention)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreIntervention::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $intervention->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($intervention->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.intervention.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    <a class="btn btn-outline-danger" href="{{ route('admin.intervention.destroy', $intervention) }}" data-toggle="tooltip" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::select(collect(['' => '---- Types -----'])->union($types), 'type_id', 'Type*')->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Véhicules -----'])->union($vehicules), 'vehicule_id', 'Véhicule*')->defaultValue(old('vehicule_id', request('vehicule_id', $intervention->vehicule_id)))->class('js-example-basic-single js-states form-control')->groupAddClass('col-md-6') }}
                {{ Aire::dateTimeLocal('debut_at', 'Début*')->defaultValue(\Carbon\Carbon::now()->startOfHour())->required()->groupAddClass('col-md-6')->helpText('Date incluse') }}
                {{ Aire::dateTimeLocal('fin_at', 'Fin*')->defaultValue(\Carbon\Carbon::now()->startOfHour())->required()->groupAddClass('col-md-6')->helpText('Date incluse') }}
                {{ Aire::input('intervenant', 'Intervenant')->groupAddClass('col-md-6') }}
                {{ Aire::textArea('information', 'Information')->groupAddClass('col-md-6') }}
            </div>
        </div>
    </div>
    {{ Aire::close() }}


@endsection
