<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;


class EnabledUser
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
        $user = Auth::user();

        if (! $user->enabled) {
            return response()->json(['error' => 'Su usuario se encuentra desactivado'], 401);
        }

        if (is_null($user->company_id) && Auth::user()->rol != User::ROL_OWNER) {
            return response()->json(['error' => 'No se encuentra asociado a una empresa'], 401);
        }

        return $next($request);
    }
}
