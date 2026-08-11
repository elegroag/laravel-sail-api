<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Corta el acceso al portal de usuarios finales (Mercurio) y a las APIs
 * cuando config('app.maintenance_endusers') está activo.
 *
 * - /mercurio/*: renderiza la página Inertia Auth/FueraServicio con 503.
 * - /api/*     : responde JSON 503 con header Retry-After.
 * - Cajas (sesión con tipfun) bypasea automáticamente.
 * - /web/*, /cajas/* y todo lo demás pasa sin cambios.
 */
class EnsureEndUserAvailable
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.maintenance_endusers')) {
            return $next($request);
        }

        // Auto-bypass para administradores de Cajas ya autenticados.
        if (session()->has('tipfun') && session('tipfun')) {
            return $next($request);
        }

        $path = $request->path();
        $isMercurio = str_starts_with($path, 'mercurio');
        $isApi = str_starts_with($path, 'api');

        if (! $isMercurio && ! $isApi) {
            return $next($request);
        }

        if ($request->expectsJson() || $isApi) {
            return response()->json([
                'success' => false,
                'message' => 'La plataforma se encuentra en mantenimiento. Intente más tarde.',
                'maintenance' => true,
            ], 503)->header('Retry-After', '900');
        }

        return Inertia::render('Auth/FueraServicio', [
            'msj' => config('app.maintenance_endusers_message'),
        ])->toResponse($request)
            ->setStatusCode(503)
            ->header('Cache-Control', 'no-store');
    }
}
