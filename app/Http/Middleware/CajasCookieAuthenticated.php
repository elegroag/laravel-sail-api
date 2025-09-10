<?php

namespace App\Http\Middleware;

use App\Library\Auth\SessionCookies;
use Closure;
use Illuminate\Http\Request;

class CajasCookieAuthenticated
{
    /**
     * Manejar una solicitud entrante.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!SessionCookies::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.'
                ], 401);
            }
            return redirect('cajas/login');
        }

        $tipo = session()->has('tipo') ? session('tipo') : null;
        $user = session()->has('user') ? session('user') : null;

        if ($user && $user != null && $tipo && $tipo != null) {
            $request->attributes->set('cajas_user', $user);
            $request->attributes->set('cajas_tipo', $tipo);
        } else {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.'
                ], 401);
            }
            return redirect('cajas/login');
        }

        return $next($request);
    }
}
