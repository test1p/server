<?php

namespace App\Http\Middleware;

use Closure;

class AdminOnly
{
    public function handle($request, Closure $next)
    {
        $role = auth()->user()->role;
        if($role === 0) {
            return $next($request);
        }
        abort(403, '権限がありません。');
    }
}