<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Si APP_URL es https, redirige peticiones inseguras a HTTPS.
 * Alineado con el entorno Docker (9043→443) y Smart Checkout ePayco.
 */
class ForceHttpsFromAppUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $appUrl = (string) config('app.url', '');
        $scheme = parse_url($appUrl, PHP_URL_SCHEME);

        if ($scheme !== 'https' || $request->secure()) {
            return $next($request);
        }

        $host = parse_url($appUrl, PHP_URL_HOST) ?: $request->getHost();
        $port = parse_url($appUrl, PHP_URL_PORT);
        $authority = $host . ($port ? ':' . $port : '');

        return redirect()->away(
            'https://' . $authority . $request->getRequestUri(),
            301
        );
    }
}
