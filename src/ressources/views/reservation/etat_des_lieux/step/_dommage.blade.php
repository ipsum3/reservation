<div class="media" style="min-width: 200px">
    <div class="media-img">
        @if($dommage->illustration)
            <a href="{{ asset($dommage->illustration->path) }}" target="_blank" title="Voir">
                <img src="{{ Croppa::url($dommage->illustration->cropPath, 300, null/*, ['pad' => '255,255,255']*/) }}" alt="{{ $dommage->illustration->tagAlt }}">
            </a>
        @else
            <p class="p-2">Aucune photo</p>
        @endif
    </div>
    <div class="media-title d-block">
        <p>
            {{ $dommage->type?->nom }} - {{ $dommage->emplacement? $dommage->emplacement?->nom.' - ' : ''}} {{ $dommage->element?->nom}}
        </p>
        @if($dommage->observations)
            <p class="text-muted">{!! nl2br(e($dommage->observations)) !!}</p>
        @endif
    </div>
    <div class="media-toolbar">
        @if(empty($protected))
        <ul>
            <li>
                <a href="{{ route('admin.inspection.dommage.edit', [$reservation, $type, $dommage]) }}" title="Modifier">
                    <span class="fa fa-edit"></span>
                </a>
            </li>
            <li>
                <form action="{{ route('admin.inspection.dommage.destroy', [$reservation, $type, $dommage]) }}" method="POST" onsubmit="return confirm('Confirmer la suppression de ce dommage ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit">
                        <span class="fa fa-trash-alt"></span>
                    </button>
                </form>
            </li>
        </ul>
        @endif
    </div>
</div>
