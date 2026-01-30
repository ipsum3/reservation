@extends('IpsumAdmin::layouts.app')
@section('title', 'État des lieux')

@section('content')

    @include('IpsumReservation::reservation.etat_des_lieux.step._progressbar')

    <h1 class="main-title">État des lieux {{ strtolower($type->nom) }} <a href="{{ route('admin.reservation.edit', $reservation) }}"><small class="text-muted">(résa. {{ $reservation->reference }})</small></a></h1>

    {{ Aire::open()->id('reservation')->route($dommage->exists ? 'admin.inspection.dommage.update' : 'admin.inspection.dommage.store', $dommage->exists ? [$reservation, $type, $dommage] : [$reservation, $type])->bind($dommage)/*->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionDommage::class)*/ }}

    <div class="row">

        <div class="col-md-12">

            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">{{ $dommage->exists ? 'Modifier' : 'Ajouter' }} un dommage</h2>
                </div>
                <div class="box-body">

                    <div class="row justify-content-center">

                        <div class="col-md-8 mt-3 mb-5">

                            <div id="upload-DragDrop"></div>

                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-4">
                            {{ Aire::select(collect(['' => '---- Types -----'])->union($dommage_types->pluck('nom','id')), 'type_id', 'Type de dommage*')->required() }}
                        </div>

                        <div class="col-md-4">
                            {{ Aire::select(collect(['' => '---- Emplacements -----'])->union($dommage_emplacements->pluck('nom','id')), 'emplacement_id', 'Emplacement*')->required() }}
                        </div>

                        <div class="col-md-4">
                            {{ Aire::select(collect(['' => '---- Éléments -----'])->union($dommage_elements->pluck('nom','id')), 'element_id', 'Élément*')->required() }}
                        </div>

                        <div class="col-md-12 mt-3">
                            {{ Aire::textArea('observations', 'Observation(s)')->rows(5) }}
                        </div>


                    </div>
                </div>

                <div class="box-footer">
                    <div><a href="{{ route('admin.inspection.dommages', [$reservation, $type]) }}" id="prevBtn" class="btn btn-outline-secondary">Retour</a></div>
                    <div>
                        <button type="submit" id="nextBtn" class="btn btn-primary">{{ $dommage->exists ? 'Modifier' : 'Ajouter' }}</button>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{ Aire::close() }}

    <script>
        const existingMedias = @json($dommage->images->each(function ($media) {
                $media->url = url(Croppa::url($media->crop_path, 400));
            }));

        const maxNumberOfFiles = 1;
        const publicationId = '{{ $dommage->id ?? '' }}';
        const publicationType = "Ipsum\\Reservation\\app\\Models\\Dommage\\Dommage";
        const groupe = "";
        const repertoire = 'inspection';

    </script>

    @include('IpsumReservation::reservation.etat_des_lieux.step._uppy')


@endsection