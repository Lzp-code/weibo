<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    //UsersController里面的$this->middleware('guest',['only'=>['create']])方法的判断;
    public function handle($request, Closure $next, $guard = null)
    {

        if (Auth::guard($guard)->check()) {
            session()->flash('info','您已登录，无需再次操作。');
            return redirect('/');
        }

        return $next($request);
    }
}
