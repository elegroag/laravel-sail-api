<?php

namespace App\Library\Auth;

class SessionCookies
{
    /**
     * authenticate function
     *
     * @return bool
     */
    public static function authenticate(SessionInterface $session, array $request)
    {
        $userData = $session->authenticate($request);

        if (! $userData) return false;

        session(['user' => $userData]);
        return true;
    }

    public static function destroyIdentity()
    {
        session()->forget(['user', 'tipo', 'coddoc', 'documento', 'nombre', 'email', 'id', 'tipfun', 'estado_afiliado']);
        session()->invalidate();
        session()->regenerateToken();
    }

    public static function user(): ?array
    {
        // Retornar identidad desde la sesión de Laravel
        if (! function_exists('session')) {
            return null;
        }
        if (! session()->has('user')) {
            return null;
        }
        $payload = (array) session('user');

        return [
            'valid' => true,
            'payload' => $payload,
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
