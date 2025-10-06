<?php

namespace App\Library\Auth;

use App\Services\Srequest;

class SessionCookies
{


    /**
     * authenticate function
     * @return bool
     */
    public static function authenticate(string $useApp = '', Srequest $request)
    {
        switch ($useApp) {
            case 'mercurio':
                $session = new SessionMercurio();
                break;
            case 'cajas':
                $session = new SessionCajas();
                break;
            default:
                return false;
                break;
        }

        session()->regenerate();
        $userData = $session->authenticate($request);

        if (!$userData) {
            return false;
        }

        session()->put('user', $userData);
        return true;
    }

    public static function destroyIdentity()
    {
        session()->forget(['user', 'tipo', 'coddoc', 'documento', 'nombre', 'email', 'id']);
        session()->invalidate();
        session()->regenerateToken();
    }

    public static function user(): ?array
    {
        // Retornar identidad desde la sesión de Laravel
        if (!function_exists('session')) {
            return null;
        }
        if (!session()->has('user')) {
            return null;
        }
        $payload = (array) session('user');

        return [
            'valid' => true,
            'payload' => $payload
        ];
    }

    /**
     * Retorna true si existe una identidad válida en la cookie
     */
    public static function check(): bool
    {
        return function_exists('session') ? session()->has('user') : false;
    }
}
