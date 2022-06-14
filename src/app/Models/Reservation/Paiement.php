<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Ipsum\Core\app\Models\BaseModel;

class Paiement extends BaseModel
{
    protected $table = 'paiements';

    protected $guarded = ['id'];



    /*
     * 
     * Relations
     */

    public function payable()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->belongsTo(Moyen::class);
    }
    
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }



    /*
     * Scopes
     */

    
    

    /*
     * Accessors & Mutators
     */

}
