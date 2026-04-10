<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\ApiDocumentationAuth;
use App\Http\Middleware\CajasCookieAuthenticated;
use App\Http\Middleware\EnsureCookieAuthenticated;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        api: __DIR__ . '/../routes/api.php'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);
        $middleware->validateCsrfTokens(except: [
            'web/*',
            'mercurio/*',
            'cajas/*',
            'api/sanctum/csrf-cookie'
        ]);

        $middleware->web(append: [
            HandleCors::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        $middleware->api(append: [
            SubstituteBindings::class,
        ]);

        // Middleware para documentación de API
        $middleware->alias([
            'api.docs.auth' => ApiDocumentationAuth::class,
            'mercurio.auth' => EnsureCookieAuthenticated::class,
            'cajas.auth' => CajasCookieAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            $ruta = $request->path();

            // Detectar si es ruta de cajas
            if (str_starts_with($ruta, 'cajas')) {
                return redirect('cajas/login');
            }

            // Detectar si es ruta de web/mercurio
            if (str_starts_with($ruta, 'web') || str_starts_with($ruta, 'mercurio')) {
                return redirect('web/login');
            }

            // Vista personalizada 404
            return response()->view('errors.web-unavailable', ['ruta' => $ruta], 404);
        });
    })->create();
