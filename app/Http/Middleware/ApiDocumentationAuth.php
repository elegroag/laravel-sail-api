<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiDocumentationAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // En producción, requerir autenticación para ver la documentación
        if (app()->environment('production')) {
            // Puedes implementar tu lógica de autenticación aquí
            // Por ejemplo, verificar si el usuario está autenticado
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('error', 'Debe iniciar sesión para acceder a la documentación de la API');
            }
        }

        return $next($request);
    }
}
