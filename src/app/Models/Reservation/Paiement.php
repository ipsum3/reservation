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

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function moyen()
    {
        return $this->belongsTo(Moyen::class, 'paiement_moyen_id');
    }



    /*
     * Scopes
     */

    public function scopeOk($query)
    {
        return $query->whereNull('erreur');
    }
    

    /*
     * Accessors & Mutators
     */

    public function getIsOKAttribute()
    {
        return $this->erreur !== null;
    }
}
