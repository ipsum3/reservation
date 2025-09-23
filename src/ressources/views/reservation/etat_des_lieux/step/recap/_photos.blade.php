@php
    $photos = $inspection->medias()->groupe('photos')->get();
@endphp
@if($photos->count())
    @foreach($photos as $media)
        <td>
            <img class="col-md-4" src="{{ config('app.url') }}{{ Croppa::url($media->cropPath, 400) }}" alt="{{ $media->titre }}">
        </td>
    @endforeach
@endif