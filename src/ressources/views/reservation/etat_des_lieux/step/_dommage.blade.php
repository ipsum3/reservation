<div class="media bg-white col-md-2" >
    <div class="media-img h-100 bg-light">
        @if($dommage->illustration)
            <a href="{{ Croppa::url($dommage->illustration->cropPath, 1200) }}" target="_blank" title="Voir">
                <img src="{{ Croppa::url($dommage->illustration->cropPath, 364, 178, ['pad' => '220,219,219']) }}" alt="{{ $dommage->illustration->tagAlt }}" width="100%">
            </a>
        @else
            <p class="p-2" style="max-width: 327px; min-width: 327px; width: 100%;">Aucune photo</p>
        @endif
    </div>
    <div class="media-toolbar">
        <div>
            <h5 class="card-title text-primary text-center p-2">
                {{ $dommage->type?->nom }}<br> {{ $dommage->emplacement? $dommage->emplacement?->nom.' - ' : ''}} {{ $dommage->element?->nom}}
            </h5>
        </div>
        @if(!$dommage->protected)
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
