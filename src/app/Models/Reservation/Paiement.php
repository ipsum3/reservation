<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Reservation\Paiement
 *
 * @property int $id
 * @property int|null $reservation_id
 * @property int $paiement_moyen_id
 * @property string $montant
 * @property string|null $transaction_ref
 * @property string|null $autorisation_ref
 * @property string|null $erreur
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $is_o_k
 * @property-read \Ipsum\Reservation\app\Models\Reservation\Moyen|null $moyen
 * @property-read \Ipsum\Reservation\app\Models\Reservation\Reservation|null $reservation
 * @method static \Illuminate\Database\Eloquent\Builder|Paiement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paiement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paiement ok()
 * @method static \Illuminate\Database\Eloquent\Builder|Paiement query()
 * @mixin \Eloquent
 */
class Paiement extends BaseModel
{
    protected $table = 'paiements';

    protected $guarded = ['id'];



    protected static function booted()
    {
        self::created(function (self $paiement) {
            $reservation = $paiement->reservation;
            $reservation->montant_paye = $reservation->paiements()->sum('montant');
            $reservation->save();
        });
    }



    /*
     * 
     * Relations
     */

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function moyen()
    {
        return $this->belongsTo(Moyen::class, 'paiement_moyen_id');
    }



    /*
     * Scopes
     */

    public function scopeOk($query)
    {
        return $query->whereNull('erreur');
    }
    

    /*
     * Accessors & Mutators
     */

    public function getIsOKAttribute()
    {
        return $this->erreur !== null;
    }
}
