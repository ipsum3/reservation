@props([
    'min' => '7:00',
    'max' => '21:00',
    'step' => 30,
    'value' => '16:00',
    'date' => null,
    'isDeparture' => true // Permet de distinguer le comportement Départ / Retour
])

@php
    use Carbon\Carbon;

    $name = $attributes->get('name');
    $maxTime = Carbon::parse($max);
    $minTime = Carbon::parse($min);

    // Initialisation de la valeur sélectionnée par défaut
    $selectedValue = old($name, $value);

    // Si on est aujourd'hui, on recalcule la sélection automatique par défaut
    if ($date && Carbon::parse($date)->isToday()) {
        $now = Carbon::now();

        if ($isDeparture) {
            // DEPART : Heure actuelle arrondie au $step supérieur
            $currentMinute = $now->minute;
            $ceilMinute = ceil($currentMinute / $step) * $step;

            if ($ceilMinute >= 60) {
                $now->addHour()->minute(0);
            } else {
                $now->minute($ceilMinute);
            }

            // Sécurité : On ne descend pas en dessous du minimum du magasin
            if ($now->lessThan($minTime)) {
                $now = $minTime->copy();
            }
            // Sécurité : On ne dépasse pas le maximum
            if ($now->greaterThan($maxTime)) {
                $now = $maxTime->copy();
            }

            $selectedValue = $now->format('H:i');
        } else {
            // RETOUR : Durée la plus petite possible (Heure actuelle + 1 step par exemple)
            // On prend l'heure actuelle arrondie supérieure + 1 fois le step
            $currentMinute = $now->minute;
            $ceilMinute = ceil($currentMinute / $step) * $step;

            if ($ceilMinute >= 60) {
                $now->addHour()->minute(0);
            } else {
                $now->minute($ceilMinute);
            }

            // On ajoute un step pour avoir la "durée la plus petite" après le départ estimé
            $duree = Cache::remember('duree_min', 60*60*24, function () {
                return \Ipsum\Reservation\app\Models\Tarif\Duree::orderBy('min')->select('min')->first();
            });
            $duree_min = $duree->min_display ?? $step;
            $now->addMinutes($duree_min);

            if ($now->lessThan($minTime)) {
                $now = $minTime->copy();
            }
            if ($now->greaterThan($maxTime)) {
                $now = $maxTime->copy();
            }

            $selectedValue = $now->format('H:i');
        }

        if($selectedValue < old($name, $value)){
            $selectedValue = old($name, $value);
        }
    }

    $minHour = $minTime->hour;
    $minMinute = $minTime->minute;
    $adjustedMinHour = $minHour;
    $adjustedMinMinute = ceil($minMinute / $step) * $step;
    if ($adjustedMinMinute >= 60) {
        $adjustedMinHour++;
        $adjustedMinMinute = 0;
    }
    $minTime->hour($adjustedMinHour)->minute($adjustedMinMinute);
    $options = [];
@endphp

<select {{ $attributes->merge([]) }}>
    @if( Carbon::parse($min) != $minTime )
        <option value="{{ Carbon::parse($min)->format('H:i') }}"
                {{ $selectedValue == Carbon::parse($min)->format('H:i') ? 'selected' : '' }}>
            {{ Carbon::parse($min)->format('H\hi') }}
        </option>
    @endif

    @for ($time = $minTime->copy(); $time <= $maxTime; $time->addMinutes($step))
        <option value="{{ $time->format('H:i') }}"
                {{ $selectedValue == $time->format('H:i') ? 'selected' : '' }}>
            {{ $time->format('H\hi') }}
        </option>
        @php
            $options[] = $time->format('H\hi');
        @endphp
    @endfor

    @if( !in_array($maxTime->format('H\hi'), $options) )
        <option value="{{ $maxTime->format('H:i') }}"
                {{ $selectedValue == $maxTime->format('H:i') ? 'selected' : '' }}>
            {{ $maxTime->format('H\hi') }}
        </option>
    @endif
</select>
