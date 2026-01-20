@extends('IpsumAdmin::layouts.app')
@section('title', 'Inspection')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux.step._progressbar')

    <h1 class="main-title">État des lieux - Inspection {{ $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'initiale': 'finale' }}</h1>

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Récapitualtif</h2>
                </div>
                <div class="box-body">

                    @include('IpsumReservation::reservation.etat_des_lieux.step.recap._recap')

                </div>

                <div class="box-footer">
                    <div><a href="{{ route('admin.inspection.dommages', [$reservation, $type]) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div><a href="{{ route('admin.inspection.signature.locataire', [$reservation, $type]) }}" id="nextBtn" class="btn btn-primary">Suivant</a></div>
                </div>
            </div>

        </div>

    </div>

@endsection