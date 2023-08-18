<select class="form-control" name="vehicule_id" id="vehicule_id" tabindex="-1" aria-hidden="true">
    <option value="">---- Véhicules -----</option>
    @foreach($vehicules as $vehicule)
        <option value="{{ $vehicule->id }}"
                class="@if ($vehicule->interventions_count)
                    text-info
                    @elseif(!$vehicule->reservations_count or ($vehicule->reservations_count == 1 and $vehicule->id == $vehicule_id))
                    text-success
                    @else
                    text-danger
                    @endif
                    "
                {{ old('vehicule_id', $vehicule_id) == $vehicule->id ? 'selected' : '' }}>
            {{ $vehicule->categorie->nom.' : '.$vehicule->immatriculation.' '.$vehicule->marque_modele }}
        </option>
    @endforeach
</select>