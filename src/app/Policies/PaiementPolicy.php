<?php

namespace Ipsum\Reservation\app\Policies;


use Illuminate\Auth\Access\HandlesAuthorization;
use Ipsum\Admin\app\Models\Admin;
use Ipsum\Reservation\app\Models\Reservation\Paiement;

class PaiementPolicy
{
    use HandlesAuthorization;

    public function before(Admin $user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }


    public function delete(Admin $user, Paiement $model)
    {
        return false;
    }


}
