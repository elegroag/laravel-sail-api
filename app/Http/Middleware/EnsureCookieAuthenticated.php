<?php

namespace App\Http\Middleware;

use App\Library\Auth\SessionCookies;
use Closure;
use Illuminate\Http\Request;

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
            // Si es API/JSON devolvemos 401, en caso contrario redirigimos a login
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.'
                ], 401);
            }
            return redirect('login/index');
        }

        // Opcional: podemos inyectar el usuario validado en el request
        $identity = SessionCookies::user();
        if ($identity && isset($identity['user'])) {
            // No sobreescribimos auth() de Laravel; dejamos un atributo para consumo interno
            $request->attributes->set('mercurio_user', $identity['user']);
            $request->attributes->set('mercurio_identity', $identity['payload']);
        }

        return $next($request);
    }
}
