@props(['min' => '7:00', 'max' => '21:00', 'step' => 30, 'value' => '16:00'])

@php
    use Carbon\Carbon;

    $name = $attributes->get('name');
    $maxTime = Carbon::parse($max);
    $minTime = Carbon::parse($min);

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
                {{ old($name, $value) == Carbon::parse($min)->format('H:i') ? 'selected' : '' }}>
            {{ Carbon::parse($min)->format('H\hi') }}
        </option>
    @endif

    @for ($time= $minTime->copy(); $time <= $maxTime; $time->addMinutes($step))
        <option value="{{ $time->format('H:i') }}"
                {{ old($name, $value) == $time->format('H:i') ? 'selected' : '' }}>
            {{ $time->format('H\hi') }}
        </option>
        @php
            $options[] = $time->format('H\hi');
        @endphp
    @endfor

    @if( !in_array($maxTime->format('H\hi'), $options) )
        <option value="{{ $maxTime->format('H:i') }}"
                {{ old($name, $value) == $maxTime->format('H:i') ? 'selected' : '' }}>
            {{ $maxTime->format('H\hi') }}
        </option>
    @endif
</select>