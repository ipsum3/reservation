{{ _('Catégorie') }} {{ $reservation->categorie_nom }}<br>
@if ($reservation->vehicule)
    {{ _('Marque et modéle') }} : {{ $reservation->vehicule->marque_modele }}<br>
    {{ _('Immatriculation') }} : {{ $reservation->vehicule->immatriculation }}<br>
@endif
{{ _('Kilométrage') }} : {{ $inspection?->kilometrage }}<br>
{{ _('Carburant') }} : {{ $inspection?->carburant }}/8<br>