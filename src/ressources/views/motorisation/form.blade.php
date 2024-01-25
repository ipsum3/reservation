@extends('IpsumAdmin::layouts.app')
@section('title', 'Carrosseries')

@section('content')

    <h1 class="main-title">Motorisation</h1>

    {{ Aire::open()->route($motorisation->exists ? 'admin.motorisation.update' : 'admin.motorisation.store', $motorisation->exists ? [$motorisation, request()->route('locale')] : '')->bind($motorisation)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreMotorisation::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $motorisation->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($motorisation->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.carrosserie.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    {{--<a class="btn btn-outline-danger" href="{{ route('admin.carrosserie.destroy', $motorisation) }}" data-toggle="tooltip" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>--}}
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::number('montant', 'Montant')->groupAddClass('col-md-6')->step('0.01')->required() }}
            </div>
        </div>
    </div>
    {{ Aire::close() }}


@endsection
