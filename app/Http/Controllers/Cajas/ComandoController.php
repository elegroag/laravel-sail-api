<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ComandoController extends ApplicationController
{

    protected $db;
    protected $usuario;

    public function initialize()
    {
        Core::importHelper('hash');
        Core::importLibrary("Services", "Services");
        Core::importHelper('format');
        $this->setTemplateAfter("template");
        $this->db = (object) DbBase::rawConnect();
        $this->db->setFetchMode(DbBase::DB_ASSOC);
        $this->usuario = parent::getActUser('usuario');
    }

    /**
     * statusComando function
     * @return void
     */
    public function statusComandoAction()
    {
        $this->setResponse("ajax");
        $id = $this->getPostParam('id');
        if ($id) {
            $comando = $this->Comandos->findFirst("id='{$id}'");
        } else {
            $proceso = $this->getPostParam('proceso');
            $servicio = $this->getPostParam('servicio');
            $comando = $this->Comandos->findFirst(" usuario='{$$this->usuario}' and (linea_comando like '%{$servicio}%' OR proceso='{$proceso}')");
        }
        if ($comando) {
            $salida = array(
                "success" => true,
                "id" => $comando->getId(),
                "progreso" => $comando->getProgreso(),
                "usuario" => $comando->getUsuario(),
                "proceso" => $comando->getProceso(),
                "estado" => $comando->getEstado()
            );
        } else {
            $salida = array("success" => false);
        }
        return $this->renderObject($salida, false);
    }

    /**
     * listarComandos function
     * @return void
     */
    public function listarComandosAction()
    {
        $this->setResponse("ajax");
        $servicio = $this->getPostParam('servicio');
        $fechaini = ($this->getPostParam('fechaini') == '') ? date('Y-m-d') : $this->getPostParam('fechaini');
        $fechafin = ($this->getPostParam('fechafin') == '') ? date('Y-m-d') : $this->getPostParam('fechafin');
        $sql = "SELECT * FROM comandos WHERE usuario='{$this->usuario}' and (linea_comando like '%{$servicio}%') and (fecha_runner >='{$fechaini}' and fecha_runner <='{$fechafin}')";
        $comandos = $this->db->fetchAll($sql);
        if ($comandos) {
            $salida = array(
                "success" => true,
                "data" => $comandos
            );
        } else {
            $salida = array("success" => false);
        }
        return $this->renderObject($salida, false);
    }

    /**
     * resultadoComando
     * @return void
     */
    public function resultadoComandoAction()
    {
        $this->setResponse("ajax");
        $id = $this->getPostParam('id');

        $comando = $this->Comandos->findFirst("id='{$id}' and usuario='{$this->usuario}'");
        if ($comando) {
            if ($comando->getEstado() == 'F') {
                $salida = array(
                    "success" => true,
                    "data" => $comando->getResultado()
                );
            } else {
                $msj = ($comando->getEstado() == 'E') ? "El comando no ha terminado de procesar, el progreso logrado es del: " . $comando->getProgreso() . "%" : "";
                $msj = ($comando->getEstado() == 'X') ? "El comando ha terminado con salida de error, el progreso logrado es del: " . $comando->getProgreso() . "%" : $msj;
                $salida = array(
                    "success" => false,
                    "msj" => $msj . " " . $comando->getEstado()
                );
            }
        } else {
            $salida = array("success" => false);
        }
        return $this->renderObject($salida, false);
    }
}
