<?php

namespace Ipsum\Reservation\app\Models\Source;

use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Source\Source
 *
 * @property int $id
 * @property string $nom
 * @property string $icon
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Source\Source> $sources
 * @property-read int|null $sources_count
 * @method static \Illuminate\Database\Eloquent\Builder|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Type query()
 * @mixin \Eloquent
 */
class Type extends BaseModel
{
    protected $table = 'source_types';

    protected $guarded = ['id'];
    
    public $timestamps = false;

    /*
     * Relations
     */
    public function sources()
    {
        return $this->hasMany(Source::class);
    }

    /*
     * Scopes
     */

    /*
     * Accessors & Mutators
     */

    /*
     * Functions
     */

}
