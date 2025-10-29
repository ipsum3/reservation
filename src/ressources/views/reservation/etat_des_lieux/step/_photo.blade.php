<div class="media bg-white col-md-2" >
    <div class="media-img h-100 bg-light">
        @if($media->isImage)
            <a href="{{ Croppa::url($media->cropPath, 1200) }}" target="_blank" title="Voir">
                <img src="{{ Croppa::url($media->cropPath, 364, 178, ['pad' => '220,219,219']) }}" alt="{{ $media->tagAlt }}" width="100%">
            </a>
        @else
            <p class="p-2" style="max-width: 327px; min-width: 327px; width: 100%;">Aucune photo</p>
        @endif
    </div>
    <div class="media-toolbar">
        <div>
            <h5 class="card-title text-primary text-center p-2">
                Photo n° {{ $loop->iteration }}
            </h5>
        </div>
        @if(!$media->protected)
            <ul>
                <li>
                    <a href="{{ route('admin.media.getDestroy', $media->id) }}" data-toggle="tooltip" title="Supprimer"><span class="fa fa-trash-alt"></span></a>
                </li>
            </ul>
        @endif
    </div>
</div>