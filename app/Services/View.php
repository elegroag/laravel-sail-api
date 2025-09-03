<?php 
namespace App\Services;

use App\Exceptions\DebugException;

class View {

    public static function renderView(string $view='', array|null $params = null)
	{
		$viewsDir = resource_path('views');
		if (file_exists($viewsDir . '/' . $view . '.phtml')) {
            if (is_array($params) === true) {
                foreach ($params as $ai => $valor) {
                    $$ai = $valor;
                }
            }
			include $viewsDir . '/' . $view . '.phtml';
		} else {
			throw new DebugException("La vista '$view' no existe o no se puede cargar", 0);
		}
	}
}
