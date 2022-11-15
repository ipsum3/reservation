<?php

namespace Ipsum\Reservation\app\Models\Tarif;

use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Tarif\Jour
 *
 * @property-read \Ipsum\Reservation\app\Models\Tarif\Duree|null $durees
 * @method static \Illuminate\Database\Eloquent\Builder|Jour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Jour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Jour query()
 * @mixin \Eloquent
 */
class Jour extends BaseModel
{

    protected $guarded = ['id'];

    protected $table = 'duree_jours';

    const VALEURS = [1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi', 0 => 'dimanche'];


    public $timestamps = false;






    /*
     * Relations
     */

    public function durees()
    {
        return $this->belongsTo(Duree::class);
    }



    /*
     * Scopes
     */




    /*
     * Functions
     */




    /*
     * Accessors & Mutators
     */

    public function getHeureAttribute()
    {
        return (isset($this->attributes['heure']) and $this->attributes['heure'] !== null) ? substr($this->attributes['heure'], 0, -3) : null;
    }

}
