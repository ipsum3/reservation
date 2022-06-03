@extends('IpsumAdmin::layouts.app')
@section('title', 'Reservations')

@section('content')

    <h1 class="main-title">Reservation</h1>

    {{ Aire::open()->route($reservation->exists ? 'admin.reservation.update' : 'admin.reservation.store', $reservation->exists ? [$type, $reservation] : $type)->bind($reservation)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreReservation::class) }}
        {{ Aire::hidden('type', $type) }}
        <div class="box">
            <div class="box-header">
                <h2 class="box-title">{{ $reservation->exists ? 'Modification' : 'Ajout' }}</h2>
                <div class="btn-toolbar">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                    <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                    @if ($reservation->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.create', $type) }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    @if ($reservation->is_deletable)
                    <a class="btn btn-outline-danger" href="{{ route('admin.reservation.delete', $reservation) }}" data-toggle="tooltip" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                    @endif
                    @endif
                </div>
            </div>
            <div class="box-body">
                {{ Aire::input('titre', 'Titre*') }}

                @if (config('ipsum.reservation.types.'.$type.'.has_categorie'))
                    <div class="form-group">
                        <label for="categorie_id">Catégorie*</label>
                        <select id="categorie_id" name="categorie_id" class="form-control @error('categorie_id') is-invalid @enderror">
                            <option value="">---- Catégories -----</option>
                            @foreach($categories as $categorie)
                                <option value="{{ $categorie->id }}" {{ ($reservation->exists and $categorie->id == request()->old('categorie_id', $reservation->categorie_id)) ? 'selected' : '' }}>{{ $categorie->nom }}</option>
                                @foreach($categorie->children as $sous_categorie)
                                    <option value="{{ $sous_categorie->id }}" {{ ($reservation->exists and $sous_categorie->id == request()->old('categorie_id', $reservation->categorie_id)) ? 'selected' : '' }}>-- {{ $sous_categorie->nom }}</option>
                                @endforeach
                            @endforeach
                        </select>
                        @error('categorie_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                {{ Aire::textArea('extrait', 'Extrait')->class('tinymce-simple') }}

                {{ Aire::textArea('texte', 'Texte')->class('tinymce')->data('medias', route('admin.media.popin')) }}

                <script src="{{ asset('ipsum/admin/dist/tinymce.js') }}"></script>
            </div>
        </div>
        <div class="box">
            <div class="box-header">
                <h2 class="box-title">
                    Publication
                    @if ($reservation->exists)
                    <small class="text-muted">&nbsp;Créé le <span data-toggle="tooltip"  title="{{ $reservation->created_at }}">{{ $reservation->created_at->format('d/m/Y')  }}</span> et modifié le <span data-toggle="tooltip"  title="{{ $reservation->updated_at }}">{{ $reservation->updated_at->format('d/m/Y')  }}</span></small>
                    @endif
                </h2>
            </div>
            <div class="box-body row">
                <div class="col">
                    {{ Aire::date('date', 'Date') }}
                </div>
                <div class="col">
                    {{ Aire::select(config('ipsum.reservation.etats'), 'etat', 'Etat') }}
                </div>
            </div>
        </div>
        <div class="box">
            <div class="box-header">
                <h2 class="box-title">Médias</h2>
            </div>
            <div class="box-body">
                <div class="upload"
                     data-uploadendpoint="{{ route('admin.media.store') }}"
                     data-uploadmedias="{{ route('admin.media.publication', ['publication_type' => \Ipsum\Reservation\app\Models\Reservation::class, 'publication_id' => $reservation->exists ? $reservation->id : '']) }}"
                     data-uploadrepertoire="reservation"
                     data-uploadpublicationid="{{ $reservation->id }}"
                     data-uploadpublicationtype="{{ \Ipsum\Reservation\app\Models\Reservation::class }}"
                     data-uploadgroupe=""
                     data-uploadnote="Images et documents, poids maximum {{ config('ipsum.media.upload_max_filesize') }} Ko"
                     data-uploadmaxfilesize="{{ config('ipsum.media.upload_max_filesize') }}"
                     data-uploadmmaxnumberoffiles=""
                     data-uploadminnumberoffiles=""
                     data-uploadallowedfiletypes=""
                     data-uploadcsrftoken="{{ csrf_token() }}">
                    <div class="upload-DragDrop"></div>
                    <div class="upload-ProgressBar"></div>
                    <div class="upload-alerts mt-3"></div>
                    <div class="mt-3">
                        <h3>Médias associés :</h3>
                        <div class="d-flex flex-row flex-wrap sortable upload-files" data-sortableurl="{{ route('admin.media.changeOrder') }}" data-sortablecsrftoken="{{ csrf_token() }}">
                        </div>
                    </div>
                </div>
            </div>
            <link href="{{ asset('ipsum/admin/dist/uppy.css') }}" rel="stylesheet">
            <script src="{{ asset('ipsum/admin/dist/uppy.js') }}"></script>
        </div>
        @if(auth()->user()->isSuperAdmin())
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Seo</h2>
                </div>
                <div class="box-body">
                    {{ Aire::input('seo_title', 'Balise title') }}
                    {{ Aire::input('seo_description', 'Balise description') }}
                </div>
            </div>
        @endif
    {{ Aire::close() }}

@endsection
