@extends('IpsumAdmin::layouts.app')
@section('title', 'Inspection')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux._progressbar')

    <h1 class="main-title">État des lieux - Inspection {{ $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'initiale': 'finale' }}</h1>

    {{ Aire::open()->id('reservation')->route('admin.inspection.checklist.store', [$reservation, $type])->bind($inspection)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionChecklist::class) }}

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Checklist</h2>
                    <div></div>

                    {{--@include('IpsumReservation::reservation.etat_des_lieux._progressbar')

                    <!-- Progress bar -->
                    <ul class="progressbar mt-2 clearfix overflow-auto">
                        @if($type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
                            <li><a href="{{ route('admin.inspection.vehicule', [$reservation, $type]) }}">Véhicule</a></li>
                            <li><a href="{{ route('admin.inspection.client', [$reservation, $type]) }}">Client / Réservation</a></li>
                        @endif
                        <li class="active">Kilométrage / Carburant / Checklist</li>
                        <li>Dommages</li>
                        <li>Photos</li>
                        <li>Récapitulatif</li>
                        <li>Signature client</li>
                        <li>Signature agent</li>
                    </ul>--}}
                </div>
                <div class="box-body">

                    <div class="row">
                        {{ Aire::number('kilometrage', 'Kilométrage (km)*')->required()->value(old('kilometrage', $inspection->kilometrage ?? ($reservation->vehicule->last_inspection->kilometrage ?? '') ))->helpText(( $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale) ? 'Kilométrage initial : '.$reservation->inspection_initiale?->kilometrage.' km' : '')->groupAddClass('col-md-12') }}
                        {{ Aire::range('carburant', 'Niveau de carburant*')->step('1')->min(0)->max(8)->value(old('carburant', $inspection->carburant ?? ($reservation->vehicule->last_inspection->carburant ?? 8) ))->list('markers')->id('carburant')->helpText(( $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale) ? 'Niveau de carburant initial : '.$reservation->inspection_initiale?->carburant.'/8' : '')->groupAddClass('col-md-11') }}
                        <p class="d-flex pl-3">
                            <output id="carburant_value"></output>
                        </p>
                        <datalist id="markers">
                            @for($i=0; $i<=8; $i++)
                                <option value="{{ $i }}" label="{{ $i }}"></option>
                            @endfor
                        </datalist>
                        <script>
                            const value = document.querySelector("#carburant_value");
                            const input = document.querySelector("#carburant");
                            value.textContent = input.value + '/8';
                            input.addEventListener("input", (event) => {
                                value.textContent = event.target.value + '/8';
                            });
                        </script>

                        @if(( $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale))
                            <div class="form-group col-md-4 alert alert-light">
                                <label class=" cursor-pointer" data-aire-component="label" for="checklists">
                                    Checklist initiale
                                </label>
                                @foreach($checklists as $checklist)
                                    <div class="form-check mt-2">
                                        @if(in_array($checklist->id, old('checklists', $reservation->inspection_initiale?->checklists->pluck('id')->toArray() ?? [])))
                                            <i class="fa fa-check-square text-success"></i>
                                        @else
                                            <i class="fa fa-window-close text-danger"></i>
                                        @endif
                                        <label class="form-check-label" for="checklist_{{ $checklist->id }}">
                                            {{ $checklist->nom }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="form-group col-md-4 mt-2 alert">
                            <label class=" cursor-pointer" data-aire-component="label" for="checklists">
                                Checklist
                            </label>
                            @foreach($checklists as $checklist)
                                <div class="custom-control custom-switch form-check mt-2">
                                    <input name="checklists[]" value="{{ $checklist->id }}" type="checkbox" class="custom-control-input" id="checklist_{{ $checklist->id }}" {{ in_array($checklist->id, old('checklists', $inspection?->checklists->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="checklist_{{ $checklist->id }}">{{ $checklist->nom }}</label>
                                </div>
                            @endforeach
                        </div>

                        @if(( $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale))
                            <div class="form-group col-md-4 mt-2 alert alert-light" data-aire-component="group" data-aire-for="observations">
                                <label class=" cursor-pointer" data-aire-component="label" for="__aire-0-observations10">
                                    Observation(s) initiale(s)
                                </label>
                                <div class="mt-2">
                                    {!! $reservation->inspection_initiale->observations !!}
                                </div>
                            </div>
                        @endif

                        {{ Aire::textArea('observations', 'Observation(s)')->rows(5)->groupAddClass('col-md-4 mt-2 alert') }}

                    </div>

                </div>

                <div class="box-footer">
                    <div><a href="{{ route('admin.inspection.client', [$reservation, $type]) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div><button type="submit" id="nextBtn" class="btn btn-primary">Suivant</button></div>
                </div>
            </div>

        </div>

    </div>

    {{ Aire::close() }}

    <script src="{{ asset('ipsum/admin/dist/tinymce.js') }}"></script>


@endsection