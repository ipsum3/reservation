@if($reservation->vehicule?->dommages && $inspection->type_id == \Ipsum\Reservation\app\Models\Inspection\Type::INITIAL_ID)
    @foreach($reservation->vehicule?->dommages as $dommage)
        @if($dommage->inspection->id != $inspection->id && $dommage->inspection->id != $reservation->inspection_initiale->id)
            <tr>
                <td>{{ $dommage->type->nom }}</td>
                <td>{{ $dommage->emplacement->nom }}</td>
                <td>{{ $dommage->element->nom }}</td>
                <td>{!! $dommage->observations !!}</td>
                <td>
                    @php $media = $dommage?->inspection->medias()->groupe($dommage->id)->first(); @endphp
                    @if($media)
                        <img src="{{ config('app.url') }}{{ Croppa::url($media->cropPath, 200) }}" alt="{{ $media->titre }}">
                    @endif
                </td>
            </tr>
        @endif
    @endforeach
@endif


@if($inspection->dommages->count())
    @foreach($inspection->dommages as $dommage)
        <tr>
            <td>{{ $dommage->type->nom }}</td>
            <td>{{ $dommage->emplacement->nom }}</td>
            <td>{{ $dommage->element->nom }}</td>
            <td>{!! $dommage->observations !!}</td>
            <td>
                @php $media = $inspection->medias()->groupe($dommage->id)->first(); @endphp
                @if($media)
                    <img src="{{ config('app.url') }}{{ Croppa::url($media->cropPath, 200) }}" alt="{{ $media->titre }}">
                @endif
            </td>
        </tr>
    @endforeach
@endif