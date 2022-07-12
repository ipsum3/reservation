<?php

namespace Ipsum\Reservation\app\Models\Lieu;

use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Lieu\Type
 *
 * @property int $id
 * @property string $nom
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Lieu\Lieu[] $lieux
 * @property-read int|null $lieux_count
 * @method static \Illuminate\Database\Eloquent\Builder|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type query()
 * @mixin \Eloquent
 */
class Type extends BaseModel
{

    protected $table = 'lieu_types';

    public $timestamps = false;

    const AGENCE_ID = 1;
    const DEPOT_ID = 2;

    
    /*
     * Relations
     */

    public function lieux()
    {
        return $this->hasMany(Lieu::class);
    }


    /*
     * Scopes
     */




    /*
     * Accessors & Mutators
     */

}
