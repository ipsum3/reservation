@extends('IpsumAdmin::layouts.app')
@section('title', 'Durée')

@section('content')

    <h1 class="main-title">Tranche de durée</h1>

    {{ Aire::open()->route($duree->exists ? 'admin.duree.update' : 'admin.duree.store', $duree->exists ? $duree : null)->bind($duree)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreDuree::class) }}
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">{{ $duree->exists ? 'Modification' : 'Ajout' }}</h3>
            </div>
            <div class="box-body">
                <div class="form-row">
                    {{ Aire::input('min', 'Mini (jour)*')->groupAddClass('col-md-6') }}
                    {{ Aire::input('max', 'Maxi (jour)')->groupAddClass('col-md-6') }}
                </div>
            </div>
            <div class="box-footer">
                <div><button class="btn btn-outline-secondary" type="reset">Annuler</button></div>
                <div><button class="btn btn-primary" type="submit">Enregistrer</button></div>
            </div>
        </div>
    {{ Aire::close() }}

@endsection
