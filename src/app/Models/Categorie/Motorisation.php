<?php

namespace Ipsum\Reservation\app\Models\Categorie;


use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Core\Concerns\Translatable;

/**
 * Ipsum\Reservation\app\Models\Categorie\Motorisation
 *
 * @property int $id
 * @property string|null $class
 * @property string $nom
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Categorie\Categorie[] $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|Motorisation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Motorisation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Motorisation query()
 * @mixin \Eloquent
 */
class Motorisation extends BaseModel
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
