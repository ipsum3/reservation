@extends('IpsumAdmin::layouts.app')
@section('title', 'État des lieux')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux.step._progressbar')

    <h1 class="main-title">État des lieux {{ strtolower($inspection->type->nom) }} <a href="{{ route('admin.reservation.edit', $inspection->reservation) }}"><small class="text-muted">(résa. {{ $inspection->reservation->reference }})</small></a></h1>

    {{ Aire::open()->id('reservation')->route('admin.inspection.vehicule.store', [$inspection])->bind($inspection)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionVehicule::class) }}

    <div class="row">
        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Véhicule</h2>
                </div>
                <div class="box-body">

                    <div id="vehicule-alert" class="alert alert-warning" style="display: none"></div>

                    @include('IpsumReservation::reservation._conflicts')

                    <div class="form-row">
                        {{
                            Aire::select(collect(['' => '---- Catégories -----'])
                                ->union($categories), 'categorie_id', 'Catégorie*')
                                ->id('reservation-categorie')
                                ->data('ajax-url', route('admin.reservation.vehiculeSelect', ['vehicule_id' => $inspection->vehicule_id ?? $inspection->reservation->vehicule_id]))
                                ->value(old('categorie_id', $inspection->reservation->categorie_id))
                                ->required()
                                ->groupAddClass('col-md-6')
                        }}
                        @if ($inspection->reservation->is_confirmed and $vehicules->count())
                            <div class="form-group col-md-6">
                                <label for="vehicule_id">Véhicule</label>
                                <div id="vehicule-select">
                                    @include('IpsumReservation::reservation._vehicules_select', ['vehicule_id' => old('vehicule_id', $inspection->vehicule_id ?? $reservation->vehicule_id)])
                                </div>
                                @error('vehicule_id')
                                <ul class="invalid-feedback d-block">
                                    <li>{{ $message }}</li>
                                </ul>
                                @enderror
                            </div>
                            <input type="hidden" name="reservation_id" value="{{ $inspection->reservation->id }}">
                            <input type="hidden" name="debut_at" value="{{ $inspection->reservation->debut_at->format("Y-m-d\TH:i") }}">
                            <input type="hidden" name="fin_at" value="{{ $inspection->reservation->fin_at->format("Y-m-d\TH:i") }}">
                        @endif

                    </div>
                </div>

                <div class="box-footer">
                    <div><a href="{{ route('admin.reservation.edit', $inspection->reservation) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div>
                        <button type="submit" id="nextBtn" class="btn btn-primary">Suivant</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ Aire::close() }}

@endsection