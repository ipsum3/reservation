
<div>
    <div style="text-align:center; margin-bottom:5px;">
        @php
            Croppa::render(Croppa::url($photo->cropPath, 600))
        @endphp
        <img src="{{ public_path(Croppa::url($photo->cropPath, 600)) }}"
             alt="{{ $photo->titre }}"
             style="width:100%; height:auto; border-radius:4px; border:1px solid #ddd;">
    </div>
</div>
