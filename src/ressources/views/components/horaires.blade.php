@props(['min' => '7:00', 'max' => '21:00', 'step' => 30, 'value' => '16:00'])

@php
    use Carbon\Carbon;

    $name = $attributes->get('name');
    $minTime = Carbon::parse($min);
    $maxTime = Carbon::parse($max);
@endphp

<select {{ $attributes->merge([]) }}>
    @for ($time = $minTime->copy(); $time <= $maxTime; $time->addMinutes($step))
        <option value="{{ $time->format('H:i') }}"
                {{ old($name, $value) == $time->format('H:i') ? 'selected' : '' }}>
            {{ $time->format('H\hi') }}
        </option>
    @endfor
</select>