<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exceptions\DebugException;
use App\Services\Utils\Pagination;
use App\Services\CajaServices\CertificadosServices;
use App\Models\Mercurio10;
use App\Models\Mercurio45;
use App\Models\Mercurio07;
use App\Models\Gener42;
use App\Services\Aprueba\ApruebaCertificado;
use App\Services\Request as ServicesRequest;
use Illuminate\Support\Facades\View;
use App\Services\Utils\SenderEmail;

class AprobacioncerController extends ApplicationController
{
    protected $tipopc = 8;
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
     * services variable
     * @var Services
     */
    protected $services;


    public function aplicarFiltroAction(Request $request, string $estado = 'P')
    {
        $this->setResponse("ajax");
        $cantidad_pagina = $request->input("numero", 10);
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
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata("filter_certificado", $query, true);
        set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new CertificadosServices());
        return $this->renderObject($response, false);
    }

    public function changeCantidadPaginaAction(Request $request, string $estado = 'P')
    {
        $this->buscarAction($request, $estado);
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
        //Tag::setDocumentTitle('Aprobacion Certificados');
    }

    public function buscarAction(Request $request, string $estado = 'P')
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina', 1);
        $cantidad_pagina = $request->input("numero", 10);
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
            $request->input('campo'),
            $request->input('condi'),
            $request->input('value')
        );

        set_flashdata("filter_certificado", $query, true);
        set_flashdata("filter_params", $pagination->filters, true);

        $response = $pagination->render(new CertificadosServices());
        return $this->renderObject($response, false);
    }

    public function inforAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id');
            if (!$id) {
                throw new DebugException("Error no se puede identificar el identificador de la solicitud.", 501);
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
        } catch (DebugException $err) {
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
    public function apruebaAction(Request $request)
    {
        $this->setResponse("ajax");

        $user = session()->get('user');
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
                $this->db->begin();
                $postData = $request->all();
                $idSolicitud = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
                $aprueba->findSolicitud($idSolicitud);
                $aprueba->findSolicitante();
                $aprueba->procesar($postData);
                $this->db->commit();
                $aprueba->enviarMail($request->input('actapr'));
                $salida = array(
                    'success' => true,
                    'msj' => 'El registro se completo con éxito'
                );
            } catch (DebugException $err) {

                $this->db->rollback();
                $salida = array(
                    "success" => false,
                    "msj" => $err->getMessage(),
                );
            }
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage(),
            );
        }
        if ($debuginfo) $salida['info'] = $debuginfo;
        return $this->renderObject($salida, false);
    }

    public function rechazarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = $request->input('nota', "addslaches", "alpha", "extraspaces", "striptags");
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $modelos = array("mercurio10", "mercurio45");

            $response = $this->db->begin();
            $today = Carbon::now();
            $mercurio45 = $this->Mercurio45->findFirst("id='$id'");
            $this->Mercurio45->updateAll("estado='X',motivo='$nota',codest='$codest',fecest='" . $today->format('Y-m-d H:i:s') . "'", "conditions: id='$id' ");
            $item = $this->Mercurio10->maximum("item", "conditions: tipopc='$this->tipopc' and numero='$id'") + 1;
            $mercurio10 = new Mercurio10();

            $mercurio10->setTipopc($this->tipopc);
            $mercurio10->setNumero($id);
            $mercurio10->setItem($item);
            $mercurio10->setEstado("X");
            $mercurio10->setNota($nota);
            $mercurio10->setCodest($codest);
            $mercurio10->setFecsis($today->format('Y-m-d H:i:s'));
            if (!$mercurio10->save()) {

                $this->db->rollback();
            }
            $mercurio07 = $this->Mercurio07->findFirst("tipo='{$mercurio45->getTipo()}' and coddoc='{$mercurio45->getCoddoc()}' and documento = '{$mercurio45->getDocumento()}'");
            $asunto = "Certificado";
            $msj  = "acabas de utilizar";
            $senderEmail = new SenderEmail(new ServicesRequest([
                "email_emisor" => $mercurio07->getEmail(),
                "email_clave" => $mercurio07->getClave(),
                "asunto" => $asunto,
            ]));

            $senderEmail->send($mercurio07->getEmail(), $asunto);
            $this->db->commit();
            $response = parent::successFunc("Movimiento Realizado Con Exito");
            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }
}
