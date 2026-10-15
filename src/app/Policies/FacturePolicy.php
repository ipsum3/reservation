<?php

namespace Ipsum\Reservation\app\Policies;


use Illuminate\Auth\Access\HandlesAuthorization;
use Ipsum\Admin\app\Models\Admin;
use Ipsum\Reservation\app\Models\Reservation\Facture;

class FacturePolicy
{
    use HandlesAuthorization;



    public function delete(Admin $user, Facture $facture)
    {
        return $facture->is_brouillon;
    }


}
