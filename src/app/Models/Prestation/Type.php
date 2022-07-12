<?php

namespace Ipsum\Reservation\app\Models\Prestation;

use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Prestation\Type
 *
 * @property int $id
 * @property string $nom
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Prestation\Prestation[] $prestations
 * @property-read int|null $prestations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type query()
 * @mixin \Eloquent
 */
class Type extends BaseModel
{

    protected $table = 'prestation_types';

    public $timestamps = false;

    const OPTION_ID = 1;
    const ASSURANCE_ID = 2;
    const FRAIS_ID = 3;


    public function prestations()
    {
        return $this->hasMany(Prestation::class);
    }
}
