<div class="row">

    <div class="{{ $type->is_initial ? 'col-xl-6' : 'col-md-6' }}">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Informations locataire</div>
            <div class="card-body p-2">
                <strong>{{ $reservation->civilite }} {{ $reservation->prenom }} {{ $reservation->nom }}</strong><br/>
                {{ $reservation->adresse }}<br />
                {{ $reservation->cp }} {{ $reservation->ville }} {{ $reservation->pays_nom }}<br />
                {{ $reservation->telephone }}<br />
            </div>
        </div>

        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Conducteur{{ $reservation->conducteurs->count() ? 's' : '' }}</div>
            <div class="card-body p-2">
                <strong>{{ $reservation->prenom }} {{ $reservation->nom }}</strong><br/>
                @if ($reservation->naissance_at)
                    {{ _('Né le') }} {{ $reservation->naissance_at->format('d/m/Y') }}
                    @if ($reservation->naissance_lieu)
                        à {{ $reservation->naissance_lieu }}
                    @endif
                    <br>
                @endif
                @if ($reservation->permis_numero or $reservation->permis_at)
                    {{ _('Permis') }}
                    @if ($reservation->permis_numero)
                        {{ _('n°') }}{{ $reservation->permis_numero }}
                    @endif
                    @if ($reservation->permis_at)
                        {{ _('délivré le') }} {{ $reservation->permis_at->format('d/m/Y') }}
                    @endif
                    @if ($reservation->permis_delivre)
                        {{ _('par') }} {{ $reservation->permis_delivre }}
                    @endif
                @endif

                @foreach($reservation->conducteurs as $conducteur)
                    <br><br>
                    <strong>{{ $conducteur->prenom }} {{ $conducteur->nom }}</strong><br/>
                    @if ($conducteur->naissance_at)
                        {{ _('Né le') }} {{ $conducteur->naissance_at->format('d/m/Y') }}
                        @if ($conducteur->naissance_lieu)
                            à {{ $conducteur->naissance_lieu }}
                        @endif
                        <br>
                    @endif
                    @if ($conducteur->permis_numero or $conducteur->permis_at)
                        {{ _('Permis') }}
                        @if ($conducteur->permis_numero)
                            {{ _('n°') }}{{ $conducteur->permis_numero }}
                        @endif
                        @if ($conducteur->permis_at)
                            {{ _('délivré le') }} {{ $conducteur->permis_at->format('d/m/Y') }}
                        @endif
                        @if ($conducteur->permis_delivre)
                            {{ _('par') }} {{ $conducteur->permis_delivre }}
                        @endif
                    @endif
                @endforeach
            </div>
        </div>

        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Tarification</div>
            <div class="card-body p-2">
                <table class="table table-sm table-billing mb-0">
                    <tbody>
                    @if($reservation->prestations->count())
                        @foreach ($reservation->prestations as $prestation)
                            <tr>
                                <td>{{ $prestation->quantite }} x {{ $prestation->nom }} <small class="text-muted">{{ $prestation->choix }}</small></td>
                                <td class="text-right">{{ $prestation->tarif_libelle }}</td>
                            </tr>
                        @endforeach
                    @endif

                    @if($reservation->promotions->count())
                        @foreach ($reservation->promotions as $promotion)
                            <tr>
                                <td>Offre : {{ $promotion->nom }}</td>
                                <td class="text-right">-@prix($promotion->reduction)&nbsp;€</td>
                            </tr>
                        @endforeach
                    @endif
                    @if ($reservation->remise)
                        <tr>
                            <td>Remise</td>
                            <td class="text-right">-@prix($reservation->remise)&nbsp;€</td>
                        </tr>
                    @endif
                    @if ($reservation->caution || $reservation->paiementCaution?->montant)
                        <tr>
                            <td>
                                {{ _('Caution') }}

                                @if($reservation->paiementCaution)
                                    <span class="badge badge-success ml-1">Sécurisée (@prix($reservation->paiementCaution->montant) €)</span>
                                @elseif($reservation->caution_send_at)
                                    <span class="badge badge-info ml-1">Envoyée le {{ $reservation->caution_send_at->format('d/m/Y H:i') }}</span>
                                @endif

                            </td>
                            <td class="text-right">
                                @prix($reservation->caution)&nbsp;€
                            </td>
                        </tr>
                    @endif
                    @if ($reservation->franchise)
                        <tr>
                            <td>{{ _('Franchise') }}</td>
                            <td class="text-right">@prix($reservation->franchise)&nbsp;€ </td>
                        </tr>
                    @endif
                    <tr>
                        <td><strong>Total (TTC)</strong></td>
                        <td class="text-right">@prix($reservation->total)&nbsp;€</td>
                    </tr>
                    @if (!$reservation->is_payed)
                        <tr>
                            <td><strong>Reste à régler</strong></td>
                            <td class="text-right">@prix($reservation->total - $reservation->montant_paye)&nbsp;€</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="{{ $type->is_initial ? 'col-xl-6' : 'col-md-6' }}">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Véhicule</div>
            <div class="card-body p-2">
                <strong>{{ _('Catégorie') }} {{ $reservation->categorie_nom }}</strong><br>
                @if ($reservation->vehicule)
                    <strong>{{ _('Marque et modéle') }} :</strong> {{ $reservation->vehicule->marque_modele }}<br>
                    <strong>{{ _('Immatriculation') }} :</strong> {{ $reservation->vehicule->immatriculation }}<br>
                @endif
            </div>
        </div>

        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Période</div>
            <div class="card-body p-2">
                <strong>{{ _('Départ') }} :</strong>
                {{ $reservation->debut_lieu_nom }}
                {{ _('le') }} {{ $reservation->debut_at->format('d/m/Y') }} {{ _('à') }} {{ $reservation->debut_at->format('H\hi') }}
                <br>
                <strong>{{ _('Retour') }} :</strong>
                {{ $reservation->fin_lieu_nom }}
                {{ _('le') }} {{ $reservation->fin_at->format('d/m/Y') }} {{ _('à') }} {{ $reservation->fin_at->format('H\hi') }}
                <br><br>
                <strong>Nombre de jours :</strong> {{ $reservation->nb_jours }}
            </div>
        </div>
    </div>

    @if($inspection->locataire_signature && $inspection->agent_signature)
        <div class="col-md-4">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-secondary text-white p-1">Signatures</div>
                <div class="card-body p-2">
                    <div class="row" id="ajax-photos">
                        <div class="col-md-6">
                            <h3>Client</h3>
                            <img src="{{ $inspection->locataire_signature }}" alt="Signature agent" style="width:200px; height:auto; border:1px solid #000;">
                            <p class="mt-2">Signé le : {{ $inspection->locataire_signature_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h3>Loueur</h3>
                            <img src="{{ $inspection->agent_signature }}" alt="Signature agent" style="width:200px; height:auto; border:1px solid #000;">
                            <p class="mt-2">Signé le : {{ $inspection->agent_signature_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>