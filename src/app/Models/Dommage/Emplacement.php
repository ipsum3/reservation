<?php

namespace Ipsum\Reservation\app\Models\Dommage;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Models\Dommage\Dommage;


/**
 * Ipsum\Reservation\app\Models\Dommage\Emplacement
 *
 * @property int $id
 * @property string $nom
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Dommage> $dommages
 * @property-read int|null $dommages_count
 * @method static \Illuminate\Database\Eloquent\Builder|Emplacement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Emplacement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Emplacement query()
 * @mixin \Eloquent
 */
class Emplacement extends BaseModel
{

    protected $guarded = ['id'];
    protected $table = 'dommage_emplacements';
    public $timestamps = false;

    public function dommages()
    {
        return $this->hasMany(Dommage::class, 'element_id');
    }

}
