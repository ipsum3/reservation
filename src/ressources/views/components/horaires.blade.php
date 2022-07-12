@props(['min' => '7:00', 'max' => '21:00', 'step' => 30, 'value' => '16:00'])

@php

$name = $attributes->get('name');

@endphp


<select {{ $attributes->merge([]) }}>
    @for ($heure = (int) $min; $heure <= (int) $max; $heure++)
        @for ($minute = 0; $minute <= $step; $minute = $minute + 30)
            <option value="{{{ str_pad($heure, 2, '0', STR_PAD_LEFT) }}}:{{{ str_pad($minute, 2, '0', STR_PAD_LEFT) }}}"
                    {{{ old($name, $value) == str_pad($heure, 2, '0', STR_PAD_LEFT).':'.str_pad($minute, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}}>
                {{{ $heure }}}h{{{ str_pad($minute, 2, '0', STR_PAD_LEFT) }}}
            </option>
        @endfor
    @endfor
</select>