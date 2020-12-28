<?php

namespace App\Http\Middleware;

use Closure;

class HostOnly
{
    public function handle($request, Closure $next)
    {
        $role = auth()->user()->role;
        if($role <= 50) {
            return $next($request);
        }
        abort(403, '権限がありません。');
    }
}
