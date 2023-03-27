<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Ipsum\Core\app\Models\BaseModel;


/**
 * Ipsum\Reservation\app\Models\Reservation\Type
 *
 * @property int $id
 * @property string $nom
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Reservation\Paiement> $paiements
 * @property-read int|null $paiements_count
 * @method static \Illuminate\Database\Eloquent\Builder|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type query()
 * @mixin \Eloquent
 */
class Type extends BaseModel
{
    protected $table = 'paiement_types';

    public $timestamps = false;


    protected $guarded = ['id'];



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


}
