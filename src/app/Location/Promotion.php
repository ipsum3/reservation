<?php


namespace Ipsum\Reservation\app\Location;


use Illuminate\Database\Eloquent\Builder;

class Promotion extends \Ipsum\Reservation\app\Models\Promotion\Promotion
{

    protected ?float $reduction = null;



    /*
     * Scopes
     */

    public function scopeConditionScope(Builder $query, Devis $devis)
    {
        $debut_at = $devis->getLocation()->getDebutAt()->copy()->startOfDay();
        $fin_at = $devis->getLocation()->getFinAt()->copy()->startOfDay();
        $test = $devis->getPrestations()->pluck('id');

        $query->active()

            // Dates et durée réservation
            ->where('debut_at', '<=', $debut_at)
            ->where('fin_at', '>=', $fin_at)
            ->where(function (Builder $query) use ($debut_at, $fin_at) {
                $query->where('duree_min', '<=', $debut_at->diffInDays($fin_at))->orWhereNull('duree_min');
            })
            ->where(function (Builder $query) use ($debut_at, $fin_at) {
                $query->where('duree_max', '>=', $debut_at->diffInDays($fin_at))->orWhereNull('duree_max');
            })

            // Catégories
            ->where(function (Builder $query) use ($devis) {
                $query->whereHas('categories', function (Builder $query) use ($devis) {
                    $query->where('id', $devis->getLocation()->getCategorie()->id);
                })->orWhereDoesntHave('categories');
            })

            // Lieux
            ->where(function (Builder $query) use ($devis) {
                $query->whereHas('lieuxDebut', function (Builder $query) use ($devis) {
                    $query->where('id', $devis->getLocation()->getLieuDebut()->id);
                })->orWhereDoesntHave('lieuxDebut');
            })
            ->where(function (Builder $query) use ($devis) {
                $query->whereHas('lieuxFin', function (Builder $query) use ($devis) {
                    $query->where('id', $devis->getLocation()->getLieuFin()->id);
                })->orWhereDoesntHave('lieuxFin');
            })

            // Prestations
            ->where(function (Builder $query) use ($devis) {
                $query->whereHas('prestations', function (Builder $query) use ($devis) {
                    $query->whereIn('id', $devis->getPrestations()->pluck('id')->all());
                })->orWhereDoesntHave('prestations');
            })


            // Condition
            ->where(function (Builder $query) use ($devis) {
                $query->where('condition_paiement_id', $devis->getLocation()->getCondition()->id)->orWhereNull('condition_paiement_id');
            })

            // Code promo
            ->where(function (Builder $query) use ($devis) {
                $query->where('code', $devis->getLocation()->getCodePromo())->orWhereNull('code');
            })

            // Client
            ->where(function (Builder $query) {
                $query->where('client_id', auth()->check() ? auth()->id() : null)->orWhereNull('client_id');
            });
    }




    /*
     * Functions
     */

    public function calculerTotalReduction(Devis $devis): self
    {

        $reduction = $this->reduction_valeur;

        $promotion_categorie = $this->categories()->find($devis->getLocation()->getCategorie()->id);
        if ($promotion_categorie) {
            $reduction += $promotion_categorie->pivot->reduction;
        }

        // Pas de % pour les prestations : trop compliqué
        foreach ($devis->getPrestations() as $prestation) {
            $promotion_prestation = $this->prestations()->find($prestation->id);
            if ($promotion_prestation) {
                $reduction += $promotion_prestation->pivot->reduction;
            }
        }

        /*$promotion_lieu = $this->lieux()->find($devis->getLocation()->getLieuDebut()->id);
        if ($promotion_lieu) {
            $reduction += $promotion_lieu->pivot->reduction;
        }*/

        if ($this->reduction_type == 'pourcentage') {
            $this->reduction = ($reduction * $devis->getMontantBase()) / 100;
        } else {
            $this->reduction = $reduction;
        }

        return $this;
    }

    public function getReductionAttribute(): ?float
    {
        return $this->reduction;
    }
}