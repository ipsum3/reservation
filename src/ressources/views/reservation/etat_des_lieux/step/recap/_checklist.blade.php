@foreach($checklists as $item)
    @if( in_array($item->id, old('checklists', $inspection->checklists->pluck('id')->toArray() ?? [])) )
        <div class="alert alert-success" role="alert">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="alert-text">
                {{ $item->nom }}
            </div>
        </div>
    @else
        <div class="alert alert-danger" role="alert">
            <div class="alert-icon">
                <i class="fas fa-minus-circle"></i>
            </div>
            <div class="alert-text">
                {{ $item->nom }}
            </div>
        </div>
    @endif

@endforeach