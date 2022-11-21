<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Ipsum\Admin\Concerns\Sortable;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Core\Concerns\Translatable;

/**
 * Ipsum\Reservation\app\Models\Categorie\Carrosserie
 *
 * @property int $id
 * @property string|null $class
 * @property string $nom
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Categorie\Categorie[] $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|Carrosserie filtreSortable($objet)
 * @method static \Illuminate\Database\Eloquent\Builder|Carrosserie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Carrosserie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Carrosserie query()
 * @mixin \Eloquent
 */
class Carrosserie extends BaseModel
{
    use Sortable, Translatable;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $translatable_attributes = ['nom'];


    /*
     * Relations
     */

    public function categories()
    {
        return $this->hasMany(Categorie::class);
    }
}
