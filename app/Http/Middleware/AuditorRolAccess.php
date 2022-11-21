<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;



class AuditorRolAccess
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
        $user = Auth::user();

        if (!is_null($user) && $user->rol == User::ROL_AUDITOR) {
            return response()->json(['error' => Auth::user()], 401);
        }

        return $next($request);
    }
}
