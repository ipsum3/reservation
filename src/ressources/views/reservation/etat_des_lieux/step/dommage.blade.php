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
                        <li class="active">Dommages</li>
                        <li>Photos</li>
                        <li>Récapitulatif</li>
                        <li>Signature client</li>
                        <li>Signature agent</li>
                    </ul>
                </div>
                <div class="box-body">

                    {{ Aire::open()->id('reservation')->route('admin.inspection.dommage.store', [$reservation, $type])->bind($inspection)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreInspectionDommage::class) }}

                    <!-- STEP 5 -->
                    <div class="step active">

                        @if($inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID && $reservation->inspection_initiale)

                            <h2 class="text-xl font-semibold mb-2">Dommage(s) initiale(s)</h2>

                            <table class="table table-hover table-striped"  style="min-width: 1000px">
                                <thead>
                                <tr>
                                    <th> Type </th>
                                    <th> Emplacement </th>
                                    <th> Elément </th>
                                    <th> Observations </th>
                                    <th style="width: 200px">Image</th>
                                    <th style="width: 100px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($reservation->inspection_initiale?->dommages)
                                    @foreach($reservation->inspection_initiale?->dommages as $dommage)
                                        <tr>
                                            <td>
                                                {{ $dommage->type?->nom }}
                                            </td>
                                            <td>
                                                {{ $dommage->emplacement?->nom }}
                                            </td>
                                            <td>
                                                {{ $dommage->element?->nom }}
                                            </td>
                                            <td>
                                                {!! $dommage->observations !!}
                                            </td>
                                            <td>
                                                @if($dommage->inspection->medias()->groupe($dommage->id)->count())
                                                    @php $media = $dommage->inspection->medias()->groupe($dommage->id)->first(); @endphp
                                                    <div class="media sortable-item" data-sortable="{{ $media->id }}">
                                                        <div class="media-img">
                                                            @if ($media->isImage)
                                                                <img src="{{ Croppa::url($media->cropPath, 200, 200) }}" alt="{{ $media->tagAlt }}" />
                                                            @else
                                                                <span class="media-icone {{ $media->icone }}"></span>
                                                            @endif
                                                        </div>
                                                        <div class="media-title">
                                                            {{ $media->titre }}
                                                        </div>
                                                        <div class="media-toolbar">
                                                            <ul>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>

                            <h2 class="text-xl font-semibold mb-2 mt-4">Dommage(s)</h2>
                        @endif


                        <button class="btn btn-outline-secondary table-editable-add" data-target="dommages" id="dommages-add" type="button" data-toggle="tooltip" title="Ajouter">
                            <i class="fas fa-plus"></i> Ajouter un dommage
                        </button>&nbsp;
                        <div class="box-body overflow-auto">
                            <input type="hidden" name="dommages">

                            <table class="table table-hover table-striped"  style="min-width: 1000px">
                                <thead>
                                <tr>
                                    <th> Type </th>
                                    <th> Emplacement </th>
                                    <th> Elément </th>
                                    <th> Observations </th>
                                    <th style="width: 200px">Image</th>
                                    <th style="width: 100px"></th>
                                </tr>
                                </thead>
                                <tbody id="dommages-lignes">
                                @if($reservation->vehicule?->dommages && $inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
                                    @foreach($reservation->vehicule?->dommages as $dommage)
                                        @if($dommage->inspection->id != $inspection->id && $dommage->inspection->id != $reservation->inspection_initiale->id)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="ids[]" value="{{ $dommage->id }}" />
                                                    {{ $dommage->type?->nom }}
                                                </td>
                                                <td>
                                                    {{ $dommage->emplacement?->nom }}
                                                </td>
                                                <td>
                                                    {{ $dommage->element?->nom }}
                                                </td>
                                                <td>
                                                    {!! $dommage->observations !!}
                                                </td>
                                                <td>
                                                    @if($dommage->inspection->medias()->groupe($dommage->id)->count())
                                                        @php $media = $dommage->inspection->medias()->groupe($dommage->id)->first(); @endphp
                                                        <div class="media sortable-item" data-sortable="{{ $media->id }}">
                                                            <div class="media-img">
                                                                @if ($media->isImage)
                                                                    <img src="{{ Croppa::url($media->cropPath, 200, 200) }}" alt="{{ $media->tagAlt }}" />
                                                                @else
                                                                    <span class="media-icone {{ $media->icone }}"></span>
                                                                @endif
                                                            </div>
                                                            <div class="media-title">
                                                                {{ $media->titre }}
                                                            </div>
                                                            <div class="media-toolbar">
                                                                <ul>
                                                                    <li><a href="{{ route('admin.media.edit', $media->id) }}" data-toggle="tooltip" title="Editer"><span class="fa fa-edit"></span></a></li>
                                                                    <li><a href="{{ route('admin.media.getDestroy', $media->id) }}" data-toggle="tooltip" title="Supprimer"><span class="fa fa-trash-alt"></span></a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="dommages-delete btn btn-outline-danger" data-confirm="false"><i class="fa fa-trash-alt"></i></button>
                                                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i></button>&nbsp;
                                                </td>
                                            </tr>

                                        @endif
                                    @endforeach
                                @endif
                                @if($inspection->dommages->count())
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach($inspection->dommages as $dommage)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="dommages[{{ $i }}][id]" value="{{ $dommage->id }}" />
                                                <select class="form-control" name="dommages[{{ $i }}][type_id]" required>
                                                    <option value="">-- Types --</option>
                                                    @foreach($dommage_types as $type)
                                                        <option value="{{ $type->id }}" {{ $dommage->type?->id == $type->id  ? 'selected' : '' }}>{{ $type->nom }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control" name="dommages[{{ $i }}][emplacement_id]" required>
                                                    <option value="">-- Emplacement --</option>
                                                    @foreach($dommage_emplacements as $emplacement)
                                                        <option value="{{ $emplacement->id }}" {{ $dommage->emplacement?->id == $emplacement->id  ? 'selected' : '' }}>{{ $emplacement->nom }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control" name="dommages[{{ $i }}][element_id]" required>
                                                    <option value="">-- Elément --</option>
                                                    @foreach($dommage_elements as $element)
                                                        <option value="{{ $element->id }}" {{ $dommage->element?->id == $element->id  ? 'selected' : '' }}>{{ $element->nom }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <textarea class="form-control" rows="5" name="dommages[{{ $i }}][observations]">{{ $dommage->observations }}</textarea>
                                            </td>
                                            <td>
                                                @if($inspection->medias()->groupe($dommage->id)->count())
                                                    @php $media = $inspection->medias()->groupe($dommage->id)->first(); @endphp
                                                    <div class="media sortable-item" data-sortable="{{ $media->id }}">
                                                        <div class="media-img">
                                                            @if ($media->isImage)
                                                                <img src="{{ Croppa::url($media->cropPath, 200, 200) }}" alt="{{ $media->tagAlt }}" />
                                                            @else
                                                                <span class="media-icone {{ $media->icone }}"></span>
                                                            @endif
                                                        </div>
                                                        <div class="media-title">
                                                            {{ $media->titre }}
                                                        </div>
                                                        <div class="media-toolbar">
                                                            <ul>
                                                                <li><a href="{{ route('admin.media.edit', $media->id) }}" data-toggle="tooltip" title="Editer"><span class="fa fa-edit"></span></a></li>
                                                                <li><a href="{{ route('admin.media.getDestroy', $media->id) }}" data-toggle="tooltip" title="Supprimer"><span class="fa fa-trash-alt"></span></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="upload"
                                                         data-initialize="true"
                                                         data-uploadendpoint="{{ route('admin.media.store') }}"
                                                         data-uploadmedias="{{ route('admin.media.publication', ['publication_type' => \Ipsum\Reservation\app\Models\Inspection\Inspection::class, 'publication_id' => $inspection->exists ? $inspection->id : '', 'groupe' => $dommage->id ]) }}"
                                                         data-uploadrepertoire=""
                                                         data-uploadpublicationid="{{ $inspection->id ?? '' }}"
                                                         data-uploadpublicationtype="{{ \Ipsum\Reservation\app\Models\Inspection\Inspection::class }}"
                                                         data-uploadgroupe="{{ $dommage->id }}"
                                                         data-uploadnote="Une seule image (max {{ config('ipsum.media.upload_max_filesize') }} Ko)"
                                                         data-uploadmaxfilesize="{{ config('ipsum.media.upload_max_filesize') }}"
                                                         data-uploadmmaxnumberoffiles="1"
                                                         data-uploadminnumberoffiles="0"
                                                         data-uploadallowedfiletypes="image/*"
                                                         data-uploadcsrftoken="{{ csrf_token() }}">
                                                        <div class="upload-DragDrop"></div>
                                                        <div class="upload-ProgressBar"></div>
                                                        <div class="upload-alerts mt-3"></div>
                                                        <div class="mt-2 d-flex flex-row flex-wrap sortable upload-files"
                                                             data-sortableurl="{{ route('admin.media.changeOrder') }}"
                                                             data-sortablecsrftoken="{{ csrf_token() }}">
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <input type="hidden" name="ids[]" value="{{ $dommage->id }}" />
                                                <button type="button" class="dommages-delete btn btn-outline-danger" data-confirm="false"><i class="fa fa-trash-alt"></i></button>
                                                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i></button>&nbsp;
                                            </td>
                                        </tr>

                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                @endif

                                <script id="dommages-add-template" type="x-tmpl-mustache">
                                    <tr>
                                        <td>
                                            <input type="hidden" name="dommages[@{{ indice }}][uuid]" value="uuid-@{{ indice }}" />
                                            <select class="form-control" name="dommages[@{{ indice }}][type_id]" required>
                                                <option value="">-- Types --</option>
                                            @foreach($dommage_types as $dommage_type)
                                                <option value="{{ $dommage_type->id }}" {{--{{ $paiement->type?->id == $type->id  ? 'selected' : '' }}--}}>{{ $dommage_type->nom }}</option>
                                            @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="dommages[@{{ indice }}][emplacement_id]" required>
                                                <option value="">-- Emplacement --</option>
                                            @foreach($dommage_emplacements as $emplacement)
                                                <option value="{{ $emplacement->id }}" {{--{{ $paiement->type?->id == $type->id  ? 'selected' : '' }}--}}>{{ $emplacement->nom }}</option>
                                            @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="dommages[@{{ indice }}][element_id]" required>
                                                <option value="">-- Elément --</option>
                                            @foreach($dommage_elements as $element)
                                                <option value="{{ $element->id }}" {{--{{ $paiement->type?->id == $type->id  ? 'selected' : '' }}--}}>{{ $element->nom }}</option>
                                            @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <textarea class="form-control" rows="3" name="dommages[@{{ indice }}][observations]"></textarea>
                                        </td>
                                        <td>
                                            <div class="upload"
                                                 data-initialize="false"
                                                 data-uploadendpoint="{{ route('admin.media.store') }}"
                                                 data-uploadmedias="{{ route('admin.media.publication', [ 'publication_type' => \Ipsum\Reservation\app\Models\Inspection\Inspection::class, 'publication_id' => $inspection->id ?? '' ]) }}&groupe=uuid-@{{ indice }}"
                                                 data-uploadpublicationid="{{ $inspection->id ?? '' }}"
                                                 data-uploadpublicationtype="{{ \Ipsum\Reservation\app\Models\Inspection\Inspection::class }}"
                                                 data-uploadgroupe="uuid-@{{ indice }}"
                                                 data-uploadnote="Une seule image (max {{ config('ipsum.media.upload_max_filesize') }} Ko)"
                                                 data-uploadmaxfilesize="{{ config('ipsum.media.upload_max_filesize') }}"
                                                 data-uploadmmaxnumberoffiles="1"
                                                 data-uploadallowedfiletypes="image/*"
                                                 data-uploadcsrftoken="{{ csrf_token() }}">
                                                <div class="upload-DragDrop"></div>
                                                <div class="upload-ProgressBar"></div>
                                                <div class="upload-alerts mt-3"></div>
                                                <div class="mt-2 d-flex flex-row flex-wrap sortable upload-files"
                                                     data-sortableurl="{{ route('admin.media.changeOrder') }}"
                                                     data-sortablecsrftoken="{{ csrf_token() }}">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="dommages-delete btn btn-outline-danger" data-confirm="false"><i class="fa fa-trash-alt"></i></button>
                                            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i></button>
                                        </td>
                                    </tr>
                                </script>
                                </tbody>
                            </table>

                        </div>
                    </div>

                            <!-- Navigation -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('admin.inspection.checklist', [$reservation, $type]) }}" id="prevBtn" class="btn btn-secondary">Retour</a>
                                <button type="submit" id="nextBtn" class="btn btn-primary">Suivant</button>
                            </div>

                        {{ Aire::close() }}
                </div>
            </div>

        </div>

    </div>


    <link href="{{ asset('ipsum/admin/dist/uppy.css') }}" rel="stylesheet">
    <script src="{{ asset('ipsum/admin/dist/uppy.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function initUploads() {
                document.querySelectorAll('.upload').forEach(function (el) {
                    console.log(el, el.dataset.initialize)
                    if (el.dataset.initialize === "false") {
                        window.uppyInit(el);
                        el.dataset.initialize = "true";
                    }
                });
            }

            // Init au chargement
            //initUploads();

            document.getElementById('dommages-add').addEventListener('click', function () {
                setTimeout(initUploads, 100);
            });
        });
    </script>

@endsection