<?php

namespace Ipsum\Reservation\app\Models\Prestation;

use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Prestation\Tarification
 *
 * @property int $id
 * @property string $nom
 * @property string|null $class
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Prestation\Prestation> $prestations
 * @property-read int|null $prestations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Tarification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tarification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tarification query()
 * @mixin \Eloquent
 */
class Tarification extends BaseModel
{
    protected $table = 'prestation_tarifications';
    protected $guarded = ['id'];
    public $timestamps = false;

    const JOUR_ID = 1;
    const FORFAIT_ID = 2;
    const AGENCE_ID = 3;
    const CARBURANT_ID = 4;


    public function prestations()
    {
        return $this->hasMany(Prestation::class, 'tarification_id');
    }

}
