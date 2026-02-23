<?php

namespace Ipsum\Reservation\app\Models\Dommage;

use Ipsum\Core\app\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ipsum\Media\Concerns\Mediable;
use Ipsum\Reservation\app\Models\Categorie\Vehicule;
use Ipsum\Reservation\app\Models\Inspection\Inspection;


/**
 * Ipsum\Reservation\app\Models\Dommage\Dommage
 *
 * @property int $id
 * @property int $vehicule_id
 * @property int|null $inspection_id
 * @property int $type_id
 * @property int $emplacement_id
 * @property int $element_id
 * @property string|null $observations
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Ipsum\Reservation\app\Models\Dommage\Element|null $element
 * @property-read \Ipsum\Reservation\app\Models\Dommage\Emplacement|null $emplacement
 * @property-read \Ipsum\Media\app\Models\Media|null $illustration
 * @property-read Inspection|null $inspection
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Media\app\Models\Media> $medias
 * @property-read int|null $medias_count
 * @property-read \Ipsum\Reservation\app\Models\Dommage\Type|null $type
 * @property-read Vehicule|null $vehicule
 * @method static \Illuminate\Database\Eloquent\Builder|Dommage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dommage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Dommage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Dommage query()
 * @method static \Illuminate\Database\Eloquent\Builder|Dommage withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Dommage withoutTrashed()
 * @mixin \Eloquent
 */
class Dommage extends BaseModel
{
    use SoftDeletes, Mediable;

    protected $guarded = ['id'];
    protected $mediable_delete = true;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function emplacement()
    {
        return $this->belongsTo(Emplacement::class, 'emplacement_id');
    }

    public function element()
    {
        return $this->belongsTo(Element::class, 'element_id');
    }

}
