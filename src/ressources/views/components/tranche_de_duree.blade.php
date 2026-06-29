@props([
    'min',
    'max' => null,
])

@php

    $format = function (int $minutes): array {

        $parts = duration_parts($minutes);

        // Moins d'un jour
        if ($parts['days'] === 0) {

            return [
                'type'  => 'hour',
                'label' => $parts['minutes']
                    ? sprintf('%dh%02d', $parts['hours'], $parts['minutes'])
                    : sprintf('%dh', $parts['hours']),
            ];

        }

        // Nombre entier de jours
        if ($parts['hours'] === 0 && $parts['minutes'] === 0) {

            return [
                'type'  => 'day',
                'label' => (string) $parts['days'],
            ];

        }

        // Jour + heure
        return [
            'type' => 'mixed',
            'label' => sprintf(
                '%d jours %dh%02d',
                $parts['days'],
                $parts['hours'],
                $parts['minutes']
            ),
        ];

    };

    $minValue = $format($min);
    $maxValue = $max !== null ? $format($max) : null;

@endphp

@if ($max === null)

    @if ($minValue['type'] === 'hour')
        {{ $minValue['label'] }} et plus
    @else
        {{ $minValue['label'] }} jours et plus
    @endif

@elseif ($minValue['type'] === 'hour' && $maxValue['type'] === 'hour')

    {{ $minValue['label'] }} à {{ $maxValue['label'] }}

@elseif ($minValue['type'] === 'day' && $maxValue['type'] === 'day')

    @if ($minValue['label'] === $maxValue['label'])

        {{ $minValue['label'] }} jour{{ $minValue['label'] > 1 ? 's' : '' }}

    @else

        {{ $minValue['label'] }} à {{ $maxValue['label'] }} jours

    @endif

@else

    {{ $minValue['type'] === 'day' ? $minValue['label'].' jours' : $minValue['label'] }}
    à
    {{ $maxValue['type'] === 'day' ? $maxValue['label'].' jours' : $maxValue['label'] }}

@endif