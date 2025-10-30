
    <div style=" border:1px solid #ccc; border-radius:6px; padding:6px; box-sizing:border-box; background:#fafafa; margin-bottom:8px;">
        <div style="text-align:center; margin-bottom:5px;">
            @if($media->isImage)
                <img src="{{ config('app.url') }}{{ Croppa::url($media->cropPath, 600) }}"
                     alt="{{ $media->titre }}"
                     style="width:100%; height:auto; border-radius:4px; border:1px solid #ddd;">
            @else
                <div style="width:100%; min-height:130px; background:#eee; border-radius:4px; display:flex; align-items:center; justify-content:center; color:#999;padding-top: 10px">
                    Aucune photo
                </div>
            @endif
        </div>

        <div style="font-size:11px; line-height:1.4;">
            <p><strong>Photo n° {{ $loop->iteration }}</strong></p>
        </div>
    </div>
