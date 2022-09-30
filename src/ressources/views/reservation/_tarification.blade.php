@if ($reservation->promotions)
    <div class="alert alert-info">
        @foreach ($reservation->promotions as $promotion)
            {{ _('Offre') }} {{ strtolower($promotion['nom']) }} : -@prix($promotion['reduction'])&nbsp;€<br>
        @endforeach
    </div>
@endif
@if ($reservation->prestations)
    <div class="form-row">
        @foreach($prestations as $prestation)
            @php
                $presta = $reservation->prestations->getById($prestation->id);
                if (isset($devis) and $devis->getPrestations()->hasByPrestation($prestation)) {
                    $tarif = $devis->getPrestations()->getByPrestation($prestation)->getTarif();
                    $quantite = $devis->getPrestations()->getByPrestation($prestation)->getQuantite();
                    $quantite = empty($quantite) ? 1 : $quantite; // prestation de type frais
                } else {
                    $tarif = $presta->tarif ?? null;
                    $quantite = $presta->quantite ?? null;
                }
            @endphp
            <div class="form-group col-md-6">
                <label class=" cursor-pointer" data-aire-component="label" for="presta-{{ $prestation->id }}">
                    {{ $prestation->nom }}
                </label>
                <div class="form-row">
                    <div class="form-group col-6">
                        <label class=" cursor-pointer" data-aire-component="label" for="presta-{{ $prestation->id }}">
                            Quantité
                        </label>
                        <select class="form-control " data-aire-component="select" name="prestations[{{ $prestation->id }}][quantite]" id="presta-{{ $prestation->id }}">
                            <option value=""> ---- Quantité ----- </option>
                            @for($i = 1; $i <= $prestation->quantite_max; $i++)
                                <option value="{{ $i }}" @selected(old('prestations.'.$prestation->id.'.quantite', $quantite) == $i)>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label class="cursor-pointer" data-aire-component="label" for="presta-tarif-{{ $prestation->id }}">
                            Tarif €
                        </label>
                        <input class="form-control" type="text" name="prestations[{{ $prestation->id }}][tarif]" value="{{ old('prestations.'.$prestation->id.'.tarif', $tarif) }}" id="presta-tarif-{{ $prestation->id }}">
                    </div>
                </div>
                <input type="hidden" name="prestations[{{ $prestation->id }}][id]" value="{{ $prestation->id }}">
                <input type="hidden" name="prestations[{{ $prestation->id }}][nom]" value="{{ $prestation->nom }}">
                <input type="hidden" name="prestations[{{ $prestation->id }}][tarification]" value="{{ $presta->tarification ?? $prestation->tarification }}">
            </div>
        @endforeach
    </div>
@endif

<div class="form-row">

    {{ Aire::number('franchise', 'Franchise (€)')->setAttribute('step', 0.01)->value(isset($devis) ? $reservation->categorie->franchise : null)->groupAddClass('col-md-6') }}

    {{ Aire::number('montant_base', 'Montant de base (€)')->setAttribute('step', 0.01)->value(isset($devis) ? $devis->getMontantBase() : null)->groupAddClass('col-md-6') }}
    {{ Aire::number('total', 'Total (€)')->setAttribute('step', 0.01)->value(isset($devis) ? $devis->getTotal() : null)->groupAddClass('col-md-6') }}

</div>