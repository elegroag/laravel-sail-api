<?php

namespace App\Http\Middleware;

use App\Library\Auth\SessionCookies;
use App\Models\MenuItem;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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
                    'message' => 'No autenticado.',
                ], 401);
            }

            set_flashdata('error', [
                'msj' => 'No autenticado.',
                'code' => 401,
            ]);
            return redirect('web/login');
        }

        if ($this->autorization($request) === false) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado para acceder al modulo. ',
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

        if ($user && $user != null && $tipo && $tipo != null) {
            $request->attributes->set('mercurio_user', $user);
            $request->attributes->set('mercurio_tipo', $tipo);
        } else {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.',
                ], 401);
            }

            set_flashdata('error', [
                'msj' => 'No autorizado para acceder al modulo. ',
                'code' => 401,
            ]);
            return redirect('mercurio/principal/index');
        }

        return $next($request);
    }

    public function autorization(Request &$request)
    {
        $controllerName = $request->route()->getController(); // Esto devolverá una instancia de UserController
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

        if ($estado_afiliado == 'I') {
            $tipo = 'P';
        }

        // Verificar si el tipfun tiene permiso para la acción
        $hasPermission = MenuItem::select(
            DB::raw('menu_tipos.tipo')
        )
            ->join('menu_tipos', 'menu_tipos.menu_item', '=', 'menu_items.id')
            ->where('menu_items.controller', $this->controller)
            ->where('menu_items.codapl', 'ME')
            ->where('menu_tipos.tipo', $tipo);

        if (!$hasPermission->exists()) {
            return false; // No autorizado
        }
        return true; // Autorizado
    }
}
