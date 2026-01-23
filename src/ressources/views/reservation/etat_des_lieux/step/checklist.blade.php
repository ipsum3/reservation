@extends('IpsumAdmin::layouts.app')
@section('title', 'État des lieux')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux.step._progressbar')

    <h1 class="main-title">État des lieux {{ strtolower($type->nom) }} <a href="{{ route('admin.reservation.edit', $reservation) }}"><small class="text-muted">(résa. {{ $reservation->reference }})</small></a></h1>

    {{ Aire::open()->id('reservation')->route('admin.inspection.checklist.store', [$reservation, $type])->bind($inspection)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionChecklist::class) }}

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Checklist</h2>
                </div>
                <div class="box-body">

                    <div class="row">
                        {{ Aire::number('kilometrage', 'Kilométrage (km)*')->required()->value(old('kilometrage', $inspection->kilometrage ?? ($reservation->vehicule->last_inspection->kilometrage ?? '') ))
                            ->helpText(( !$type->is_initial && $reservation->inspection_initiale) ? 'Kilométrage initial : '.$reservation->inspection_initiale?->kilometrage.' km' : '')
                            ->groupAddClass('col-md-12') }}
                        {{ Aire::range('carburant', 'Niveau de carburant* : ')->step('1')->min(0)->max(8)->value(old('carburant', $inspection->carburant ?? ($reservation->vehicule->last_inspection->carburant ?? 8) ))
                            ->list('markers')
                            ->id('carburant')
                            ->helpText((!$type->is_initial && $reservation->inspection_initiale) ? 'Niveau de carburant initial : '.$reservation->inspection_initiale?->carburant.'/8' : '')
                            ->groupAddClass('col-md-12') }}
                        <datalist id="markers">
                            @for($i=0; $i<=8; $i++)
                                <option value="{{ $i }}" label="{{ $i }}"></option>
                            @endfor
                        </datalist>
                        <script>
                            var span = document.createElement("span");
                            span.id = 'carburant_value';
                            document.querySelector("label[for=carburant]").appendChild(span);
                            const value = document.querySelector("#carburant_value");
                            const input = document.querySelector("#carburant");
                            value.textContent = input.value + '/8';
                            input.addEventListener("input", (event) => {
                                value.textContent = event.target.value + '/8';
                            });
                        </script>

                        <div class="form-group col-md-6 mt-2 alert">
                            <label class=" cursor-pointer" data-aire-component="label" for="checklists">
                                Checklist
                            </label>
                            @foreach($checklists as $checklist)
                                <div class="custom-control custom-switch form-check mt-3">
                                    <input name="checklists[]" value="{{ $checklist->id }}" type="checkbox" class="custom-control-input"
                                           id="checklist_{{ $checklist->id }}" {{ in_array($checklist->id, old('checklists', $inspection?->checklists->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="checklist_{{ $checklist->id }}">
                                        {{ $checklist->nom }}
                                        @if(!$type->is_initial && $reservation->inspection_initiale)
                                            <br>
                                            <span class="text-muted">(initial
                                                @if(in_array($checklist->id, $reservation->inspection_initiale->checklists->pluck('id')->toArray()))
                                                    <i class="fa fa-check-square text-success"></i>
                                                @else
                                                    <i class="fa fa-window-close text-danger"></i>
                                                @endif
                                                )
                                            </span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        {{ Aire::textArea('observations', 'Observation(s)')->rows(5)->groupAddClass('col-md-6 mt-2 alert')
                            ->helpText((!$type->is_initial && $reservation->inspection_initiale) ? 'Observation(s) initiale : '.($reservation->inspection_initiale->observations ?: '-') : '') }}

                    </div>

                </div>

                <div class="box-footer">
                    <div><a href="{{ route('admin.inspection.client', [$reservation, $type]) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div>
                        <button type="submit" id="nextBtn" class="btn btn-primary">Suivant</button>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{ Aire::close() }}

    <script src="{{ asset('ipsum/admin/dist/tinymce.js') }}"></script>

@endsection