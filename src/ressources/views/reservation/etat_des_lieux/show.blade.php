@extends('IpsumAdmin::layouts.app')
@section('title', 'Inspection')

@section('content')

    <h1 class="main-title">État des lieux - Inspection {{ strtolower($type->nom) }}</h1>

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Récapitulatif</h2>
                    <div class="btn-toolbar">
                       {{-- <a href="{{ route('admin.reservation.departEtRetour') }}" id="prevBtn" class="btn btn-secondary">Départ / Retour</a>--}}
                        @if ($type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
                            <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.contratSigne', [$inspection->reservation]) }}" target="_blank"><i class="fa fa-file-download"></i> Contrat signé</a>&nbsp;
                        @endif
                        <a class="btn btn-outline-secondary" href="{{ route('admin.inspection.pdf', [$inspection]) }}" target="_blank"><i class="fa fa-file-download"></i> Etat des lieux signé</a>
                    </div>
                </div>
                <div class="box-body">

                    @include('IpsumReservation::reservation.etat_des_lieux.step.recap._recap')

                </div>
            </div>

        </div>

    </div>

@endsection