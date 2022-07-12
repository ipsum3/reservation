<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use App\Article\Translatable;
use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Reservation\Pays
 *
 * @property int $id
 * @property int $code
 * @property string $alpha2
 * @property string $alpha3
 * @property string $nom
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Reservation\Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Pays newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pays newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pays query()
 * @mixin \Eloquent
 */
class Pays extends BaseModel
{

    protected $table = 'pays';

    public $timestamps = false;




    /*
     *
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
