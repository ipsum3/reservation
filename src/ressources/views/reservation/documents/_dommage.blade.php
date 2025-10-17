
    <div style=" border:1px solid #ccc; border-radius:6px; padding:6px; box-sizing:border-box; background:#fafafa; margin-bottom:8px;">
        <div style="text-align:center; margin-bottom:5px;">
            @if($dommage->illustration)
                <img src="{{ config('app.url') }}{{ Croppa::url($dommage->illustration->cropPath, 600) }}"
                     alt="{{ $dommage->illustration->titre }}"
                     style="width:100%; height:auto; border-radius:4px; border:1px solid #ddd;">
            @else
                <div style="width:100%; min-height:130px; background:#eee; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#999;padding-top: 10px">
                    Aucune photo
                </div>
            @endif
        </div>

        <div style="font-size:11px; line-height:1.4;">
            <p>{{ $dommage->emplacement? $dommage->emplacement?->nom.' - ' : ''}} {{ $dommage->element? $dommage->element?->nom.' - ' : ''}} {{ $dommage->type?->nom }}</p>
            @if($dommage->observations)
                <p><small>{!! $dommage->observations !!}</small></p>
            @endif
        </div>
    </div>
