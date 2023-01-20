@extends('IpsumAdmin::layouts.app')
@section('title', 'Paiements')

@section('content')

    <h1 class="main-title">Paiement <small>({{ $paiement->exists ? 'Modification' : 'Ajout' }})</small></h1>

    {{ Aire::open()->route('admin.paiement.update', [$paiement->id])->bind($paiement)->formRequest(\Ipsum\Reservation\app\Http\Requests\UpdatePaiement::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Paiement</h2>
            <div class="btn-toolbar">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::date('created_at', 'Date')->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Moyens -----'])->union($moyens->pluck('nom', 'id')), 'paiement_moyen_id', 'Moyen')->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Types -----'])->union($types->pluck('nom', 'id')), 'paiement_type_id', 'Moyen')->groupAddClass('col-md-6') }}
                {{ Aire::number('montant', 'Montant')->groupAddClass('col-md-6') }}
                {{ Aire::textArea('note', 'Note')->groupAddClass('col-md-6') }}
            </div>
        </div>
    </div>
    {{ Aire::close() }}

@endsection
