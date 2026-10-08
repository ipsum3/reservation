<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Enum\FactureEtat;
use Ipsum\Reservation\app\Enum\FactureType;
use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Reservation\Paiement;


/**
 * Ipsum\Reservation\app\Models\Reservation\Facture
 *
 * @property int $id
 * @property string|null $numero
 * @property int $reservation_id
 * @property int $client_id
 * @property FactureEtat $etat
 * @property FactureType $type
 * @property string|null $provider
 * @property string|null $provider_reference
 * @property string|null $url
 * @property \Illuminate\Support\Carbon|null $emission_at
 * @property \Illuminate\Support\Carbon $echeance_at
 * @property \Illuminate\Support\Carbon|null $send_at
 * @property string|null $total
 * @property string|null $montant_paye
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $is_brouillon
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Paiement> $paiements
 * @property-read int|null $paiements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Prestation> $produits
 * @property-read int|null $produits_count
 * @property-read \Ipsum\Reservation\app\Models\Reservation\Reservation|null $reservation
 * @method static \Illuminate\Database\Eloquent\Builder|Facture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Facture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Facture query()
 * @mixin \Eloquent
 */
class Facture extends BaseModel
{
    protected $table = 'factures';


    protected $guarded = ['id'];

    protected $casts = [
        'etat' => FactureEtat::class,
        'type' => FactureType::class,
        'emission_at' => 'datetime:Y-m-d',
        'echeance_at' => 'datetime:Y-m-d',
        'send_at' => 'datetime:Y-m-d\TH:i',
    ];



    protected static function booted()
    {
        static::deleting(function (self $facture) {
            $facture->paiements()->whereHas('reservation')->update(['facture_id' => null]);
            $facture->paiements()->delete();
            $facture->produits()->sync([]);
        });
    }



    /*
     *
     * Relations
     */

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function produits()
    {
        return $this->morphToMany(Prestation::class, 'prestable')->withPivot(['montant', 'quantite', 'description', 'remise']);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }



    /*
     * Scopes
     */



    /*
     * Functions
     */

    public function updateMontantPaye()
    {
        $this->montant_paye = $this->paiements()->sum('montant');
        return $this;
    }

    public function updateTotal()
    {
        $this->total = $this->produits->sum('pivot.montant') - $this->remise;
        return $this;
    }


    /*
     * Accessors & Mutators
     */

    public function getIsBrouillonAttribute()
    {
        return $this->etat === FactureEtat::BROUILLON;
    }
}
