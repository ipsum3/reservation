<?php

namespace Ipsum\Reservation\app\Models\Dommage;
use Ipsum\Core\app\Models\BaseModel;


/**
 * Ipsum\Reservation\app\Models\Dommage\Element
 *
 * @property int $id
 * @property string $nom
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Dommage\Dommage> $dommages
 * @property-read int|null $dommages_count
 * @method static \Illuminate\Database\Eloquent\Builder|Element newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Element newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Element query()
 * @mixin \Eloquent
 */
class Element extends BaseModel
{

    protected $guarded = ['id'];
    protected $table = 'dommage_elements';
    public $timestamps = false;

    public function dommages()
    {
        return $this->hasMany(Dommage::class, 'element_id');
    }

}
