<div class="row">
    {{ Aire::input('nom', 'Nom')->groupAddClass('col-md-6') }}
    {{ Aire::select(array_combine(\Ipsum\Reservation\app\Models\Tarif\Duree::TARIFICATION, Ipsum\Reservation\app\Models\Tarif\Duree::TARIFICATION), 'tarification', 'Tarification*')->groupAddClass('col-md-6') }}



    <div class="col-lg-12">
        <div class="form-row">
            <div class="form-group col-3">
                <label for="min_format">Format</label>
                <div class="custom-control custom-switch form-check">
                    <input type="hidden" name="min_format" value="minute">
                    <input name="min_format" value="jour" type="checkbox" class="custom-control-input" id="min_format" {{ old('min_format', $duree->min_format) === 'jour' ? 'checked' : '' }} data-toggle="collapse" href=".collapseMin" role="button" aria-expanded="{{ old('min_format', $duree->min_format) === 'jour' ? 'false' : 'true' }}" aria-controls="collapseMin">
                    <label class="custom-control-label" for="min_format">
                        jour/minute
                    </label>
                </div>
            </div>
            <div class="form-group col-3" data-aire-component="group" data-aire-for="min_jours">
                <label class="cursor-pointer" for="min_jours">Mini Jours*</label>
                <input type="number" class="form-control" name="min_jours" min="0" id="min_jours" value="{{ old('min_jours', duration_parts($duree->min_display)['days']) }}" required>
                @error('min_jours')
                <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group col-3 collapse {{ old('min_format', $duree->min_format) === 'jour' ? '' : 'show' }} collapseMin" data-aire-component="group" data-aire-for="min_heures">
                <label class="cursor-pointer" for="min_heures">Mini Heures*</label>
                <input type="number" class="form-control" name="min_heures" min="0" max="23" id="min_heures" value="{{ old('min_heures', duration_parts($duree->min_display)['hours']) }}">
                @error('min_heures')
                <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group col-3 collapse {{ old('min_format', $duree->min_format) === 'jour' ? '' : 'show' }} collapseMin" data-aire-component="group" data-aire-for="min_minutes">
                <label class="cursor-pointer" for="min_minutes">Mini Minutes*</label>
                <input type="number" class="form-control" name="min_minutes" min="0" max="59" id="min_minutes" value="{{ old('min_minutes', duration_parts($duree->min_display)['minutes']) }}">
                @error('min_minutes')
                <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="form-row">
            <div class="form-group col-3">
                <label for="max_format">Format</label>
                <div class="custom-control custom-switch form-check">
                    <input type="hidden" name="max_format" value="minute">
                    <input name="max_format" value="jour" type="checkbox" class="custom-control-input" id="max_format" {{ old('max_format', $duree->max_format) === 'jour' ? 'checked' : '' }} data-toggle="collapse" href=".collapseMax" role="button" aria-expanded="{{ old('max_format', $duree->max_format) === 'jour' ? 'false' : 'true' }}" aria-controls="collapseMax">
                    <label class="custom-control-label" for="max_format">
                        jour/minute
                    </label>
                </div>
            </div>
            <div class="form-group col-3" data-aire-component="group" data-aire-for="max_jours">
                <label class="cursor-pointer" for="max_jours">Maxi Jours</label>
                <input type="number" class="form-control" name="max_jours" min="0" id="max_jours" value="{{ old('max_jours', duration_parts($duree->max_display)['days']) }}">
                @error('max_jours')
                <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group col-3 collapse {{ old('max_format', $duree->max_format) === 'jour' ? '' : 'show' }} collapseMax" data-aire-component="group" data-aire-for="max_heures">
                <label class="cursor-pointer" for="max_heures">Maxi Heures</label>
                <input type="number" class="form-control" name="max_heures" min="0" max="23" id="max_heures" value="{{ old('max_heures', duration_parts($duree->max_display)['hours']) }}">
                @error('max_heures')
                <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group col-3 collapse {{ old('max_format', $duree->max_format) === 'jour' ? '' : 'show' }} collapseMax" data-aire-component="group" data-aire-for="max_minutes">
                <label class="cursor-pointer" for="max_minutes">Maxi Minutes</label>
                <input type="number" class="form-control" name="max_minutes" min="0" max="59" id="max_minutes" value="{{ old('max_minutes', duration_parts($duree->max_display)['minutes']) }}">
                @error('max_minutes')
                <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>
