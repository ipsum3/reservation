<?php

namespace Ipsum\Reservation\app\Models\Inspection;

use Ipsum\Core\app\Models\BaseModel;


/**
 * Ipsum\Reservation\app\Models\Inspection\Checklist
 *
 * @property int $id
 * @property string $nom
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Inspection\Inspection> $inspections
 * @property-read int|null $inspections_count
 * @method static \Illuminate\Database\Eloquent\Builder|Checklist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Checklist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Checklist query()
 * @mixin \Eloquent
 */
class Checklist extends BaseModel
{

    protected $guarded = ['id'];
    protected $table = 'checklist';
    public $timestamps = false;

    public function inspections()
    {
        return $this->belongsToMany(Inspection::class, 'checklist-inspections')
            ->withPivot('value');
    }

}
