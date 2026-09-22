<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectWwwHost
{
    private const WWW_HOST = 'www.comfacaenlinea.com.co';

    private const CANONICAL_HOST = 'comfacaenlinea.com.co';

    /**
     * Redirect www.* to apex host outside local environment.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.env') === 'local') {
            return $next($request);
        }

        $host = strtolower($request->getHost());

        if ($host !== self::WWW_HOST) {
            return $next($request);
        }

        $target = 'https://'.self::CANONICAL_HOST.$request->getRequestUri();

        return redirect()->away($target, 301);
    }
}
