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
                        <li class="active">Kilométrage / Carburant / Checklist</li>
                        <li>Dommages</li>
                        <li>Photos</li>
                        <li>Récapitulatif</li>
                        <li>Signature client</li>
                        <li>Signature agent</li>
                    </ul>
                </div>
                <div class="box-body">

                    {{ Aire::open()->id('reservation')->route('admin.inspection.checklist.store', [$reservation, $type])->bind($inspection)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionChecklist::class) }}

                    <!-- STEP 4 -->
                    <div class="step active">
                        <div class="box-body">
                            <div class="row">
                                {{ Aire::number('kilometrage', 'Kilométrage (km)*')->required()->helpText(( $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale) ? 'Kilométrage initial : '.$reservation->inspection_initiale?->kilometrage.' km' : '')->groupAddClass('col-md-12') }}
                                {{ Aire::range('carburant', 'Niveau de carburant*')->step('1')->min(0)->max(8)->list('markers')->id('carburant')->helpText(( $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale) ? 'Niveau de carburant initial : '.$reservation->inspection_initiale?->carburant.'/8' : '')->groupAddClass('col-md-11') }}
                                <p class="d-flex"><output id="carburant_value"></output></p>
                                <datalist id="markers">
                                    @for($i=0; $i<=8; $i++)
                                        <option value="{{ $i }}" label="{{ $i }}"></option>
                                    @endfor
                                </datalist>
                                <script>
                                    const value = document.querySelector("#carburant_value");
                                    const input = document.querySelector("#carburant");
                                    value.textContent = input.value+'/8';
                                    input.addEventListener("input", (event) => {
                                        value.textContent = event.target.value+'/8';
                                    });
                                </script>

                                @if(( $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale))
                                    <div class="form-group col-md-2 mt-4 alert alert-light">
                                        <label class=" cursor-pointer" data-aire-component="label" for="checklists">
                                            Checklist initiale
                                        </label>
                                        @foreach($checklists as $checklist)
                                            <div class="form-check">
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

                                <div class="form-group col-md-2 mt-4 alert">
                                    <label class=" cursor-pointer" data-aire-component="label" for="checklists">
                                        Checklist
                                    </label>
                                    @foreach($checklists as $checklist)
                                        <div class="form-check">
                                            <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    name="checklists[]"
                                                    value="{{ $checklist->id }}"
                                                    id="checklist_{{ $checklist->id }}"
                                                    {{ in_array($checklist->id, old('checklists', $inspection?->checklists->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="checklist_{{ $checklist->id }}">
                                                {{ $checklist->nom }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                @if(( $type->id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale))
                                    <div class="form-group col-md-4 mt-4 alert alert-light" data-aire-component="group" data-aire-for="observations">
                                        <label class=" cursor-pointer" data-aire-component="label" for="__aire-0-observations10">
                                            Observation(s) initiale(s)
                                        </label>
                                        <div class="mt-2">
                                            {!! $reservation->inspection_initiale->observations !!}
                                        </div>
                                    </div>
                                @endif

                                {{ Aire::textArea('observations', 'Observation(s)')->groupAddClass('col-md-4 mt-4 alert')->class('tinymce-simple') }}

                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.inspection.client', [$reservation, $type]) }}" id="prevBtn" class="btn btn-secondary">Retour</a>
                        <button type="submit" id="nextBtn" class="btn btn-primary">Suivant</button>
                    </div>

                    {{ Aire::close() }}
                </div>
            </div>

        </div>

    </div>

    <script src="{{ asset('ipsum/admin/dist/tinymce.js') }}"></script>


@endsection