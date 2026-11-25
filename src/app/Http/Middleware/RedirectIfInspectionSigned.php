<?php

namespace Ipsum\Reservation\app\Http\Middleware;

use Closure;
use Ipsum\Reservation\app\Models\Inspection\Type;

class RedirectIfInspectionSigned
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

        if ($inspection && $inspection->isSigned()) {
            return redirect()->route('admin.inspection.show', [$inspection]);
        }

        return $next($request);
    }
}
