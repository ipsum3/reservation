<div class="media">
    <div class="media-img h-100 bg-light">
        @if($dommage->illustration)
            <img src="{{ Croppa::url($dommage->illustration->cropPath, 400) }}" alt="{{ $dommage->illustration->tagAlt }}">
        @else
            <p class="p-2">Aucune photo</p>
        @endif
    </div>
    <div class="media-toolbar">
        <div>
            <h5 class="card-title text-primary text-center p-2">
                {{ $dommage->emplacement? $dommage->emplacement?->nom.' - ' : ''}} {{ $dommage->element? $dommage->element?->nom.' - ' : ''}} {{ $dommage->type?->nom }}
            </h5>
        </div>
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
                    <span class="fa fa-trash-alt"></span>
                </form>
            </li>
        </ul>
    </div>
</div>