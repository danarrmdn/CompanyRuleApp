<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && is_null(Auth::user()->password_change_at)) {
            if (! $request->routeIs('profile.settings') && ! $request->routeIs('password.update') && ! $request->routeIs('logout')) {
                return redirect()->route('profile.settings')->with('warning', 'You must change your password before continuing.');
            }
        }

        return $next($request);
    }
}
