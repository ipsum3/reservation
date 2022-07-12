<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Reservation\Moyen
 *
 * @property int $id
 * @property string $nom
 * @property-read mixed $is_site_cb
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Reservation\Paiement[] $paiements
 * @property-read int|null $paiements_count
 * @method static \Illuminate\Database\Eloquent\Builder|Moyen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Moyen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Moyen query()
 * @mixin \Eloquent
 */
class Moyen extends BaseModel
{
    protected $table = 'paiement_moyens';

    public $timestamps = false;

    const CB_SITE_ID = 1;



    /*
     * 
     * Relations
     */
    
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }



    /*
     * Scopes
     */




    /*
     * Accessors & Mutators
     */

    public function getIsSiteCbAttribute()
    {
        return $this->id == self::CB_SITE_ID;
    }
}
