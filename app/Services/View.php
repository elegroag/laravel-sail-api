<?php

namespace App\Services;

use App\Exceptions\DebugException;

class View
{
    /**
     * Renderiza una vista .phtml y retorna el HTML como string.
     * Compatibilidad con controladores legacy que esperan View::render(...).
     */
    public static function render(string $view = '', ?array $params = null): string
    {
        $viewsDir = resource_path('views');
        $file = $viewsDir.'/'.$view.'.phtml';
        if (! file_exists($file)) {
            throw new DebugException("La vista '$view' no existe o no se puede cargar", 0);
        }
        if (is_array($params)) {
            foreach ($params as $ai => $valor) {
                $$ai = $valor;
            }
        }
        ob_start();
        include $file;

        return ob_get_clean() ?: '';
    }

    public static function renderView(string $view = '', ?array $params = null)
    {
        $viewsDir = resource_path('views');
        if (file_exists($viewsDir.'/'.$view.'.phtml')) {
            if (is_array($params) === true) {
                foreach ($params as $ai => $valor) {
                    $$ai = $valor;
                }
            }
            include $viewsDir.'/'.$view.'.phtml';
        } else {
            throw new DebugException("La vista '$view' no existe o no se puede cargar", 0);
        }
    }
}
