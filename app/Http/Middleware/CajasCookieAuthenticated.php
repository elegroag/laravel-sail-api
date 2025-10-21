<?php

namespace App\Http\Middleware;

use App\Library\Auth\SessionCookies;
use App\Models\MenuPermission;
use Closure;
use Illuminate\Http\Request;
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

            return redirect('cajas/login');
        }

        if ($this->autorization($request) === false) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado para acceder a la acción.',
                ], 401);
            }
            if(!($this->controller == 'PrincipalController' && $this->actionMethod == 'index')){
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

            return redirect('cajas/login');
        }

        return $next($request);
    }

    public function autorization(Request $request)
    {
        $controllerName = $request->route()->getController(); // Esto devolverá una instancia de UserController
        $controllerClassName = str_replace('App\\Http\\Controllers\\', '', get_class($controllerName));
        $out = explode('\\', $controllerClassName);
        if (count($out) < 2) {
            $this->controller = $out[0];
            $this->application = null;
        }else{
            $this->application = $out[0];
            $this->controller = $out[1];
        }

        $this->actionMethod = $request->route()->getActionMethod();
        $tipfun = session('tipfun');

        // Verificar si el tipfun tiene permiso para la acción
        $hasPermission = MenuPermission::where('tipfun', $tipfun)
            ->where('menu_item_id', $this->controller . '.' . $this->actionMethod) // Asume que menu_item_id coincide con controller.action
            ->where('can_view', true)
            ->exists();

        if (!$hasPermission) {
            return false; // No autorizado
        }

        return true; // Autorizado
    }
}
