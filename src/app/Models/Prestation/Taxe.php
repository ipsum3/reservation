<?php
namespace Ipsum\Reservation\app\Models\Prestation;

use Ipsum\Core\app\Models\BaseModel;


/**
 * Ipsum\Reservation\app\Models\Prestation\Taxe
 *
 * @property int $id
 * @property string $nom
 * @property string $taux
 * @property int $default
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Prestation\Prestation> $produits
 * @property-read int|null $produits_count
 * @method static \Illuminate\Database\Eloquent\Builder|Taxe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Taxe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Taxe query()
 * @mixin \Eloquent
 */
class Taxe extends BaseModel
{

    public $timestamps = false;

    protected $guarded = [];



    /*
     * Relations
     */

    public function produits()
    {
        return $this->hasMany(Prestation::class);
    }



    /*
     * Scopes
     */





    /*
     * Accessors & Mutators
     */



}
