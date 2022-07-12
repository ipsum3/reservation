<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Reservation\Etat
 *
 * @property int $id
 * @property string $nom
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Reservation\Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Etat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Etat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Etat query()
 * @mixin \Eloquent
 */
class Etat extends BaseModel
{

    protected $table = 'reservation_etats';

    public $timestamps = false;

    const NON_VALIDEE_ID = 1;
    const VALIDEE_ID = 2;
    const ANNULEE_ID = 3;


    /*
     * Relations
     */

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }



    /*
     * Scopes
     */




    /*
     * Accessors & Mutators
     */

}
