<div class="media sortable-item" data-sortable="{{ $media->id }}">
    <div class="media-img">
        @if ($media->isImage)
            <a href="{{ Croppa::url($media->cropPath, 1200) }}" target="_blank" title="Voir">
                <img src="{{ Croppa::url($media->cropPath, 200, 200) }}" alt="{{ $media->tagAlt }}" width="100%" />
            </a>
        @else
            <span class="media-icone {{ $media->icone }}"></span>
        @endif
    </div>
    <div class="media-title">
        {{ $media->titre }}
    </div>
    <div class="media-toolbar">
        <ul>
        </ul>
    </div>
</div>