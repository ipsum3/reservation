@foreach($checklists as $item)
    <div class="form-check mt-2">
        @if( in_array($item->id, old('checklists', $inspection->checklists->pluck('id')->toArray() ?? [])) )
            <i class="fa fa-check-square text-success"></i>
        @else
            <i class="fa fa-window-close text-danger"></i>
        @endif
        <label class="form-check-label" for="checklist_{{ $item->id }}">
            {{ $item->nom }}
        </label>
    </div>
@endforeach