@php
    list($resas, $decalage_max) = addInfosToReservation($type == 'vehicule' ? $vehicule->reservations : collect([$reservation]), $date_debut, $date_fin);
@endphp

<tr class="planning-ligne">
    <th class="planning-{{ $type }}-entete"
        @if ($decalage_max)
            style="height: {{ 10 + (50 * ($decalage_max + 1)) }}px"
        @endif
    >
        @if ($type == 'vehicule')
            <a href="{{ route('admin.vehicule.edit', $vehicule) }}">
                {{ $vehicule->immatriculation }}<br>
                {{ $vehicule->marque_modele }}
            </a>
        @else
            Réservation {{ $reservation->reference }}
        @endif
    </th>
    @for($date = $date_debut->copy(); $date->lte($date_fin); $date->addDay())
        <td class="planning-case">
            <div>
                @if (isset($resas[$date->format('Y-m-d')]))
                    @foreach($resas[$date->format('Y-m-d')] as $resa)
                        <a href="{{ route('admin.reservation.edit', $resa) }}" style="width: {{ $resa->width }}px; left: {{ $resa->decalage }}px; top: {{ $resa->top }}px;"
                           class="planning-reservation {{ ($type == 'vehicule' and !$resa->has_conflit) ? 'bg-success' : 'bg-danger' }}"
                           data-toggle="tooltip" data-placement="auto" data-html="true" title="
                                                         <div>Réservation : {{ $resa->reference }}</div>
                                                         <div>
                                                            Départ : {{ $resa->debut_lieu_nom }} {{ $resa->debut_at->format('d/m/Y H\hi') }}<br>
                                                            Retour : {{ $resa->fin_lieu_nom }} {{ $resa->fin_at->format('d/m/Y H\hi') }}<br>
                                                         </div>"
                        >
                            @if ($resa->has_conflit)
                                <i class="fa fa-exclamation-triangle"></i>
                            @endif
                            {{ $resa->prenom.' '.$resa->nom }} {{$resa->overlaps}}
                        </a>
                    @endforeach
                @endif
            </div>
        </td>
    @endfor
</tr>