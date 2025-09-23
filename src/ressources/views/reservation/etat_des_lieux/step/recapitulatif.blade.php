@extends('IpsumAdmin::layouts.app')
@section('title', 'Inspection')

@section('content')

    <h1 class="main-title">État des lieux - Inspection {{ $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'initiale': 'finale' }}</h1>

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    @include('IpsumReservation::reservation.etat_des_lieux._progressbar')

                    <!-- Progress bar -->
                    <ul class="progressbar mt-2 clearfix overflow-auto">
                        @if($type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
                            <li>Véhicule</li>
                            <li>Client / Réservation</li>
                        @endif
                        <li>Kilométrage / Carburant / Checklist</li>
                        <li>Dommages</li>
                        <li>Photos</li>
                        <li class="active">Récapitulatif</li>
                        <li>Signature client</li>
                        <li>Signature agent</li>
                    </ul>
                </div>
                <div class="box-body">


                    <div class="step active" id="ajax-recap">
                        @include('IpsumReservation::reservation.etat_des_lieux.step.recap._recap')
                    </div>

                    <!-- Navigation -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ URL::previous() }}" id="prevBtn" class="btn btn-secondary">Retour</a>
                        <a href="{{ route('admin.inspection.signature.locataire', [$reservation, $type]) }}" id="nextBtn" class="btn btn-primary">Suivant</a>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection