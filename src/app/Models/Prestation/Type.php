<?php

namespace Ipsum\Reservation\app\Models\Prestation;

use Illuminate\Database\Eloquent\Builder;
use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Prestation\Type
 *
 * @property int $id
 * @property string $nom
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Prestation\Prestation> $prestations
 * @property-read int|null $prestations_count
 * @method static Builder|Type newModelQuery()
 * @method static Builder|Type newQuery()
 * @method static Builder|Type prestas()
 * @method static Builder|Type query()
 * @mixin \Eloquent
 */
class Type extends BaseModel
{

    protected $table = 'prestation_types';

    public $timestamps = false;

    const OPTION_ID = 1;
    const ASSURANCE_ID = 2;
    const FRAIS_ID = 3;
    const LOCATION_ID = 4;
    const RESTITUTION_ID = 5;

    const PRESTATION_IDS = [self::OPTION_ID, self::ASSURANCE_ID, self::FRAIS_ID];




    /*
     * Relations
     */

    public function prestations()
    {
        return $this->hasMany(Prestation::class);
    }



    /*
     * Scopes
     */

    public function scopePrestas(Builder $query)
    {
        return $query->whereIn('id', self::PRESTATION_IDS);
    }


}
