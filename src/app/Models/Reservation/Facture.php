<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Enum\FactureType;


/**
 * Ipsum\Reservation\app\Models\Reservation\Facture
 *
 * @property int $id
 * @property string $numero
 * @property int $reservation_id
 * @property FactureType $type
 * @property string $provider
 * @property string $provider_reference
 * @property string|null $url
 * @property string|null $send_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Reservation\Paiement> $paiements
 * @property-read int|null $paiements_count
 * @property-read \Ipsum\Reservation\app\Models\Reservation\Reservation|null $reservation
 * @method static \Illuminate\Database\Eloquent\Builder|Facture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Facture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Facture query()
 * @mixin \Eloquent
 */
class Facture extends BaseModel
{
    protected $table = 'factures';


    protected $guarded = ['id'];

    protected $casts = [
        'type' => FactureType::class,
    ];



    /*
     * 
     * Relations
     */

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
    
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }



    /*
     * Scopes
     */




    /*
     * Accessors & Mutators
     */


}
