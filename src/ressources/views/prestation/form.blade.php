@extends('IpsumAdmin::layouts.app')
@section('title', 'Prestations')

@section('content')

    <h1 class="main-title">Prestations</h1>

    {{ Aire::open()->route($prestation->exists ? 'admin.prestation.update' : 'admin.prestation.store', $prestation->exists ? [$prestation] : '')->bind($prestation)->formRequest(\Ipsum\Reservation\app\Http\Requests\StorePrestation::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $prestation->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
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
                {{ Aire::input('nom', 'Nom*')->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Types -----'])->union($types), 'type_id', 'Type*')->groupAddClass('col-md-6') }}
            </div>

        </div>
    </div>
    {{ Aire::close() }}
    
@endsection
