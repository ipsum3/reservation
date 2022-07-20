<?php

namespace Ipsum\Reservation\app\Policies;


use Illuminate\Auth\Access\HandlesAuthorization;
use Ipsum\Admin\app\Models\Admin;
use Ipsum\Reservation\app\Models\Reservation\Reservation;

class ReservationPolicy
{
    use HandlesAuthorization;

    public function before(Admin $user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    public function viewAny(Admin $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function create(Admin $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function update(Admin $user, Reservation $model)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function delete(Admin $user, Reservation $model)
    {
        return false;
    }


}
