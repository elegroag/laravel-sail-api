<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AprobacioncerController extends ApplicationController
{
    private $tipopc = 8;

    public function initialize()
    {
        $this->setPersistance(false);
        Core::importHelper('format');
        Core::importLibrary("Services", "Services");
        Core::importLibrary("Pagination", "Pagination");
        Core::importLibrary("ParamsBeneficiario", "Collections");
        $this->setTemplateAfter('bone');
        $this->services = Services::Init();
    }

    /**
     * services variable
     * @var Services
     */
    protected $services;

    public function beforeFilter($permisos = array())
    {
        $permisos = array(
            "aplicarFiltro" => "113",
            "info" => "114",
            "buscar" => "115",
            "aprobar" => "116",
            "devolver" => "117",
            "Rechazar" => "118"
        );
        $flag = parent::beforeFilter($permisos);
        if (!$flag) {
            $response = parent::errorFunc("No cuenta con los permisos para este proceso");
            if (is_ajax()) {
                $this->setResponse("ajax");
                $this->renderObject($response, false);
            } else {
                Router::redirectToApplication('Cajas/principal/index');
            }
            return false;
        }
    }

    public function aplicarFiltroAction($estado = 'P')
    {
        $this->setResponse("ajax");
        $cantidad_pagina = ($this->getPostParam("numero")) ? $this->getPostParam("numero") : 10;
        $usuario = parent::getActUser();

        $pagination = new Pagination(
            new Request(
                array(
                    "cantidadPaginas" => $cantidad_pagina,
                    "query" => "usuario='{$usuario}' and estado='{$estado}'",
                    "estado" => $estado
                )
            )
        );

        $query = $pagination->filter(
            $this->getPostParam('campo'),
            $this->getPostParam('condi'),
            $this->getPostParam('value')
        );

        Flash::set_flashdata("filter_certificado", $query, true);
        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new CertificadosServices());
        return $this->renderObject($response, false);
    }

    public function changeCantidadPaginaAction($estado = 'P')
    {
        $this->buscarAction($estado);
    }

    public function indexAction()
    {
        $campo_field = array(
            "codben" => "Cedula",
            "nombre" => "Primer Apellido",
        );

        $this->setParamToView("campo_filtro", $campo_field);
        $this->setParamToView("title", "Aprobacion Certificados");
        $this->setParamToView("buttons", array("F"));
        $this->setParamToView("mercurio11", $this->Mercurio11->find());
        Tag::setDocumentTitle('Aprobacion Certificados');
    }

    public function buscarAction($estado = 'P')
    {
        $this->setResponse("ajax");
        $pagina = ($this->getPostParam('pagina')) ? $this->getPostParam('pagina') : 1;
        $cantidad_pagina = ($this->getPostParam("numero")) ? $this->getPostParam("numero") : 10;
        $usuario = parent::getActUser();

        $pagination = new Pagination(
            new Request(
                array(
                    "cantidadPaginas" => $cantidad_pagina,
                    "query" => "usuario='{$usuario}' and estado='{$estado}'",
                    "estado" => $estado,
                    "pagina" => $pagina,
                )
            )
        );

        $query = $pagination->filter(
            $this->getPostParam('campo'),
            $this->getPostParam('condi'),
            $this->getPostParam('value')
        );

        Flash::set_flashdata("filter_certificado", $query, true);
        Flash::set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new CertificadosServices());
        return $this->renderObject($response, false);
    }

    public function inforAction()
    {
        $this->setResponse("ajax");
        try {
            $id = $this->getPostParam('id');
            if (!$id) {
                throw new Exception("Error no se puede identificar el identificador de la solicitud.", 501);
            }
            $mercurio45 = (new Mercurio45)->findFirst("id='{$id}'");
            $html = View::render(
                'aprobacioncer/tmp/consulta',
                array(
                    'mercurio01' => $this->Mercurio01->findFirst(),
                    'mercurio45' => $mercurio45
                )
            );

            $certificadoServices = new CertificadosServices();
            $adjuntos = $certificadoServices->adjuntos($mercurio45);
            $seguimiento = $certificadoServices->seguimiento($mercurio45);

            $campos_disponibles = $mercurio45->CamposDisponibles();
            $response = array(
                'success' => true,
                'data' => $mercurio45->getArray(),
                'mercurio11' => $this->Mercurio11->find(),
                "consulta" => $html,
                'adjuntos' => $adjuntos,
                'seguimiento' => $seguimiento,
                'campos_disponibles' => $campos_disponibles,
            );
        } catch (Exception $err) {
            $response = array(
                'success' => false,
                'msj' => $err->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    /**
     * apruebaAction function
     * @return void
     */
    public function apruebaAction()
    {
        $this->setResponse("ajax");
        Services::Init();
        $user = Auth::getActiveIdentity();
        $debuginfo = array();
        try {
            try {
                $acceso = (new Gener42)->count("*", "conditions: permiso='92' AND usuario='{$user['usuario']}'");
                if ($acceso == 0) {
                    return $this->renderObject(array(
                        "success" => false,
                        "msj" => "El usuario no dispone de permisos de aprobación"
                    ));
                }

                $aprueba = new ApruebaCertificado();
                $aprueba->setTransa();
                $postData = $_POST;
                $idSolicitud = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
                $aprueba->findSolicitud($idSolicitud);
                $aprueba->findSolicitante();
                $aprueba->procesar($postData);
                $aprueba->endTransa();
                $aprueba->enviarMail($this->getPostParam('actapr'));
                $salida = array(
                    'success' => true,
                    'msj' => 'El registro se completo con éxito'
                );
            } catch (DebugException $err) {
                $debuginfo = $err->getDebugInfo();
                $aprueba->closeTransa($err->getMessage());
                $salida = array(
                    "success" => false,
                    "msj" => $err->getMessage(),
                );
            }
        } catch (TransactionFailed $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
            );
        }
        if ($debuginfo) $salida['info'] = $debuginfo;
        return $this->renderObject($salida, false);
    }

    public function rechazarAction()
    {
        try {
            try {
                $this->setResponse("ajax");
                $id = $this->getPostParam('id', "addslaches", "alpha", "extraspaces", "striptags");
                $nota = $this->getPostParam('nota', "addslaches", "alpha", "extraspaces", "striptags");
                $codest = $this->getPostParam('codest', "addslaches", "alpha", "extraspaces", "striptags");
                $modelos = array("mercurio10", "mercurio45");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $today = new Date();
                $mercurio45 = $this->Mercurio45->findFirst("id='$id'");
                $this->Mercurio45->updateAll("estado='X',motivo='$nota',codest='$codest',fecest='{$today->getUsingFormatDefault()}'", "conditions: id='$id' ");
                $item = $this->Mercurio10->maximum("item", "conditions: tipopc='$this->tipopc' and numero='$id'") + 1;
                $mercurio10 = new Mercurio10();
                $mercurio10->setTransaction($Transaccion);
                $mercurio10->setTipopc($this->tipopc);
                $mercurio10->setNumero($id);
                $mercurio10->setItem($item);
                $mercurio10->setEstado("X");
                $mercurio10->setNota($nota);
                $mercurio10->setCodest($codest);
                $mercurio10->setFecsis($today->getUsingFormatDefault());
                if (!$mercurio10->save()) {
                    parent::setLogger($mercurio10->getMessages());
                    parent::ErrorTrans();
                }
                $mercurio07 = $this->Mercurio07->findFirst("tipo='{$mercurio45->getTipo()}' and coddoc='{$mercurio45->getCoddoc()}' and documento = '{$mercurio45->getDocumento()}'");
                $asunto = "Certificado";
                $msj  = "acabas de utilizar";
                $senderEmail = new SenderEmail();
                $senderEmail->sendEmail($mercurio07->getEmail(), $mercurio07->getNombre(), $asunto, $msj, "");
                parent::finishTrans();
                $response = parent::successFunc("Movimiento Realizado Con Exito");
                return $this->renderObject($response, false);
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }
}
