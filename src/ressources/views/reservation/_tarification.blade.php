@if (request()->undo)
    {{ Aire::bind($reservation) }}
@endif

@php
    $promotions = isset($devis) ? $devis->getPromotions() : $reservation->promotions ?? [];
@endphp

@if (isset($promotions_diff) and $promotions_diff->count())
    <div class="alert alert-warning">
        <p>
            @if($promotions_diff->count() > 1)
                Les promotions ci-dessous ne sont plus applicables :
            @else
                La promotion ci-dessous n'est plus applicable :
            @endif
        </p>
        <ul class="mb-0">
            @foreach($promotions_diff as $promotion)
                <li>{{ _('Offre') }} {{ strtolower($promotion->nom) }} : -@prix($promotion->reduction)&nbsp;€</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($promotions->count())
    @error('promotions.*')
    <div class="alert alert-warning">{{ $message }}</div>
    @enderror
    <div class="alert alert-info">
        <ul class="mb-0">
            @foreach ($promotions as $promotion)
                <li>
                    {{ _('Offre') }} {{ strtolower($promotion->nom) }} : -@prix($promotion->reduction)&nbsp;€
                    <input type="hidden" name="promotions[{{ $promotion->id }}][id]" value="{{ $promotion->id }}">
                    <input type="hidden" name="promotions[{{ $promotion->id }}][nom]" value="{{ $promotion->nom }}">
                    <input type="hidden" name="promotions[{{ $promotion->id }}][reference]" value="{{ $promotion->reference }}">
                    <input type="hidden" name="promotions[{{ $promotion->id }}][reduction]" value="{{ $promotion->reduction }}">
                </li>
            @endforeach
        </ul>
    </div>
@else
    <input type="hidden" name="promotions" value="">
@endif
@if ($prestations)
    <div class="form-row">
        @foreach($prestations as $prestation)
            @php
                $presta = $reservation->prestations->getById($prestation->id);
                if (isset($devis) and $devis->getPrestations()->hasByPrestation($prestation)) {
                    $tarif = $devis->getPrestations()->getByPrestation($prestation)->getTarif();
                    $quantite = $devis->getPrestations()->getByPrestation($prestation)->getQuantite();
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
                    <div class="form-group col-4">
                        <label class="sr-only cursor-pointer" data-aire-component="label" for="presta-{{ $prestation->id }}">
                            Quantité
                        </label>
                        <select class="form-control " data-aire-component="select" name="prestations[{{ $prestation->id }}][quantite]" id="presta-{{ $prestation->id }}">
                            <option value="">0</option>
                            @for($i = 1; $i <= $prestation->quantite_max; $i++)
                                <option value="{{ $i }}" @selected(old('prestations.'.$prestation->id.'.quantite', $quantite) == $i)>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group col-8">
                        <label class="sr-only cursor-pointer" data-aire-component="label" for="presta-tarif-{{ $prestation->id }}">
                            Tarif €
                        </label>
                        <input class="form-control" type="number" name="prestations[{{ $prestation->id }}][tarif]" value="{{ old('prestations.'.$prestation->id.'.tarif', $tarif) }}" id="presta-tarif-{{ $prestation->id }}" step="0.01" placeholder="Tarif €">
                    </div>
                </div>
                <input type="hidden" name="prestations[{{ $prestation->id }}][id]" value="{{ $prestation->id }}">
                <input type="hidden" name="prestations[{{ $prestation->id }}][nom]" value="{{ $prestation->nom }}">
                <input type="hidden" name="prestations[{{ $prestation->id }}][tarification]" value="{{ $presta->tarification ?? $prestation->tarification }}">
                @error('prestations.'.$prestation->id)
                <div class="alert alert-warning">{{ $message }}</div>
                @enderror
            </div>
        @endforeach
    </div>
@endif

<div class="form-row">

    {{ Aire::number('caution', 'Caution (€)')->setAttribute('step', 0.01)->value(isset($devis) ? $reservation->categorie->caution : null)->groupAddClass('col-md-6') }}
    {{ Aire::number('franchise', 'Franchise (€)')->setAttribute('step', 0.01)->value(isset($devis) ? $reservation->categorie->franchise : null)->groupAddClass('col-md-6') }}

    {{ Aire::input('code_promo', 'Code promotion')->value(isset($devis) ? $reservation->code_promo : null)->groupAddClass('col-md-6') }}
    {{ Aire::number('remise', 'Remise (€)')->setAttribute('step', 0.01)->value(isset($devis) ? $devis->getRemiseAdmin() : null)->groupAddClass('col-md-6') }}
    {{ Aire::number('montant_base', 'Montant de base (€)')->setAttribute('step', 0.01)->value(isset($devis) ? $devis->getMontantBase() : null)->groupAddClass('col-md-6') }}
    {{ Aire::number('total', 'Total (€)')->setAttribute('step', 0.01)->value(isset($devis) ? $devis->getTotal() : null)->groupAddClass('col-md-6') }}

</div>