<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;



class TempAdmin
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

        if ($user->rol == User::ROL_ADMIN_PERISHABLE && !$this->verifyDateRange($user->start, $user->end, date('Y-m-d'))) {
            return response()->json(['error' => Auth::user()], 401);
        }

        return $next($request);
    }

    private function verifyDateRange($date_inicio, $date_fin, $date_nueva) {
        $date_inicio = strtotime($date_inicio);
        $date_fin = strtotime($date_fin);
        $date_nueva = strtotime($date_nueva);
        if (($date_nueva >= $date_inicio) && ($date_nueva <= $date_fin))
            return true;
        return false;
     }
}
