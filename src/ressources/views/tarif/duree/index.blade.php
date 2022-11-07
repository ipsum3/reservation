@extends('IpsumAdmin::layouts.app')
@section('title', 'Tranche de durée')

@section('content')

    <h1 class="main-title">Configuration de la grille des tarifs</h1>


    <div class="form-row">
        <div class="box col-md-6">
            <div class="box-header">
                <h2 class="box-title">Liste des tranches de durée pour une dégressivitée des tarifs</h2>
            </div>
            <div class="box-body">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>Mini</th>
                        <th>Maxi</th>
                        <th width="240px">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($durees as $duree)
                        <tr>
                            <td>{{ $duree->min }} jour{{ $duree->min > 1 ? 's' : '' }}</td>
                            <td>
                                {{ $duree->max ? $duree->max.' jours' : '-' }}
                            </td>
                            <td class="text-right">
                                <form action="{{ route('admin.duree.destroy', $duree) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <a class="btn btn-primary" href="{{ route('admin.duree.edit', [$duree]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash-alt"></i> Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ Aire::open()->route('admin.duree.store')->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreDuree::class)->addClass('col-md-6') }}
            <div class="box">
                <div class="box-header">
                    <h2 class="box-title">Ajouter une tranche de durée</h2>
                </div>
                <div class="box-body">
                    <div class="form-row">
                        {{ Aire::input('min', 'Durée minimum (jour)*')->required()->groupAddClass('col-md-6') }}
                        {{ Aire::input('max', 'Durée maximum (jour)')->groupAddClass('col-md-6') }}
                    </div>
                </div>
                <div class="box-footer">
                    <div><button class="btn btn-outline-secondary" type="reset">Annuler</button></div>
                    <div><button class="btn btn-primary" type="submit">Ajouter</button></div>
                </div>
            </div>
        {{ Aire::close() }}
    </div>
    @if ($weekend)
        <div class="form-row">
            {{ Aire::open()->route('admin.duree.update', $weekend)->bind($weekend)->formRequest(\Ipsum\Reservation\app\Http\Requests\StoreDuree::class)->addClass('col-md-6') }}
                <div class="box">
                    <div class="box-header">
                        <h2 class="box-title">Tarif spécial : forfait weekend</h2>
                    </div>
                    <div class="box-body">
                        <div class="form-row">
                            {{ Aire::input('min', 'Durée minimum (jour)*')->required()->groupAddClass('col-md-6') }}
                            {{ Aire::input('max', 'Durée maximum (jour)*')->required()->groupAddClass('col-md-6') }}
                        </div>
                        <div class="form-row">
                            {{ Aire::time('min_heure', 'Heure minimum du départ*')->required()->groupAddClass('col-md-6')->helpText('Le '.\Ipsum\Reservation\app\Models\Tarif\Duree::JOURS[$weekend->min_jour]) }}
                            {{ Aire::time('max_heure', 'Heure maximum du retour*')->required()->groupAddClass('col-md-6')->helpText('Le '.\Ipsum\Reservation\app\Models\Tarif\Duree::JOURS[$weekend->max_jour]) }}
                            {{ Aire::hidden('min_jour', $weekend->min_jour) }}
                            {{ Aire::hidden('max_jour', $weekend->max_jour) }}
                        </div>
                        {{--<div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="min_jour">Jour minimum du départ*</label>
                                <select name="min_jour" class="form-control" id="min_jour" required>
                                    <option value="">----- Jours -----</option>
                                    @foreach(\Ipsum\Reservation\app\Models\Tarif\Duree::JOURS as $key => $jour)
                                        <option value="{{ $key }}" {{ old('min_jour', (string) $weekend->min_jour) === (string) $key ? 'selected' : '' }} >{{ $jour }}</option>
                                    @endforeach
                                </select>
                                @error('min_jour')
                                <div class="invalid-feedback ">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="max_jour">Jour maximum du retour*</label>
                                <select name="max_jour" class="form-control" id="max_jour" required>
                                    <option value="">----- Jours -----</option>
                                    @foreach(\Ipsum\Reservation\app\Models\Tarif\Duree::JOURS as $key => $jour)
                                        <option value="{{ $key }}" {{ old('min_jour', (string) $weekend->max_jour) === (string) $key ? 'selected' : '' }} >{{ $jour }}</option>
                                    @endforeach
                                </select>
                                @error('max_jour')
                                <div class="invalid-feedback ">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>--}}
                    </div>
                    <div class="box-footer">
                        <div><button class="btn btn-outline-secondary" type="reset">Annuler</button></div>
                        <div><button class="btn btn-primary" type="submit">Enregistrer</button></div>
                    </div>
                </div>
            {{ Aire::close() }}
        </div>
    @endif


@endsection
