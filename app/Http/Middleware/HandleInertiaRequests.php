<?php

namespace App\Http\Middleware;

use App\Services\Menu\MenuCajas;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        if ($request->is('mercurio/*')) {
            $authUser = $request->user();
        } elseif ($request->is('cajas/*')) {
            $sessionUser = $request->session()->get('user');
            $authUser = $sessionUser ? (object) [
                'id' => $sessionUser['id'] ?? null,
                'name' => trim(($sessionUser['nombre'] ?? '').' '.($sessionUser['apellido'] ?? '')) ?: ($sessionUser['name'] ?? 'Usuario'),
                'email' => $sessionUser['email'] ?? null,
                'avatar' => null,
            ] : null;
        } else {
            $authUser = $request->user();
        }

        if ($request->is('mercurio/*') || $request->is('cajas/*')) {
            $shared = [
                ...parent::share($request),
                'auth' => [
                    'user' => $authUser,
                ],
                'ziggy' => fn (): array => [
                    ...(new Ziggy)->toArray(),
                    'location' => $request->url(),
                ],
                'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
                'flash' => [
                    'success' => fn () => $request->session()->get('success'),
                    'error' => fn () => $request->session()->get('error'),
                ],
            ];

            if ($request->is('cajas/*')) {
                $shared['cajasMenu'] = fn () => MenuCajas::getTree('CA');
            }

            return $shared;
        }

        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $authUser,
            ],
            'ziggy' => fn (): array => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
