<?php

namespace Ipsum\Reservation\app\Models\Source;

use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Source\Source
 *
 * @property int $id
 * @property string $nom
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
