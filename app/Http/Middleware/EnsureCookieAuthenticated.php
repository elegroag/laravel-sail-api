<?php

namespace App\Http\Middleware;

use App\Library\Auth\SessionCookies;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class EnsureCookieAuthenticated
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
            return redirect('web/login');
        }

        $tipo = session()->has('tipo') ? session('tipo') : null;
        $user = session()->has('user') ? session('user') : null;

        if ($user && $user != null && $tipo && $tipo != null) {
            $request->attributes->set('mercurio_user', $user);
            $request->attributes->set('mercurio_tipo', $tipo);
        } else {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.'
                ], 401);
            }
            return redirect('web/login');
        }

        return $next($request);
    }

    public function actionActive()
    {
        $route = Route::current(); // Illuminate\Routing\Route
        $name = Route::currentRouteName(); // string
        $action = Route::currentRouteAction(); // string
        return [
            'route' => $route,
            'name' => $name,
            'action' => $action
        ];
    }
}
