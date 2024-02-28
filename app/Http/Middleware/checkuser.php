<?php

namespace App\Http\Middleware;

use Closure;

class checkuser
{
    /**
     * Handle an incoming request.
     x*
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->exists('role_id')) {
            if(\Request::route()->getName() != "client_summary"){
                // user value cannot be found in session
                return redirect('/');
            }
        }
        return $next($request);
    }
}
