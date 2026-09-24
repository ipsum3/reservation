@extends('IpsumAdmin::layouts.app')
@section('title', 'Création de facture')

@section('content')

    <h1 class="main-title">Création d'une facture pour la location {{ $reservation->reference }}</h1>

    {{ Aire::open()->route('admin.facture.store', [$reservation])->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreFacture::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Création</h2>
            <div class="btn-toolbar">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Créer et envoyer la facture</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::date('nom', 'Nom*')->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Types -----'])->union(collect(\Ipsum\Reservation\app\Enum\FactureType::pluck())), 'type_id', 'Type*')->groupAddClass('col-md-6') }}
            </div>
        </div>
    </div>
    {{ Aire::close() }}
@endsection
