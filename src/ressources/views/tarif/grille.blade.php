@extends('IpsumAdmin::layouts.app')
@section('title', 'Tarifs')

@section('content')

    <h1 class="main-title">Tarifs</h1>
    {{ Aire::open()->route('admin.tarif.update', $saison) }}

    @foreach($modalites as $modalite)
        <div class="box {{ !$loop->first ? 'mt-20' : '' }}">
            <div class="box-header">
                <h2 class="box-title">Grille de tarifs {{ $modalite->nom }}</h2>
                @if ($loop->first)
                    <div class="btn-toolbar">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>&nbsp;
                        <button class="btn btn-outline-secondary" type="reset" data-toggle="tooltip" title="Annuler les modifications en cours"><i class="fas fa-undo"></i></button>&nbsp;
                    </div>
                @endif
            </div>
            <div class="box-body">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th></th>
                        @foreach ($durees as $duree)
                            <th style="width: 150px;">
                                @if ($duree->max)
                                    {{ $duree->min }} à {{ $duree->max }} jours
                                @else
                                    {{ $duree->min }} jours et plus
                                @endif
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $categorie)
                            <tr class="{{ $loop->even ? " pair" : "impair" }}">
                                <td>{{ $categorie->nom }}</td>
                                @foreach ($durees as $duree)
                                    <td class="center">
                                        <input type="number" name="{{ 'tarifs['.$modalite->id.']['.$categorie->id.']['.$duree->id.']' }}" value="{{ isset($tarifs[$modalite->id][$categorie->id][$duree->id]) ? $tarifs[$modalite->id][$categorie->id][$duree->id] : null }}" step=".01" class="form-control text-right">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
    {{ Aire::close() }}

@endsection
