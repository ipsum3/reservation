<?php

namespace Ipsum\Reservation\app\Models\Source;

use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

/**
 * Ipsum\Reservation\app\Models\Source\Source
 *
 * @property int $id
 * @property string $nom
 * @mixin \Eloquent
 */
class Source extends BaseModel
{

    protected $guarded = ['id'];
    
    public $timestamps = false;

    const SOURCE_SITE_INTERNET = 1;
    const SOURCE_AGENCE = 2;

    /*
     * Relations
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
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
