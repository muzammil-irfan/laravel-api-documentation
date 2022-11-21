<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;



class CompanyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $userRol = Auth::user()->rol;

        if (! in_array($userRol, User::getCompanyRoles())) {
            return response()->json(['error' => Auth::user()], 401);
        }

        return $next($request);
    }
}
