@extends('IpsumAdmin::layouts.app')
@section('title', 'Source')

@section('content')

    <h1 class="main-title">Source</h1>

    {{ Aire::open()->route($source->exists ? 'admin.source.update' : 'admin.source.store', $source->exists ? [$source, request()->route('locale')] : '')->bind($source)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreSource::class) }}
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">{{ $source->exists ? 'Modification' : 'Ajout' }}</h2>
            <div class="btn-toolbar">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                @if ($source->exists)
                    <a class="btn btn-outline-secondary" href="{{ route('admin.source.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                    @if( $source->id != $source::SOURCE_SITE_INTERNET and $source->id != $source::SOURCE_AGENCE )
                        <a class="btn btn-outline-danger" href="{{ route('admin.source.destroy', $source) }}" data-toggle="tooltip" title="Supprimer">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    @endif
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="form-row">
                {{ Aire::input('nom', 'Nom*')->groupAddClass('col-md-6') }}
                {{ Aire::select(collect(['' => '---- Types -----'])->union($types), 'type_id', 'Type*')->groupAddClass('col-md-6') }}
            </div>
        </div>
    </div>
    {{ Aire::close() }}

    <script src="{{ asset('ipsum/admin/dist/tinymce.js') }}"></script>
@endsection
