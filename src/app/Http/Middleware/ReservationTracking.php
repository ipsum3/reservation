<?php

namespace Ipsum\Reservation\app\Http\Middleware;

use Closure;
use Ipsum\Reservation\app\Models\Source\Source;

class ReservationTracking
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
        if( $request->origin ) {
            $source = Source::where( 'ref_tracking', $request->origin)->first();
            if ( $source ) {
                $request->session()->put( 'source_id', $source->id );
            }
        }
        return $next($request);
    }
}
