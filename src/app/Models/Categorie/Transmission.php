<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Core\Concerns\Translatable;

/**
 * Ipsum\Reservation\app\Models\Categorie\Transmission
 *
 * @property int $id
 * @property string|null $class
 * @property string $nom
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Categorie\Categorie> $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Core\app\Models\Translate> $translates
 * @property-read int|null $translates_count
 * @method static \Illuminate\Database\Eloquent\Builder|Transmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transmission query()
 * @mixin \Eloquent
 */
class Transmission extends BaseModel
{
    use Translatable;

    public $timestamps = false;

    protected $translatable_attributes = ['nom'];


    /*
     * Relations
     */

    public function categories()
    {
        return $this->hasMany(Categorie::class);
    }
}
