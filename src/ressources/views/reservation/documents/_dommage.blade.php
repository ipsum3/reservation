
    <div style=" /*border:1px solid #ccc; padding:6px; box-sizing:border-box; background:#fafafa; margin-bottom:8px;*/">
        <div style="text-align:center; margin-bottom:5px;">
            @if($dommage->illustration)
                <img src="{{ config('app.url') }}{{ Croppa::url($dommage->illustration->cropPath, 600) }}"
                     alt="{{ $dommage->illustration->titre }}"
                     style="width:100%; height:auto;">
            @else
                <div style="width:100%; min-height:130px; background:#eee; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#999;padding-top: 10px">
                    Aucune photo
                </div>
            @endif
        </div>

        <div style="font-size:11px; line-height:1.4;">
            {{ $dommage->emplacement? $dommage->emplacement?->nom.' - ' : ''}} {{ $dommage->element? $dommage->element?->nom.' - ' : ''}} {{ $dommage->type?->nom }}<br>
            @if($dommage->observations)
                <small>{!! $dommage->observations !!}</small><
            @endif
        </div>
    </div>
