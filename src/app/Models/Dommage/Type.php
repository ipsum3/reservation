<?php

namespace Ipsum\Reservation\app\Models\Dommage;

use Ipsum\Core\app\Models\BaseModel;


/**
 * Ipsum\Reservation\app\Models\Dommage\Type
 *
 * @property int $id
 * @property string $nom
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Dommage\Dommage> $dommages
 * @property-read int|null $dommages_count
 * @method static \Illuminate\Database\Eloquent\Builder|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type query()
 * @mixin \Eloquent
 */
class Type extends BaseModel
{

    protected $guarded = ['id'];
    protected $table = 'dommage_types';
    public $timestamps = false;

    public function dommages()
    {
        return $this->hasMany(Dommage::class, 'type_id');
    }

}
