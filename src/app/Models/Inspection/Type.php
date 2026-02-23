<?php

namespace Ipsum\Reservation\app\Models\Inspection;

use Ipsum\Core\app\Models\BaseModel;


/**
 * Ipsum\Reservation\app\Models\Inspection\Type
 *
 * @property int $id
 * @property string $nom
 * @property-read bool $is_final
 * @property-read bool $is_initial
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Inspection\Inspection> $inspections
 * @property-read int|null $inspections_count
 * @method static \Illuminate\Database\Eloquent\Builder|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type query()
 * @mixin \Eloquent
 */
class Type extends BaseModel
{

    protected $guarded = ['id'];
    protected $table = 'inspection_types';
    public $timestamps = false;

    const INITIAL_ID = 1;
    const FINAL_ID = 2;

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'type_id');
    }


    public function getIsInitialAttribute(): bool
    {
        return $this->id === self::INITIAL_ID;
    }

    public function getIsFinalAttribute(): bool
    {
        return $this->id === self::FINAL_ID;
    }

}
