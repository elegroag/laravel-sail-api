<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio10;
use App\Models\Mercurio45;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Logger;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CertificadosController extends ApplicationController
{

    protected $tipopc = "8";
    protected $query = "1=1";
    protected $cantidad_pagina = 10;
    protected $db;
    protected $user;
    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }


    public function indexAction()
    {
        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "Certificados",
                "metodo" => "buscarCertificadosBeneficiario",
                "params" =>  parent::getActUser("documento")
            )
        );

        $beneficiarios = false;
        $certificadosPresentados = false;
        $out = $ps->toArray();

        if ($out['success']) {
            $beneficiarios = $out['data'];
            $certificadosPresentados = array();
            foreach ($beneficiarios as $ai => $beneficiario) {
                $has = (new Mercurio45())->getFind("codben='{$beneficiario['codben']}' and estado='P'");
                if ($has) {
                    $certificadosPresentados = $has;
                    $beneficiarios[$ai]['certificadoPendiente'] = true;
                    $beneficiarios[$ai]['certificados'] = $has;
                }
            }
        }

        return view(
            "mercurio/certificados",
            array(
                "certificadosPresentados" => $certificadosPresentados,
                "subsi22" => $beneficiarios,
                "title" => "Presentación Certificados"
            )
        );
    }

    public function guardarAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id');
            $codben = $request->input('codben');
            $nombre = $request->input('nombre');
            $codcer = $request->input('codcer');
            $nomcer = $request->input('nomcer');

            $id_mercurio45 = Mercurio45::max('id') + 1;

            if ((new Mercurio45)->getCount(
                "*",
                "conditions: codben='$codben' and codcer='$codcer' and estado <> 'X'"
            ) > 0) {
                $response = "Ya tiene un certificado presentando, por favor espere a su aprobacion";
                return $this->renderObject($response);
            }

            $mercurio01 = Mercurio01::first();

            $today = Carbon::now();

            $logger = new Logger();
            $id_log = $logger->registrarLog(true, "Presentacion Certificados", "");
            $mercurio45 = new Mercurio45();

            $mercurio45->setId($id_mercurio45);
            $mercurio45->setLog($id_log);
            $mercurio45->setCedtra(parent::getActUser("documento"));
            $mercurio45->setCodben($codben);
            $mercurio45->setNombre($nombre);
            $mercurio45->setCodcer($codcer);
            $mercurio45->setNomcer($nomcer);
            $mercurio45->setEstado("P");
            $mercurio45->setFecha($today->format('Y-m-d'));

            $asignarFuncionario = new AsignarFuncionario();
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            if ($usuario == "") {
                $response = "No se puede realizar el registro (No hay usuario disponible para la atenci&oacute;n de la solicitud.),Comuniquese con la Atencion al cliente";
                return $this->renderObject($response);
            }

            $mercurio45->setUsuario($usuario);
            $mercurio45->setTipo(parent::getActUser("tipo"));
            $mercurio45->setCoddoc(parent::getActUser("coddoc"));
            $mercurio45->setDocumento(parent::getActUser("documento"));

            if (isset($_FILES['archivo_' . $codben]['name']) && $_FILES['archivo_' . $codben]['name'] != "") {
                $extension = explode(".", $_FILES['archivo_' . $codben]['name']);
                $name = $this->tipopc . "_" . $mercurio45->getId() . "." . end($extension);
                $_FILES['archivo_' . $codben]['name'] = $name;

                $estado = $this->uploadFile("archivo_" . $codben, $mercurio01->getPath());

                if ($estado != false) {
                    $mercurio45->setArchivo($name);
                    $mercurio45->save();

                    $item = Mercurio10::where('tipopc', $this->tipopc)
                        ->where('numero', $mercurio45->getId())
                        ->max('item') + 1;

                    $mercurio10 = new Mercurio10();
                    //$mercurio10->setTransaction($Transaccion);
                    $mercurio10->setTipopc($this->tipopc);
                    $mercurio10->setNumero($mercurio45->getId());
                    $mercurio10->setItem($item);
                    $mercurio10->setEstado("P");
                    $mercurio10->setNota("Envio a la Caja para verificación");
                    $mercurio10->setFecsis($today->format('Y-m-d'));
                    $mercurio10->save();

                    $response = "Se adjunto con exito el archivo";
                } else {
                    $response = "No se cargo: Tamano del archivo muy grande o No es Valido";
                }
            } else {
                $response = "No se cargo el archivo";
            }

            //parent::finishTrans();

        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
        return $this->renderObject($response);
    }

    public function uploadFile() {}
}
