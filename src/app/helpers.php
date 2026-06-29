<?php

if (! function_exists('duration')) {

    /**
     * Formate une durée exprimée en minutes
     */
    function duration(?int $minutes): string
    {
        if ($minutes === null) {
            return '-';
        }

        $totalMinutes = $minutes;

        $jours = intdiv($totalMinutes, 1440);
        $reste = $totalMinutes % 1440;

        $heures = intdiv($reste, 60);
        $minutes = $reste % 60;

        // Cas inférieur à 1 jour
        if ($jours === 0) {

            if ($minutes === 0) {
                return $heures.' heure'.($heures > 1 ? 's' : '');
            }

            return sprintf('%dh%02d', $heures, $minutes);
        }

        // Jour(s) entier(s)
        if ($heures === 0 && $minutes === 0) {
            return $jours.' jour'.($jours > 1 ? 's' : '');
        }

        $texte = $jours.' jour'.($jours > 1 ? 's' : '');

        if ($heures) {
            $texte .= ' '.$heures.'h';
        }

        if ($minutes) {
            $texte .= sprintf('%02d', $minutes);
        }

        return $texte;
    }

}

if (! function_exists('duration_parts')) {

    if (! function_exists('duration_parts')) {

        function duration_parts(?int $minutes): array
        {
            if ($minutes === null) {
                return [
                    'days' => null,
                    'hours' => null,
                    'minutes' => null,
                ];
            }

            $days = intdiv($minutes, 1440);
            $hours = intdiv($minutes % 1440, 60);
            $remainingMinutes = $minutes % 60;

            return [
                'days' => $days,
                'hours' => $hours,
                'minutes' => $remainingMinutes,
            ];
        }
    }

}
