@extends('IpsumAdmin::layouts.app')
@section('title', 'Tranche de durée')

@section('content')

    <h1 class="main-title">Configuration de la grille des tarifs</h1>


    <div class="row">
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
    <div class="row">
        <div class="box col-md-6">
            <div class="box-header">
                <h2 class="box-title">Liste des tarifs spéciaux</h2>
                <div class="btn-toolbar">
                    <a class="btn btn-outline-secondary" href="{{ route('admin.duree.create') }}" data-toggle="tooltip" title="Ajouter">
                        <i class="fas fa-plus"></i>
                    </a>&nbsp;
                </div>
            </div>
            <div class="box-body">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Mini</th>
                        <th>Maxi</th>
                        <th width="240px">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($tarifs_speciaux as $duree)
                        <tr>
                            <td>{{ $duree->nom }}</td>
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
    </div>


@endsection
