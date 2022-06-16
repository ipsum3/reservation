<?php

namespace Ipsum\Reservation\app\Http\Middleware;

use Closure;

class ReservationConfirmed
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
        $reservation = $request->route('reservation');
        if (!$reservation->is_confirmed) {
            abort('404');
        }

        return $next($request);
    }
}
