<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Ipsum\Admin\app\Casts\AsCustomFieldsObject;
use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Categorie\InterventionType
 *
 * @property int $id
 * @property string $nom
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Categorie\Intervention> $interventions
 * @property-read int|null $interventions_count
 * @method static \Illuminate\Database\Eloquent\Builder|InterventionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InterventionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InterventionType query()
 * @mixin \Eloquent
 */
class InterventionType extends BaseModel
{

    protected $table = 'intervention_types';

    protected $guarded = ['id'];

    public $timestamps = false;




    /*
     * Relations
     */

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}
