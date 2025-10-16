<?php

namespace Ipsum\Reservation\app\Http\Middleware;

use Closure;

class ReservationEmail
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
        $type = $request->route('type');

        if (!$reservation->email) {
            \Alert::error("Email client non renseigné.")->flash();
            return redirect()->route('admin.inspection.client', [$reservation, $type]);
        }

        return $next($request);
    }
}
