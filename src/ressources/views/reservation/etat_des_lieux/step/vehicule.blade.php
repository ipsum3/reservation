@extends('IpsumAdmin::layouts.app')
@section('title', 'Inspection')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux.step._progressbar')

    <h1 class="main-title">État des lieux - Inspection {{ $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'initiale': 'finale' }}</h1>

    {{ Aire::open()->id('reservation')->route('admin.inspection.vehicule.store', [$reservation, $type])->bind($reservation)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionVehicule::class) }}

    <div class="row">
        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Véhicule</h2>
                </div>
                <div class="box-body">

                    <div id="vehicule-alert" class="alert alert-warning" style="display: none"></div>

                    @if(isset($conflicts) and $conflicts->count())
                        <div class="alert alert-danger">
                            <p><strong><i class="fas fa-exclamation-triangle"></i> Conflits potentiels :</strong></p>
                            <ul>
                                @foreach($conflicts as $conflict)
                                    @if(get_class($conflict) != \Ipsum\Reservation\app\Models\Reservation\Reservation::class)
                                        <li>La réservation est en conflit avec l'intervention {{ "#".$conflict->id }} "{{ $conflict->type->nom }}" du {{ $conflict->debut_at->format('d/m/Y H:i') }} au {{ $conflict->fin_at->format('d/m/Y H:i') }}</li>
                                    @else
                                        <li>La réservation est en conflit avec la réservation {{ "#".$conflict->reference }} du {{ $conflict->debut_at->format('d/m/Y H:i') }} au {{ $conflict->fin_at->format('d/m/Y H:i') }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-row">
                        {{
                            Aire::select(collect(['' => '---- Catégories -----'])
                                ->union($categories), 'categorie_id', 'Catégorie*')
                                ->id('reservation-categorie')
                                ->data('ajax-url', route('admin.reservation.vehiculeSelect', ['vehicule_id' => $reservation->vehicule_id]))
                                ->value(old('categorie_id', $reservation->categorie_id))
                                ->required()
                                ->groupAddClass('col-md-6')
                        }}
                        @if ($reservation->is_confirmed and $vehicules->count())
                            <div class="form-group col-md-6">
                                <label for="vehicule_id">Véhicule</label>
                                <div id="vehicule-select">
                                    @include('IpsumReservation::reservation._vehicules_select', ['vehicule_id' => old('vehicule_id', $reservation->vehicule_id)])
                                </div>
                                @error('vehicule_id')
                                <ul class="invalid-feedback d-block">
                                    <li>{{ $message }}</li>
                                </ul>
                                @enderror
                            </div>
                            <input type="hidden" name="vehicule_blocage" value="0">
                            <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                            <input type="hidden" name="debut_at" value="{{ $reservation->debut_at->format("Y-m-d\TH:i") }}">
                            <input type="hidden" name="fin_at" value="{{ $reservation->fin_at->format("Y-m-d\TH:i") }}">
                            {{ Aire::checkbox('vehicule_blocage', 'Bloquer le véhicule sur cette réservation')->checked()->groupAddClass('d-none col-md-6 offset-md-6') }}
                        @endif

                    </div>
                </div>

                <div class="box-footer">
                    <div><a href="{{ route('admin.reservation.edit', $reservation) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div>
                        <button type="submit" id="nextBtn" class="btn btn-primary">Suivant</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ Aire::close() }}

@endsection