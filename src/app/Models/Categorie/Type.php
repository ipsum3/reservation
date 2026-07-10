<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Admin\Concerns\Sortable;
use Ipsum\Core\Concerns\Slug;

/**
 * Ipsum\Reservation\app\Models\Categorie\Type
 *
 * @property int $id
 * @property string $nom
 * @property int $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Categorie\Categorie> $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type query()
 * @mixin \Eloquent
 */
class Type extends BaseModel
{
    use Slug, Sortable;

    protected $table = 'categorie_types';
    
    public $timestamps = false;

    protected $slugBase = 'nom';

    /*
     * Relations
     */

    public function categories()
    {
        return $this->hasMany(Categorie::class);
    }

    public function getTagTitleAttribute()
    {
        return $this->attributes['seo_title'] == '' ? $this->titre : $this->attributes['seo_title'];
    }

    public function getTagMetaDescriptionAttribute()
    {
        return $this->attributes['seo_description'] == '' ? $this->extrait : $this->attributes['seo_description'];
    }

}
