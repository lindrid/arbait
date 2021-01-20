<?php

namespace App\Http\Middleware;

use Closure;

class MyAuthMiddleware
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
        if (is_null(session('user_id'))) {
            return response(['status' => 'Вы должны залогиниться'], 401);
        }
        return $next($request);
    }
}
