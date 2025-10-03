<div class="box">
    <div class="box-header">
        <h2 class="box-title">{{ $heure }}</h2>
    </div>
    <div class="box-body">

        <div class="table-wrapper">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th style="width: 60px"></th>
                    <th>Véhicule</th>
                    <th style="width: 20%">Lieu</th>
                    <th style="width: 20%">Client</th>
                    <th style="width: 180px">Balance</th>
                    <th style="width: 10%">Condition</th>
                    <th style="width: 200px">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($reservations as $reservation)
                    <tr>
                        <td class="text-white {{ $is_depart ? 'bg-success' : 'bg-info' }}">{{ $is_depart ? 'Départ' : 'Retour' }}</td>
                        <td>
                            <a href="{{ $reservation->categorie ? route('admin.categorie.edit', $reservation->categorie) : '#' }}">
                                Catégorie {{ $reservation->categorie_nom }}
                            </a>
                            <br>
                            <a href="{{ $reservation->vehicule ? route('admin.vehicule.edit', $reservation->vehicule) : '#' }}">
                                @if ($reservation->vehicule)
                                    {{ $reservation->vehicule->marque_modele }}<br>
                                    {{ $reservation->vehicule->immatriculation }}
                                @endif
                            </a>
                            @if ($reservation->prestations->count())
                                <i class="fa fa-clipboard-list" data-toggle="tooltip" data-placement="auto" data-html="true" title="Prestations :<br>
                                    @foreach ($reservation->prestations as $prestation)
                                        {{ $prestation->quantite }} {{ strtolower($prestation->nom) }} {{ !empty($prestation->choix) ? '('.$prestation->choix.')' : '' }} <br>
                                    @endforeach
                                "></i>
                            @endif
                        </td>
                        <td>
                            {{ $is_depart ? $reservation->debut_lieu_nom : $reservation->fin_lieu_nom }}
                            @if ($reservation->custom_fields->vol)
                                <i class="fa fa-plane-arrival" data-toggle="tooltip" data-placement="auto" data-html="true" title="Numéro de vol : {{ $reservation->custom_fields->vol }}"></i>
                            @endif
                        </td>
                        <td>
                            @if ($reservation->client)
                                <a href="{{ route('admin.client.edit', $reservation->client) }}">{{ $reservation->prenom }} {{ $reservation->nom }}</a>
                            @else
                                {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}
                            @endif
                            <br><a href="mailto:{{ $reservation->email }}"><i class="fa fa-envelope"></i> {{ $reservation->email }}</a>
                            @if ($reservation->telephone)
                                <br><a href="tel:{{ $reservation->telephone }}"><i class="fa fa-phone-square"></i> {{ $reservation->telephone }}</a>
                            @endif
                        </td>
                        <td>
                            <x-reservation::reste_a_payer total="{{ $reservation->total }}"  montant_paye="{{ $reservation->montant_paye }}" />
                        </td>
                        <td>{{ $reservation->condition ? $reservation->condition->nom : '' }}</td>
                        <td class="text-right">
                            <form action="{{ route('admin.reservation.destroy', $reservation) }}" method="POST">
                                @if ($is_depart)
                                    @if(config('ipsum.reservation.etat_des_lieux.enable') === true)
                                        <a class="btn btn-outline-primary" href="{{ route('admin.inspection.vehicule', [$reservation, \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ]) }}" title="Etat des lieux initial"><i class="fa fa-car"></i></a>
                                    @endif
                                    <a class="btn btn-outline-primary" href="{{ route('admin.reservation.contrat', [$reservation]) }}" title="contrat"><i class="fa fa-file-signature"></i></a>
                                @elseif(config('ipsum.reservation.etat_des_lieux.enable') === true)
                                    <a class="btn btn-outline-primary" href="{{ route('admin.inspection.checklist', [$reservation, \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID ]) }}" title="Etat des lieux final"><i class="fa fa-car"></i></a>
                                @endif
                                <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.edit', [$reservation]) }}"><i class="fa fa-edit"></i></a>
                                @can('delete', $reservation)
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                @endcan
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>