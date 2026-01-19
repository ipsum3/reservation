<?php

namespace Ipsum\Reservation\app\Http\Middleware;

use Closure;

class CheckEtatDesLieuxEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        if (config('ipsum.reservation.etat_des_lieux.enable') !== true) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }

}
