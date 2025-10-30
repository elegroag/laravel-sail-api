<?php

namespace App\Http\Middleware;

use App\Library\Auth\SessionCookies;
use App\Models\MenuItem;
use App\Models\MenuPermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class CajasCookieAuthenticated
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
                'msj' => 'No autorizado para acceder al modulo. ' .__LINE__ .' '. $this->controller,
                'code' => 401,
            ]);

            return redirect('cajas/login');
        }

        if ($this->autorization($request) === false) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado para acceder al modulo. ' . $this->controller,
                ], 401);
            }
            
            if (!($this->controller == 'PrincipalController' && $this->actionMethod == 'index')) {
                set_flashdata('error', [
                    'msj' => 'No autorizado para acceder al modulo. ' .__LINE__ .' '. $this->controller,
                    'code' => 401,
                ]);
                return redirect('cajas/principal/index');
            }
        }

        if ($this->validOption($request) === false) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado para acceder a la acción. ' . $this->controller . ' ' . $this->actionMethod,
                ], 401);
            }
            if (!($this->controller == 'PrincipalController' && $this->actionMethod == 'index')) {

                set_flashdata('error', [
                    'msj' => 'No autorizado para acceder a la acción. ' .__LINE__ .' '. $this->controller . ' ' . $this->actionMethod,
                    'code' => 401,
                ]);
                return redirect('cajas/principal/index');
            }
        }

        $tipo = session()->has('tipo') ? session('tipo') : null;
        if ($tipo) {
            // no es valido es usuario de mercurio no de cajas
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario de mercurio no autorizado para acceso a Sistema de Caja.',
                ], 401);
            }

            set_flashdata('error', [
                'msj' => 'Usuario de mercurio no autorizado para acceso a Sistema de Caja.',
                'code' => 401,
            ]);

            // redirigir a la pantalla de login de mercurio
            return redirect('web/login');
        }

        $tipfun = session()->has('tipfun') ? session('tipfun') : null;
        $user = session()->has('user') ? session('user') : null;

        if ($user && $user != null && $tipfun && $tipfun != null) {
            $request->attributes->set('user', $user);
            $request->attributes->set('tipfun', $tipfun);
        } else {
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
            return redirect('cajas/login');
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
        $tipfun = session('tipfun');

        // Verificar si el tipfun tiene permiso para la acción
        $hasPermission = MenuItem::select(
            DB::raw('menu_permissions.opciones')
        )
            ->join('menu_permissions', 'menu_permissions.menu_item', '=', 'menu_items.id')
            ->where('menu_items.controller', $this->controller)
            ->where('menu_permissions.tipfun', $tipfun);

        if (!$hasPermission->exists()) {
            return false; // No autorizado
        }
        $request->attributes->set('opciones', json_decode($hasPermission->first()->opciones), true);
        return true; // Autorizado
    }

    public function validOption(Request $request)
    {
        $opciones = $request->attributes->get('opciones');
        if (is_array($opciones)) {
            if (key_exists(strtolower($this->actionMethod), $opciones)) {
                if ($opciones[strtolower($this->actionMethod)] == false) {
                    return false;
                }
            }
        }
        return true;
    }
}
