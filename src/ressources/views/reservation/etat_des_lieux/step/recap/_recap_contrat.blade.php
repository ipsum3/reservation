<div class="row">

    <div class="{{ $inspection->type->is_initial ? 'col-xl-6' : 'col-md-6' }}">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Informations locataire</div>
            <div class="card-body p-2">
                @if (!$inspection->reservation->entreprise)
                    <strong>{{ $inspection->reservation->civilite }} {{ $inspection->reservation->prenom }} {{ $inspection->reservation->nom }}</strong><br/>
                    {{ $inspection->reservation->adresse }}<br />
                    {{ $inspection->reservation->cp }} {{ $inspection->reservation->ville }} {{ $inspection->reservation->pays_nom }}<br />
                    {{ $inspection->reservation->telephone }}<br />
                @else
                    <strong>{{ $inspection->reservation->entreprise->nom }}</strong><br />
                    {{ $inspection->reservation->entreprise->adresse }}<br />
                    {{ $inspection->reservation->entreprise->cp }} {{ $inspection->reservation->entreprise->ville }} {{ $inspection->reservation->entreprise->pays?->nom }}<br />
                    {{ $inspection->reservation->entreprise->telephone }}<br />
                    {{ $inspection->reservation->entreprise->siren }}<br />
                @endif
            </div>
        </div>

        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Conducteur{{ $inspection->reservation->conducteurs->count() ? 's' : '' }}</div>
            <div class="card-body p-2">
                <strong>{{ $inspection->reservation->prenom }} {{ $inspection->reservation->nom }}</strong><br/>
                @if ($inspection->reservation->entreprise)
                    {{ $inspection->reservation->adresse }}<br />
                    {{ $inspection->reservation->cp }} {{ $inspection->reservation->ville }} {{ $inspection->reservation->pays_nom }}<br />
                    {{ $inspection->reservation->telephone }}<br />
                @endif
                @if ($inspection->reservation->naissance_at)
                    {{ _('Né le') }} {{ $inspection->reservation->naissance_at->format('d/m/Y') }}
                    @if ($inspection->reservation->naissance_lieu)
                        à {{ $inspection->reservation->naissance_lieu }}
                    @endif
                    <br>
                @endif
                @if ($inspection->reservation->permis_numero or $inspection->reservation->permis_at)
                    {{ _('Permis') }}
                    @if ($inspection->reservation->permis_numero)
                        {{ _('n°') }}{{ $inspection->reservation->permis_numero }}
                    @endif
                    @if ($inspection->reservation->permis_at)
                        {{ _('délivré le') }} {{ $inspection->reservation->permis_at->format('d/m/Y') }}
                    @endif
                    @if ($inspection->reservation->permis_delivre)
                        {{ _('par') }} {{ $inspection->reservation->permis_delivre }}
                    @endif
                @endif

                @foreach($inspection->reservation->conducteurs as $conducteur)
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
                    @if($inspection->reservation->prestations->count())
                        @foreach ($inspection->reservation->prestations as $prestation)
                            <tr>
                                <td>{{ $prestation->quantite }} x {{ $prestation->nom }} <small class="text-muted">{{ $prestation->choix }}</small></td>
                                <td class="text-right">{{ $prestation->tarif_libelle }}</td>
                            </tr>
                        @endforeach
                    @endif

                    @if($inspection->reservation->promotions->count())
                        @foreach ($inspection->reservation->promotions as $promotion)
                            <tr>
                                <td>Offre : {{ $promotion->nom }}</td>
                                <td class="text-right">-@prix($promotion->reduction)&nbsp;€</td>
                            </tr>
                        @endforeach
                    @endif
                    @if ($inspection->reservation->remise)
                        <tr>
                            <td>Remise</td>
                            <td class="text-right">-@prix($inspection->reservation->remise)&nbsp;€</td>
                        </tr>
                    @endif
                    @if ($inspection->reservation->caution || $inspection->reservation->paiementCaution?->montant)
                        <tr>
                            <td>
                                {{ _('Caution') }}

                                @if($inspection->reservation->paiementCaution)
                                    <span class="badge badge-success ml-1">Sécurisée (@prix($inspection->reservation->paiementCaution->montant) €)</span>
                                @elseif($inspection->reservation->caution_send_at)
                                    <span class="badge badge-info ml-1">Envoyée le {{ $inspection->reservation->caution_send_at->format('d/m/Y H:i') }}</span>
                                @endif

                            </td>
                            <td class="text-right">
                                @prix($inspection->reservation->caution)&nbsp;€
                            </td>
                        </tr>
                    @endif
                    @if ($inspection->reservation->franchise)
                        <tr>
                            <td>{{ _('Franchise') }}</td>
                            <td class="text-right">@prix($inspection->reservation->franchise)&nbsp;€ </td>
                        </tr>
                    @endif
                    <tr>
                        <td><strong>Total (TTC)</strong></td>
                        <td class="text-right">@prix($inspection->reservation->total)&nbsp;€</td>
                    </tr>
                    @if (!$inspection->reservation->is_payed)
                        <tr>
                            <td><strong>Reste à régler</strong></td>
                            <td class="text-right">@prix($inspection->reservation->total - $inspection->reservation->montant_paye)&nbsp;€</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="{{ $inspection->type->is_initial ? 'col-xl-6' : 'col-md-6' }}">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Véhicule</div>
            <div class="card-body p-2">
                <strong>{{ _('Catégorie') }} {{ $inspection->reservation->categorie_nom }}</strong><br>
                @if ($inspection->vehicule)
                    <strong>{{ _('Marque et modéle') }} :</strong> {{ $inspection->vehicule->marque_modele }}<br>
                @endif
                <strong>{{ _('Immatriculation') }} :</strong> {{ $inspection->immatriculation }}<br>
            </div>
        </div>

        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-secondary text-white p-1">Période</div>
            <div class="card-body p-2">
                <strong>{{ _('Départ') }} :</strong>
                {{ $inspection->reservation->debut_lieu_nom }}
                {{ _('le') }} {{ $inspection->reservation->debut_at->format('d/m/Y') }} {{ _('à') }} {{ $inspection->reservation->debut_at->format('H\hi') }}
                <br>
                <strong>{{ _('Retour') }} :</strong>
                {{ $inspection->reservation->fin_lieu_nom }}
                {{ _('le') }} {{ $inspection->reservation->fin_at->format('d/m/Y') }} {{ _('à') }} {{ $inspection->reservation->fin_at->format('H\hi') }}
                <br><br>
                <strong>Nombre de jours :</strong> {{ $inspection->reservation->nb_jours }}
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