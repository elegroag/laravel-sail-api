<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Comandos;
use Illuminate\Http\Request;

class ComandoController extends ApplicationController
{

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    /**
     * statusComando function
     *
     * @return void
     */
    public function statusComando(Request $request)
    {
        $this->setResponse('ajax');
        $id = $request->input('id');
        if ($id) {
            $comando = Comandos::where('id', $id)->first();
        } else {
            $proceso = $request->input('proceso');
            $servicio = $request->input('servicio');
            $comando = Comandos::where('usuario', $this->usuario)
                ->where(function ($q) use ($servicio, $proceso) {
                    $q->where('linea_comando', 'like', "%{$servicio}%")
                        ->orWhere('proceso', $proceso);
                })
                ->first();
        }
        if ($comando) {
            $salida = [
                'success' => true,
                'id' => $comando->getId(),
                'progreso' => $comando->getProgreso(),
                'usuario' => $comando->getUsuario(),
                'proceso' => $comando->getProceso(),
                'estado' => $comando->getEstado(),
            ];
        } else {
            $salida = ['success' => false];
        }

        return $this->renderObject($salida, false);
    }

    /**
     * listarComandos function
     *
     * @return void
     */
    public function listarComandos(Request $request)
    {
        $this->setResponse('ajax');
        $servicio = $request->input('servicio');
        $fechaini = ($request->input('fechaini') == '') ? date('Y-m-d') : $request->input('fechaini');
        $fechafin = ($request->input('fechafin') == '') ? date('Y-m-d') : $request->input('fechafin');
        $comandos = Comandos::where('usuario', $this->usuario)
            ->where('linea_comando', 'like', "%{$servicio}%")
            ->where('fecha_runner', '>=', $fechaini)
            ->where('fecha_runner', '<=', $fechafin)
            ->get()
            ->toArray();
        if ($comandos) {
            $salida = [
                'success' => true,
                'data' => $comandos,
            ];
        } else {
            $salida = ['success' => false];
        }

        return $this->renderObject($salida, false);
    }

    /**
     * resultadoComando
     *
     * @return void
     */
    public function resultadoComando(Request $request)
    {
        $this->setResponse('ajax');
        $id = $request->input('id');

        $comando = Comandos::where('id', $id)->where('usuario', $this->usuario)->first();
        if ($comando) {
            if ($comando->getEstado() == 'F') {
                $salida = [
                    'success' => true,
                    'data' => $comando->getResultado(),
                ];
            } else {
                $msj = ($comando->getEstado() == 'E') ? 'El comando no ha terminado de procesar, el progreso logrado es del: ' . $comando->getProgreso() . '%' : '';
                $msj = ($comando->getEstado() == 'X') ? 'El comando ha terminado con salida de error, el progreso logrado es del: ' . $comando->getProgreso() . '%' : $msj;
                $salida = [
                    'success' => false,
                    'msj' => $msj . ' ' . $comando->getEstado(),
                ];
            }
        } else {
            $salida = ['success' => false];
        }

        return $this->renderObject($salida, false);
    }
}
