<?php

namespace Ipsum\Reservation\app\Http\Middleware;

use Closure;

class RedirectIfInspectionEmailEmpty
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
        $inspection = $request->route('inspection');

        if (!$inspection->reservation->email) {
            \Alert::error("Email client non renseigné.")->flash();
            return redirect()->route('admin.reservation.edit', [$inspection->reservation]);
        }

        return $next($request);
    }
}
