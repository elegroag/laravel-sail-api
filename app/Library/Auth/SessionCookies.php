<?php

namespace App\Library\Auth;

use App\Models\Adapter\DbBase;

class SessionCookies
{
    private $model, $tipo, $coddoc, $documento, $estado;
    private $db;

    public function __construct(...$params)
    {
        $arguments = get_params_destructures($params);
        // Aceptar tanto 'model' como 'class' para compatibilidad
        $this->model = $arguments['model'] ?? $arguments['class'] ?? null;
        $this->tipo = $arguments['tipo'];
        $this->coddoc = $arguments['coddoc'];
        $this->documento = $arguments['documento'];
        $this->estado = $arguments['estado'];
        $this->db = DbBase::rawConnect();
    }

    /**
     * authenticate function
     * @return bool
     */
    public function authenticate()
    {
        // Validaciones básicas de parámetros
        if (empty($this->model) || empty($this->tipo) || empty($this->coddoc) || empty($this->documento)) {
            return false;
        }


        // Construir condición de búsqueda
        $condiciones = " tipo='{$this->tipo}' AND coddoc='{$this->coddoc}' AND documento='{$this->documento}'";
        if (!empty($this->estado)) {
            $condiciones .= " AND estado='{$this->estado}'";
        }

        // Buscar usuario
        $usuario = $this->db->fetchOne("SELECT * FROM {$this->model} WHERE {$condiciones}");
        if (!$usuario) {
            return false;
        }

        // Definimos una clave unificada 'user' y campos planos para compatibilidad
        $userData = [
            'documento' => $usuario['documento'],
            'coddoc' => $usuario['coddoc'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'ts' => time(),
        ];

        session()->regenerate();
        session()->put('user', $userData);
        session()->put('tipo', $usuario['tipo']);
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
