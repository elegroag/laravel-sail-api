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
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! SessionCookies::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.',
                ], 401);
            }

            return redirect('cajas/login');
        }

        $tipo = session()->has('tipo') ? session('tipo') : null;
        if ($tipo) {
            // no es valido es usuario de mercurio no de cajas
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario de mercurio no autorizado para acceso a Sistema de Caja.',
                ], 401);
            }

            // redirigir a la pantalla de login de mercurio
            return redirect('web/login');
        }

        $tipfun = session()->has('tipfun') ? session('tipfun') : null;
        $user = session()->has('user') ? session('user') : null;

        if ($user && $user != null && $tipfun && $tipfun != null) {
            $request->attributes->set('user', $user);
            $request->attributes->set('tipfun', $tipfun);
        } else {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.',
                ], 401);
            }

            return redirect('cajas/login');
        }

        return $next($request);
    }
}
