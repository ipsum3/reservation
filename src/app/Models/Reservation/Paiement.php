<?php

namespace Ipsum\Reservation\app\Models\Reservation;

use Ipsum\Core\Models\BaseModel;

class Paiement extends BaseModel
{
    protected $table = 'paiements';
    
    protected $fillable=['paiement_type_id', 'montant', 'devise', 'transaction_ref', 'autorisation_ref', 'erreur', 'reservation_id','notification_id'];

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

}
