<?php

namespace Ipsum\Reservation\app\Policies;


use Illuminate\Auth\Access\HandlesAuthorization;
use Ipsum\Admin\app\Models\Admin;

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

    public function update(Admin $user, Admin $model)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function delete(Admin $user, Admin $model)
    {
        return false;
    }


}
