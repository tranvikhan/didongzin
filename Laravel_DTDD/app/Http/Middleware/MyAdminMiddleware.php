<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class MyAdminMiddleware
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
        if( Auth::check() )
        {
            if( Auth::user()->Tai_khoan_admin == 1)
                return $next($request);    
            else
                return redirect('TrangChu');
        }
        else
        {
            return redirect('TrangChu');
        }
    }
}
