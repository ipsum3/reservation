@extends('IpsumAdmin::layouts.app')
@section('title', 'Inspections')

@section('content')

    <h1 class="main-title">États des lieux</h1>
    <div class="box">
        <div class="box-header">
            <h2 class="box-title">Liste ({{ $inspections->total() }})</h2>
            <div class="btn-toolbar">
            </div>
        </div>
        <div class="box-body">

            {{ Aire::open()->class('form-inline mt-4 mb-1')->route('admin.inspection.index') }}
            <label class="sr-only" for="search">Recherche</label>
            {{ Aire::input('search')->id('search')->class('form-control mb-2 mr-sm-2')->value(request()->get('search'))->placeholder('Recherche')->withoutGroup() }}
            <label class="sr-only" for="date_debut">Date de début</label>
            {{ Aire::input('date_debut')->value(request()->get('date_debut'))->id('date_debut')->placeholder('Date de début')->style('width: 200px')->class('form-control mb-2 mr-sm-2 datepicker-range')->withoutGroup() }}
            <label class="sr-only" for="date_fin">Date de fin</label>
            {{ Aire::input('date_fin')->value(request()->get('date_fin'))->id('date_fin')->placeholder('Date de fin')->style('width: 200px')->class('form-control mb-2 mr-sm-2 datepicker-range')->withoutGroup() }}
            <button type="submit" class="btn btn-outline-secondary mb-2">Rechercher</button>

            {{ Aire::close() }}
            <div class="table-wrapper">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => '#', 'champ' => 'id'])</th>
                        <th>Réservation</th>
                        <th>Type</th>
                        <th>Locataire</th>
                        <th>Véhicule</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Du', 'champ' => 'debut_at'])</th>
                        <th>@include('IpsumAdmin::partials.tri', ['label' => 'Au', 'champ' => 'fin_at'])</th>
                        <th>Agent</th>
                        <th>Dommages</th>
                        <th>Statut</th>
                        <th width="400px" class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($inspections as $inspection)
                        <tr class="">
                            <td>{{ $inspection->id }}</td>
                            <td><a href="{{ route('admin.reservation.edit', [$inspection->reservation]) }}">{{ $inspection->reservation->reference }}</a></td>
                            <td><span class="badge badge-{{ $inspection->type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'primary' : 'info' }}">{{ $inspection->type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? 'Départ' : 'Retour' }}</span></td>
                            <td>{{ $inspection->reservation->nom }} {{ $inspection->reservation->prenom }}</td>
                            <td><a href="{{ route('admin.vehicule.edit', [$inspection->reservation->vehicule]) }}">{{ $inspection->reservation->immatriculation }} ({{ $inspection->reservation->vehicule->marque_modele }})</a></td>
                            <td>{{ $inspection->reservation->debut_at?->format('d/m/Y') }}</td>
                            <td>{{ $inspection->reservation->fin_at?->format('d/m/Y') }}</td>
                            <td>{{ $inspection->admin->email }} ({{ $inspection->admin->name }} {{ $inspection->admin->firstname }})</td>
                            <td>{{ $inspection->dommages ? count($inspection->dommages). ' dommage'.(count($inspection->dommages) > 1 ? 's' : '').' ajouté'.(count($inspection->dommages) > 1 ? 's' : '') : '' }}</td>
                            <td><span class="badge badge-{{ $inspection->isSigned()  ? 'success' : 'warning' }}">{{ $inspection->isSigned() ? 'Document signé' : 'En attente' }}</span></td>
                            <td class="text-right">
                                <form action="{{ route('admin.inspection.destroy', $inspection) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    @if (!$inspection->isSigned())
                                        <a class="btn btn-primary" href="{{ $inspection->type->id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ? route('admin.inspection.vehicule', [$inspection->reservation, $inspection->type]) : route('admin.inspection.checklist', [$inspection->reservation, $inspection->type]) }}"><i class="fa fa-edit"></i> Modifier</a>
                                        <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                    @else
                                        <a class="btn btn-primary" href="{{ route('admin.inspection.pdf', [$inspection]) }}" target="_blank"><i class="fa fa-file-pdf"></i> Voir le document</a>
                                        <a class="btn btn-primary" href="{{ route('admin.inspection.show', [$inspection->reservation, $inspection->type]) }}"><i class="fa fa-eye"></i> Voir le récapitulatif</a>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {!! $inspections->appends(request()->all())->links() !!}

        </div>
    </div>

@endsection