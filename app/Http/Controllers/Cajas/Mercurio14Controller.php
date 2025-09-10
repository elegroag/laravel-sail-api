<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Mercurio14Controller extends ApplicationController
{

    /**
     * pagination variable
     * @var Pagination
     */
    protected $pagination;

    /**
     * query variable
     * @var string
     */
    protected $query;

    public function __construct()
    {
        
        
       
        
        
        
        $this->pagination = new Pagination();
    }

    /**
     * beforeFilter function
     * @author elegroag <elegroag@ibero.edu.co>
     * @param array $permisos
     * @return void
     */
    public function beforeFilter($permisos = array())
    {
        $permisos = array("aplicarFiltro" => "71", "info" => "72", "buscar" => "73", "aprobar" => "74", "devolver" => "75", "rechazar" => "76");
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

    public function indexAction()
    {
        $this->setParamToView(
            "campo_filtro",
            array(
                "tipopc" => "Tipo servicio afiliación",
                "tipsoc" => "Tipo sociedad",
                "coddoc" => "Tipo documento"
            )
        );
        $tipopc = array("" => "Selecciona aquí...");
        foreach ((new Mercurio09)->find() as $mer09) {
            $tipopc[$mer09->getTipopc()] = $mer09->getDetalle();
        }

        $coddoc = array("" => "Selecciona aquí...");
        foreach ((new Mercurio12)->find() as $mer12) {
            $coddoc[$mer12->getCoddoc()] = $mer12->getDetalle();
        }

        $tipsoc = array("" => "Selecciona aquí...");
        foreach ((new Subsi54)->find() as $sub54) {
            $tipsoc[$sub54->getTipsoc()] = $sub54->getDetalle();
        }

        $this->setParamToView("tipopc", $tipopc);
        $this->setParamToView("coddoc", $coddoc);
        $this->setParamToView("tipsoc", $tipsoc);

        $this->setParamToView("filters", get_flashdata_item("filter_params"));
        $this->setParamToView("title", "Documentos requeridos empleadores");
        $this->setParamToView("buttons", array("N", "F"));
    }

    public function aplicarFiltroAction()
    {
        return $this->buscarAction();
    }

    public function changeCantidadPaginaAction()
    {
        return $this->buscarAction();
    }

    public function buscarAction()
    {
        $this->setResponse("ajax");

        $consultasOldServices = new GeneralService();
        $this->query = $consultasOldServices->converQuery();

        $pagina = ($request->input('pagina')) ? $request->input('pagina') : 1;
        $cantidad_pagina = ($request->input("numero")) ? $request->input("numero") : 10;
        if ($pagina == "") $pagina = 1;

        if (!$this->query) $this->query = "1=1";

        $this->pagination->setters(
            "cantidadPaginas: {$cantidad_pagina}",
            "pagina: {$pagina}",
            "query: {$this->query}"
        );
        return $this->renderObject($this->pagination->render(new Mercurio14Services()), false);
    }


    /**
     * infor function
     * Consulta y retorna los datos del registro de mercurio14 para poder ser editado
     * @return void
     */
    public function inforAction()
    {
        $this->setResponse("ajax");
        try {
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');
            $tipsoc = $request->input('tipsoc');
            $num14 = (new Mercurio14)->count("*", "conditions: tipopc='{$tipopc}' AND coddoc='{$coddoc}' AND tipsoc='{$tipsoc}'");
            if ($num14 > 0) {
                $mer14 = (new Mercurio14)->findFirst(" tipopc='{$tipopc}' AND coddoc='{$coddoc}' AND tipsoc='{$tipsoc}'");
                $tipopc_detalle = $mer14->getMercurio09()->getDetalle();
                $coddoc_detalle = $mer14->getMercurio12()->getDetalle();
                $data = $mer14->getArray();
                $data['tipopc_detalle'] = $tipopc_detalle;
                $data['coddoc_detalle'] = $coddoc_detalle;

                $response = array(
                    'success' => true,
                    'data' => $data,
                );
            }
        } catch (DbException $e) {
            $response = array(
                'success' => false,
                'data' => null,
                'msj' => $e->getMessage(),
            );
        }
        return $this->renderObject($response, false);
    }

    /**
     * guardarAction function
     *
     * @return void
     */
    public function guardarAction()
    {
        $this->setResponse("ajax");
        try {
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');
            $tipsoc = $request->input('tipsoc');
            $obliga = $request->input('obliga');
            $nota = $request->input('nota');
            $auto_generado = $request->input('auto_generado');

            $num = (new Mercurio14)->count(
                "*",
                "conditions: coddoc='{$coddoc}' AND tipopc='{$tipopc}' AND tipsoc='{$tipsoc}'"
            );
            if ($num == 0) {
                $mercurio14 = new Mercurio14();
                $mercurio14->setTipopc($tipopc);
                $mercurio14->setCoddoc($coddoc);
                $mercurio14->setTipsoc($tipsoc);
                $mercurio14->setObliga($obliga);
                $mercurio14->setNota($nota);
                $mercurio14->setAuto_generado($auto_generado);
                if (!$mercurio14->save()) {
                    throw new DebugException("Error no se puede guardar el registro", 501);
                }
            } else {
                (new Mercurio14)->updateAll(
                    " obliga='{$obliga}', auto_generado='{$auto_generado}', nota='{$nota}'",
                    "conditions: coddoc='{$coddoc}' AND tipopc='{$tipopc}' AND tipsoc='{$tipsoc}'"
                );
            }

            $data = (new Mercurio14)->findFirst("coddoc='{$coddoc}' AND tipopc='{$tipopc}' AND tipsoc='{$tipsoc}'");
            $response = array(
                'success' => true,
                'msj' => 'El registro se completo con éxito.',
                'data' => $data->getArray()
            );
        } catch (DbException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    /**
     * @name function borrarAction
     * @description []
     * ? requeriments:
     * @date update 2025/04/25
     * @author edwin <soportesistemas.comfaca@gmail.com>
     * @return void
     */
    public function borrarAction()
    {
        $this->setResponse("ajax");
        try {
            $tipopc = $request->input('tipopc');
            $coddoc = $request->input('coddoc');
            $tipsoc = $request->input('tipsoc');

            $num = (new Mercurio14)->count(
                "*",
                "conditions: coddoc='{$coddoc}' AND tipopc='{$tipopc}' AND tipsoc='{$tipsoc}'"
            );
            $res = 0;
            if ($num > 0) {
                $res = (new Mercurio14)->deleteAll(" tipopc='{$tipopc}' AND coddoc='{$coddoc}' AND tipsoc='{$tipsoc}'", "limit: 1");
            } else {
                throw new DebugException("Error no se puede borrar el registro, no está disponible.", 501);
            }
            $response = array(
                'success' => true,
                'msj' => 'El registro se borro con éxito.',
                'result' => ($res) ? true : false
            );
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }
}
