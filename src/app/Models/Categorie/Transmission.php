<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Categorie\Transmission
 *
 * @property int $id
 * @property string|null $class
 * @property string $nom
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Categorie\Categorie[] $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|Transmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transmission query()
 * @mixin \Eloquent
 */
class Transmission extends BaseModel
{


    public $timestamps = false;



    /*
     * Relations
     */

    public function categories()
    {
        return $this->hasMany(Categorie::class);
    }
}
