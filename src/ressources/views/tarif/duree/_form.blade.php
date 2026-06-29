<div class="row justify-content-around">
    {{ Aire::input('nom', 'Nom')->groupAddClass('col-md-6') }}
    <div class="col-lg-6">
            <div class="form-row">
                <div class="form-group col-4" data-aire-component="group" data-aire-for="min_jours">
                    <label class="cursor-pointer" for="min_jours">Mini Jours*</label>
                    <input type="number" class="form-control" name="min_jours" min="0" id="min_jours" value="{{ old('min_jours', duration_parts($duree->min_display)['days']) }}">
                </div>
                <div class="form-group col-4" data-aire-component="group" data-aire-for="min_heures">
                    <label class="cursor-pointer" for="min_heures">Mini Heures*</label>
                    <input type="number" class="form-control" name="min_heures" min="0" max="23" id="min_heures" value="{{ old('min_heures', duration_parts($duree->min_display)['hours']) }}">
                </div>
                <div class="form-group col-4" data-aire-component="group" data-aire-for="min_minutes">
                    <label class="cursor-pointer" for="min_minutes">Mini Minutes*</label>
                    <input type="number" class="form-control" name="min_minutes" min="0" max="59" id="min_minutes" value="{{ old('min_minutes', duration_parts($duree->min_display)['minutes']) }}">
                </div>
            </div>
            <ul class="invalid-feedback d-block" data-aire-component="errors" data-aire-for="min"></ul>
    </div>

    {{ Aire::select(array_combine(\Ipsum\Reservation\app\Models\Tarif\Duree::TARIFICATION, Ipsum\Reservation\app\Models\Tarif\Duree::TARIFICATION), 'tarification', 'Tarification*')->groupAddClass('col-md-6') }}
    <div class="col-lg-6">
            <div class="form-row">
                <div class="form-group col-4" data-aire-component="group" data-aire-for="max_jours">
                    <label class="cursor-pointer" for="max_jours">Maxi Jours</label>
                    <input type="number" class="form-control" name="max_jours" min="0" id="max_jours" value="{{ old('max_jours', duration_parts($duree->max)['days']) }}">
                </div>
                <div class="form-group col-4" data-aire-component="group" data-aire-for="max_heures">
                    <label class="cursor-pointer" for="max_heures">Maxi Heures</label>
                    <input type="number" class="form-control" name="max_heures" min="0" max="23" id="max_heures" value="{{ old('max_heures', duration_parts($duree->max)['hours']) }}">
                </div>
                <div class="form-group col-4" data-aire-component="group" data-aire-for="max_minutes">
                    <label class="cursor-pointer" for="max_minutes">Maxi Minutes</label>
                    <input type="number" class="form-control" name="max_minutes" min="0" max="59" id="max_minutes" value="{{ old('max_minutes', duration_parts($duree->max)['minutes']) }}">
                </div>
            </div>
            <ul class="invalid-feedback d-block" data-aire-component="errors" data-aire-for="max"></ul>
    </div>
</div>
