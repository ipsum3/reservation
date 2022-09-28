@php
    $resas = addInfosToReservation($type == 'vehicule' ? $vehicule->reservations : [$reservation], $date_debut, $date_fin);
@endphp

<tr class="planning-ligne">
    <th class="planning-{{ $type }}-entete">
        @if ($type == 'vehicule')
            {{ $vehicule->immatriculation }}<br>
            {{ $vehicule->marque_modele }}
        @else
            Réservation {{ $reservation->reference }}
        @endif
    </th>
    @for($date = $date_debut->copy(); $date->lte($date_fin); $date->addDay())
        <td class="planning-case">
            <div>
                @if (isset($resas[$date->format('Y-m-d')]))
                    @foreach($resas[$date->format('Y-m-d')] as $resa)
                        <a href="{{ route('admin.reservation.edit', $resa) }}" style="width: {{ $resa->width }}px; left: {{ $resa->decalage }}px;"
                           class="planning-reservation {{ $type == 'vehicule' ? 'bg-success' : 'bg-danger' }}"
                           data-toggle="tooltip" data-placement="auto" data-html="true" title="
                                                         <div>Réservation : {{ $resa->reference }}</div>
                                                         <div>
                                                            Départ : {{ $resa->debut_lieu_nom }} {{ $resa->debut_at->format('d/m/Y H\hi') }}<br>
                                                            Retour : {{ $resa->fin_lieu_nom }} {{ $resa->fin_at->format('d/m/Y H\hi') }}<br>
                                                         </div>"
                        >
                            {{ $resa->prenom.' '.$resa->nom }}
                        </a>
                    @endforeach
                @endif
            </div>
        </td>
    @endfor
</tr>