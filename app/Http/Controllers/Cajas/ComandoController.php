<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;

class ComandoController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    /**
     * statusComando function
     *
     * @return void
     */
    public function statusComandoAction(Request $request)
    {
        $this->setResponse('ajax');
        $id = $request->input('id');
        if ($id) {
            $comando = $this->Comandos->findFirst("id='{$id}'");
        } else {
            $proceso = $request->input('proceso');
            $servicio = $request->input('servicio');
            $comando = $this->Comandos->findFirst(" usuario='{$$this->usuario}' and (linea_comando like '%{$servicio}%' OR proceso='{$proceso}')");
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
    public function listarComandosAction(Request $request)
    {
        $this->setResponse('ajax');
        $servicio = $request->input('servicio');
        $fechaini = ($request->input('fechaini') == '') ? date('Y-m-d') : $request->input('fechaini');
        $fechafin = ($request->input('fechafin') == '') ? date('Y-m-d') : $request->input('fechafin');
        $sql = "SELECT * FROM comandos WHERE usuario='{$this->usuario}' and (linea_comando like '%{$servicio}%') and (fecha_runner >='{$fechaini}' and fecha_runner <='{$fechafin}')";
        $comandos = $this->db->inQueryAssoc($sql);
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
    public function resultadoComandoAction(Request $request)
    {
        $this->setResponse('ajax');
        $id = $request->input('id');

        $comando = $this->Comandos->findFirst("id='{$id}' and usuario='{$this->usuario}'");
        if ($comando) {
            if ($comando->getEstado() == 'F') {
                $salida = [
                    'success' => true,
                    'data' => $comando->getResultado(),
                ];
            } else {
                $msj = ($comando->getEstado() == 'E') ? 'El comando no ha terminado de procesar, el progreso logrado es del: '.$comando->getProgreso().'%' : '';
                $msj = ($comando->getEstado() == 'X') ? 'El comando ha terminado con salida de error, el progreso logrado es del: '.$comando->getProgreso().'%' : $msj;
                $salida = [
                    'success' => false,
                    'msj' => $msj.' '.$comando->getEstado(),
                ];
            }
        } else {
            $salida = ['success' => false];
        }

        return $this->renderObject($salida, false);
    }
}
