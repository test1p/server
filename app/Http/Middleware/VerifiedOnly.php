<?php

namespace App\Http\Middleware;

use Closure;

class VerifiedOnly
{
    public function handle($request, Closure $next)
    {
        $email_verified_at = auth()->user()->email_verified_at;
        if($email_verified_at) {
            return $next($request);
        }
        abort(403, 'メールアドレス認証が完了していません。');
    }
}
