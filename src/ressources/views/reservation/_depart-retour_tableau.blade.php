<div class="box">
    <div class="box-header">
        <h2 class="box-title text-primary"><i class="fa fa-clock mr-2"></i> {{ $heure }}:00</h2>
    </div>
    <div class="box-body">

        {{-- VERSION PC --}}
        <div class="table-wrapper d-none d-md-block">
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
                @foreach ($reservations->sortBy(function ($reservation) { return $reservation->is_depart ? $reservation->debut_at : $reservation->fin_at; }) as $reservation)
                    <tr style="{{ (
                            (!config('ipsum.reservation.etat_des_lieux.enable') and (($reservation->is_depart and $reservation->debut_at->lt(\Carbon\Carbon::now())) or (!$reservation->is_depart and $reservation->fin_at->lt(\Carbon\Carbon::now()))))
                            or (config('ipsum.reservation.etat_des_lieux.enable') and (($reservation->is_depart and $reservation->inspectionInitiale?->isSigned()) or (!$reservation->is_depart and $reservation->inspectionFinale?->isSigned())))
                            ) ? 'opacity: 0.5' : '' }}">
                        <td class="text-white {{ $reservation->is_depart ? 'bg-success' : 'bg-info' }}">
                            {{ $reservation->is_depart ? 'Départ' : 'Retour' }}<br>
                            {{ $reservation->reference }}
                            <div class="lead mt-1">{{ $reservation->is_depart ? $reservation->debut_at->format('H:i') : $reservation->fin_at->format('H:i') }}</div>
                        </td>
                        <td>
                            <a href="{{ $reservation->categorie ? route('admin.categorie.edit', $reservation->categorie) : '#' }}">
                                Catégorie {{ $reservation->categorie_nom }}
                            </a>
                            <br>
                            @if ($reservation->vehicule)
                                <a href="{{ $reservation->vehicule ? route('admin.vehicule.edit', $reservation->vehicule) : '#' }}">
                                    {{ $reservation->vehicule->marque_modele }}<br>
                                    {{ $reservation->vehicule->immatriculation }}
                                </a>
                            @endif
                            @if ($reservation->prestations->count())
                                @if (!config('ipsum.reservation.depart_retour_affichage_complet'))
                                    <i class="fa fa-clipboard-list" data-toggle="tooltip" data-placement="auto" data-html="true" title="Prestations :<br>
                                        @foreach ($reservation->prestations as $prestation)
                                            {{ $prestation->quantite }} {{ strtolower($prestation->nom) }} {{ !empty($prestation->choix) ? '('.$prestation->choix.')' : '' }} <br>
                                        @endforeach
                                    "></i>
                                @else
                                    <br>Prestations :<br>
                                    @foreach ($reservation->prestations as $prestation)
                                        {{ $prestation->quantite }} {{ strtolower($prestation->nom) }} {{ !empty($prestation->choix) ? '('.$prestation->choix.')' : '' }} <br>
                                    @endforeach
                                @endif
                            @endif
                        </td>
                        <td>
                            {{ $reservation->is_depart ? $reservation->debut_lieu_nom : $reservation->fin_lieu_nom }}
                            @if ($reservation->custom_fields->vol)
                                @if (!config('ipsum.reservation.depart_retour_affichage_complet'))
                                    <i class="fa fa-plane-arrival" data-toggle="tooltip" data-placement="auto" data-html="true" title="Numéro de vol : {{ $reservation->custom_fields->vol }}"></i>
                                @else
                                    <br>Numéro de vol : {{ $reservation->custom_fields->vol }}
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($reservation->client)
                                <a href="{{ route('admin.client.edit', $reservation->client) }}"><i class="fa fa-user"></i> {{ $reservation->prenom }} {{ $reservation->nom }}</a>
                            @else
                                <i class="fa fa-user"></i> {{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}
                            @endif
                            <br><a href="mailto:{{ $reservation->email }}"><i class="fa fa-envelope"></i> {{ $reservation->email }}</a>
                            @if ($reservation->telephone)
                                <br><a href="tel:{{ $reservation->telephone }}"><i class="fa fa-phone-square"></i> {{ $reservation->telephone }}</a>
                            @endif
                        </td>
                        <td>
                            <x-reservation::reste_a_payer total="{{ $reservation->total }}" montant_paye="{{ $reservation->montant_paye }}" />

                            @if(config('ipsum.reservation.caution_provider') && $reservation->caution)
                                <div class="mt-2">
                                    @if($reservation->paiementCaution)
                                        <span class="badge badge-success" data-toggle="tooltip" title="Caution sécurisée">
                                            <i class="fas fa-shield-alt mr-1"></i>Caution : @prix($reservation->paiementCaution->montant) €
                                        </span>
                                    @elseif($reservation->caution_send_at)
                                        <span class="badge badge-warning text-dark" data-toggle="tooltip" title="Demande envoyée le {{ $reservation->caution_send_at?->format('d/m H:i') ?? 'N/C' }}">
                                            <i class="fas fa-hourglass-half mr-1"></i>Caution envoyée
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        <td>{{ $reservation->condition ? $reservation->condition->nom : '' }}</td>

                        <td class="text-right">
                            <form action="{{ route('admin.reservation.destroy', $reservation) }}" method="POST">

                                @if ($reservation->is_depart)
                                    @if(config('ipsum.reservation.etat_des_lieux.enable') === true)
                                        @if($reservation->inspectionInitiale?->isSigned())
                                            <a class="btn btn-outline-primary" href="{{ route('admin.inspection.show', [$reservation->inspectionInitiale]) }}" data-toggle="tooltip" title="Voir l'état des lieux"><i class="fa fa-car-crash"></i></a>
                                        @else
                                            <a class="btn btn-outline-primary" href="{{ route('admin.inspection.vehicule', [$reservation, \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ]) }}" data-toggle="tooltip" title="État des lieux initial"><i class="fa fa-car"></i></a>
                                        @endif
                                    @else
                                        <a class="btn btn-outline-primary" href="{{ route('admin.reservation.contrat', [$reservation]) }}" data-toggle="tooltip" title="Contrat"><i class="fa fa-file-signature"></i></a>
                                    @endif
                                @elseif(config('ipsum.reservation.etat_des_lieux.enable') === true)
                                    @if($reservation->inspectionFinale?->isSigned())
                                        <a class="btn btn-outline-primary" href="{{ route('admin.inspection.show', [$reservation->inspectionFinale]) }}" target="_blank" data-toggle="tooltip" title="Voir l'état des lieux"><i class="fa fa-car-crash"></i></a>
                                    @else
                                        <a class="btn btn-outline-primary" href="{{ route('admin.inspection.checklist', [$reservation, \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID ]) }}" data-toggle="tooltip" title="État des lieux final"><i class="fa fa-car"></i></a>
                                    @endif
                                @endif

                                <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.edit', [$reservation]) }}" data-toggle="tooltip" title="Éditer la réservation"><i class="fa fa-edit"></i></a>

                                @can('delete', $reservation)
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" data-toggle="tooltip" title="Supprimer"><i class="fa fa-trash-alt"></i></button>
                                @endcan
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- VERSION MOBILE --}}
        <div class="d-md-none">
            @foreach ($reservations as $reservation)
                <div class="mb-3 shadow-sm" style="{{ (
                            (!config('ipsum.reservation.etat_des_lieux.enable') and (($reservation->is_depart and $reservation->debut_at->lt(\Carbon\Carbon::now())) or (!$reservation->is_depart and $reservation->fin_at->lt(\Carbon\Carbon::now()))))
                            or (config('ipsum.reservation.etat_des_lieux.enable') and (($reservation->is_depart and $reservation->inspectionInitiale?->isSigned()) or (!$reservation->is_depart and $reservation->inspectionFinale?->isSigned())))
                            ) ? 'opacity: 0.5' : '' }}">
                    <div class="{{ $reservation->is_depart ? 'bg-success' : 'bg-info' }} text-center text-white p-1">
                        <span class="lead">{{ $reservation->is_depart ? $reservation->debut_at->format('H:i') : $reservation->fin_at->format('H:i') }}</span> -
                        {{ $reservation->is_depart ? 'Départ' : 'Retour' }}
                        {{ $reservation->reference }}
                    </div>
                    <div class="p-2">
                        <h3 class="mb-1">
                            <a href="{{ $reservation->categorie ? route('admin.categorie.edit', $reservation->categorie) : '#' }}">
                                Catégorie {{ $reservation->categorie_nom }}
                            </a>
                            @if ($reservation->vehicule)
                                -
                                <a href="{{ $reservation->vehicule ? route('admin.vehicule.edit', $reservation->vehicule) : '#' }}">
                                    {{ $reservation->vehicule->marque_modele }} -
                                    {{ $reservation->vehicule->immatriculation }}
                                </a>
                            @endif
                            @if ($reservation->prestations->count())
                                @if (!config('ipsum.reservation.depart_retour_affichage_complet'))
                                    <i class="fa fa-clipboard-list" data-toggle="tooltip" data-placement="auto" data-html="true" title="Prestations :<br>
                                        @foreach ($reservation->prestations as $prestation)
                                            {{ $prestation->quantite }} {{ strtolower($prestation->nom) }} {{ !empty($prestation->choix) ? '('.$prestation->choix.')' : '' }} <br>
                                        @endforeach
                                    "></i>
                                @else
                                    <br>Prestations :<br>
                                    @foreach ($reservation->prestations as $prestation)
                                        {{ $prestation->quantite }} {{ strtolower($prestation->nom) }} {{ !empty($prestation->choix) ? '('.$prestation->choix.')' : '' }} <br>
                                    @endforeach
                                @endif
                            @endif
                        </h3>
                        <p class="mb-3">
                            <i class="fa fa-map-marker-alt"></i>
                            {{ $reservation->is_depart ? $reservation->debut_lieu_nom : $reservation->fin_lieu_nom }}
                            @if ($reservation->custom_fields->vol)
                                @if (!config('ipsum.reservation.depart_retour_affichage_complet'))
                                    <i class="fa fa-plane-arrival" data-toggle="tooltip" data-placement="auto" data-html="true" title="Numéro de vol : {{ $reservation->custom_fields->vol }}"></i>
                                @else
                                    <br>Numéro de vol : {{ $reservation->custom_fields->vol }}
                                @endif
                            @endif
                        </p>
                        <p class="mb-3">
                            <i class="fa fa-user"></i> {{ $reservation->prenom }} {{ $reservation->nom }}<br>
                            <i class="fa fa-envelope"></i> <a href="mailto:{{ $reservation->email }}">{{ $reservation->email }}</a><br>
                            @if($reservation->telephone)
                                <i class="fa fa-phone"></i> <a href="tel:{{ $reservation->telephone }}">{{ $reservation->telephone }}</a><br>
                            @endif
                        </p>
                        @if(config('ipsum.reservation.caution_provider') && $reservation->caution)
                            <p class="mb-1">
                                @if($reservation->paiementCaution)
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    <span class="badge badge-success" data-toggle="tooltip" title="Caution sécurisée">
                                        Caution : @prix($reservation->paiementCaution->montant) €
                                    </span>
                                @elseif($reservation->caution_send_at)
                                    <i class="fas fa-hourglass-half mr-1"></i>
                                    <span class="badge badge-warning text-dark" data-toggle="tooltip" title="Demande envoyée le {{ $reservation->caution_send_at?->format('d/m H:i') ?? 'N/C' }}">
                                        Caution envoyée le {{ $reservation->caution_send_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </p>
                        @endif
                        <p class="mb-3">
                            <i class="fa fa-money-check-alt"></i>
                            <x-reservation::reste_a_payer total="{{ $reservation->total }}" montant_paye="{{ $reservation->montant_paye }}" />
                            {{ $reservation->condition ? '('.$reservation->condition->nom.')' : '' }}
                        </p>

                        <div class="text-right">
                            <form action="{{ route('admin.reservation.destroy', $reservation) }}" method="POST">
                                @if ($reservation->is_depart)
                                    @if(config('ipsum.reservation.etat_des_lieux.enable') === true)
                                        @if($reservation->inspectionInitiale?->isSigned())
                                            <a class="btn btn-outline-primary" href="{{ route('admin.inspection.pdf', [$reservation->inspectionInitiale]) }}" target="_blank" title="Voir l'état des lieux"><i class="fa fa-car-crash"></i></a>
                                        @else
                                            <a class="btn btn-outline-primary" href="{{ route('admin.inspection.vehicule', [$reservation, \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID ]) }}" title="État des lieux initial"><i class="fa fa-car"></i></a>
                                        @endif
                                    @else
                                        <a class="btn btn-outline-primary" href="{{ route('admin.reservation.contrat', [$reservation]) }}" title="contrat"><i class="fa fa-file-signature"></i></a>
                                    @endif
                                @elseif(config('ipsum.reservation.etat_des_lieux.enable') === true)
                                    @if($reservation->inspectionFinale?->isSigned())
                                        <a class="btn btn-outline-primary" href="{{ route('admin.inspection.pdf', [$reservation->inspectionFinale]) }}" target="_blank" title="Voir l'état des lieux"><i class="fa fa-car-crash"></i></a>
                                    @else
                                        <a class="btn btn-outline-primary" href="{{ route('admin.inspection.checklist', [$reservation, \Ipsum\Reservation\app\Models\Inspection\Type::FINAL_ID ]) }}" title="État des lieux final"><i class="fa fa-car"></i></a>
                                    @endif
                                @endif
                                <a class="btn btn-outline-secondary" href="{{ route('admin.reservation.edit', [$reservation]) }}"><i class="fa fa-edit"></i></a>
                                @can('delete', $reservation)
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></button>
                                @endcan
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
