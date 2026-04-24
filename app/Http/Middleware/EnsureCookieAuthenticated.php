<?php

namespace App\Http\Middleware;

use App\Library\Auth\SessionCookies;
use App\Models\MenuItem;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class EnsureCookieAuthenticated
{
    protected $controller;
    protected $actionMethod;
    protected $application;

    /**
     * Manejar una solicitud entrante.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! SessionCookies::check()) {

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado checkeo de session. ',
                    'request' => $request->all(),
                    'user' => session('user'),
                    'has' => session()->has('user')
                ], 401);
            }

            set_flashdata('error', [
                'msj' => 'No autenticado checkeo de session.',
                'code' => 401,
            ]);
            return redirect('web/salir');
        }

        if ($this->autorization($request) === false) {

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado para acceder al modulo, tipo: ' . session('tipo') . '. ',
                ], 401);
            }

            set_flashdata('error', [
                'msj' => 'No autorizado para acceder al modulo. ',
                'code' => 401,
            ]);
            if (!($this->controller == 'PrincipalController' && $this->actionMethod == 'index')) {
                return redirect('mercurio/principal/index');
            }
        }

        $tipo = session()->has('tipo') ? session('tipo') : null;
        $user = session()->has('user') ? session('user') : null;

        if (
            $user &&
            $user != '' &&
            $tipo &&
            $tipo != ''
        ) {
            $request->attributes->set('user', $user);
            $request->attributes->set('tipo', $tipo);
            $request->attributes->set('tipfun', null);
        } else {

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario y tipo no es posible de identificar.',
                ], 401);
            }

            set_flashdata('error', [
                'msj' => 'El usuario y tipo no es posible de identificar.',
                'code' => 401,
            ]);

            return redirect('web/salir');
        }

        return $next($request);
    }

    public function autorization(Request &$request)
    {
        $controllerName = $request->route()->getController();
        // Esto devolverá una instancia de UserController
        $controllerClassName = str_replace('App\\Http\\Controllers\\', '', get_class($controllerName));
        $out = explode('\\', $controllerClassName);
        if (count($out) < 2) {
            $this->controller = $out[0];
            $this->application = null;
        } else {
            $this->application = $out[0];
            $this->controller = $out[1];
        }

        $this->actionMethod = $request->route()->getActionMethod();
        $tipo = session('tipo');
        $estado_afiliado = session('estado_afiliado');

        if ($estado_afiliado == 'I') $tipo = 'P';

        // Verificar si el tipfun tiene permiso para la acción
        $hasPermission = MenuItem::select(
            DB::raw('menu_tipos.tipo')
        )
            ->join('menu_tipos', 'menu_tipos.menu_item', '=', 'menu_items.id')
            ->where('menu_items.controller', $this->controller)
            ->where('menu_items.codapl', 'ME')
            ->where('menu_tipos.tipo', $tipo);

        if (!$hasPermission->exists()) return false; // No autorizado

        return true; // Autorizado
    }
}
