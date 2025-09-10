<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Utils\GeneralService;
use App\Models\Mercurio35;
use App\Models\Mercurio10;
use App\Models\Mercurio07;
use App\Models\Mercurio11;
use App\Services\Utils\CalculatorDias;
use App\Services\Utils\SenderEmail;
use App\Exceptions\DebugException;
use App\Library\DbException;
use App\Services\Tag;

class AprobacionretiroController extends ApplicationController
{

    private $tipopc = 7;
    protected $cantidad_pagina = 10;
    protected $query = "";

    public function __construct()
    {        
    }

    public function showTabla($paginate)
    {
        $html  = '<table border="0" cellpadding="0" cellspacing="0" class="table table-bordered">';
        $html .= "<thead class='thead-light'>";
        $html .= "<tr>";
        $html .= "<th scope='col'>Cedula</th>";
        $html .= "<th scope='col'>Nombre</th>";
        $html .= "<th scope='col'>Dias</th>";
        $html .= "<th scope='col'></th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody class='list'>";
        foreach ($paginate->items as $mtable) {

            $dias_vencidos = CalculatorDias::calcular($this->tipopc, $mtable->getId(), $mtable->getFecret());
            if ($dias_vencidos == 3) {
                $html .= "<tr style='background: #f1f1ad'>";
            } else if ($dias_vencidos > 3) {
                $html .= "<tr style='background: #f5b2b2'>";
            } else {
                $html .= "<tr>";
            }
            $html .= "<td>{$mtable->getCedtra()}</td>";
            $html .= "<td>{$mtable->getNomtra()}</td>";
            $html .= "<td>$dias_vencidos</td>";
            $html .= "<td class='table-actions'>";
            $html .= "<a href='#!' class='table-action btn btn-xs btn-primary' title='Info' onclick=\"info('{$mtable->getId()}')\">";
            $html .= "<i class='fas fa-folder-open text-white'></i>";
            $html .= "</a>";
            $html .= "</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        return $html;
    }

    public function aplicarFiltroAction(Request $request)
    {
        $this->setResponse("ajax");
        $consultasOldServices = new GeneralService();
        $this->query = $consultasOldServices->converQuery();
        $this->buscarAction($request);
    }

    public function changeCantidadPaginaAction(Request $request)
    {
        $this->setResponse("ajax");
        $this->cantidad_pagina = $request->input("numero");
        $this->buscarAction($request);
    }

    public function indexAction()
    {
        $campo_field = array(
            "nit" => "Nit",
            "cedtra" => "Cedula",
            "nomtra" => "Nombre",
        );
        $this->setParamToView("campo_filtro", $campo_field);
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Aprobacion Retiro Trabajadores");
        $this->setParamToView("buttons", array("F"));
       // Tag::setDocumentTitle('Aprobacion Retiro Trabajadores');
    }

    public function buscarAction(Request $request)
    {
        $this->setResponse("ajax");
        $pagina = $request->input('pagina', 1);
        $paginate = $this->Mercurio35->find("$this->query and estado='P' AND usuario = " . parent::getActUser())->paginate($this->cantidad_pagina, ['*'], 'page', $pagina);
        
        $html = $this->showTabla($paginate);
        $consultasOldServices = new GeneralService();
        $html_paginate = $consultasOldServices->showPaginate($paginate);
        
        return $this->renderObject([
            'consulta' => $html,
            'paginate' => $html_paginate
        ], false);
    }

    public function inforAction(Request $request)
    {
        $this->setResponse("ajax");
        $id = $request->input('id');
        $mercurio35 = $this->Mercurio35->findFirst("id='$id'");
        $response = "";

        $consultasOldServices = new  GeneralService();
        #$response .= $consultasOldServices->consultaRetiro($mercurio35);

        $response .= "<hr class='my-3'>";
        $response .= "<h6 class='heading-small text-muted mb-4'>Acciones </h6>";

        $response .= "<div class='row pl-lg-4'>";
        $response .= "<div class='col-md-3'>";
        $response .= "<div class='nav-wrapper'>";
        $response .= "<ul class='nav flex-column nav-pills' id='v-pills-tab' role='tablist' aria-orientation='vertical'>";

        $response .= "<li class='mb-1 mb-md-2'>";
        $response .= "<a class='nav-link active' id='v-pills-home-tab' data-toggle='pill' href='#v-aprobar' role='tab'>Aprobar</a>";
        $response .= "</li>";

        $response .= "<li class='mb-1 mb-md-2'>";
        $response .= "<a class='nav-link' id='v-pills-messages-tab' data-toggle='pill' href='#v-rechazar' role='tab'>Rechazar</a>";
        $response .= "</li>";

        $response .= "</ul>";
        $response .= "</div>";
        $response .= "</div>";

        $response .= "<div class='col-md-9'>";
        $response .= "<div class='tab-content' id='v-pills-tabContent'>";
        $response .= "<div class='tab-pane fade show active' id='v-aprobar' role='tabpanel' aria-labelledby='v-pills-home-tab'>";

        $response .= "<div class='row'>";
        $response .= "<div class='col'>";
        $response .= "<div class='jumbotron mb-1 py-4'>";
        $response .= "<h2>Aprobar</h2>";
        $response .= "<p>Esta opcion es para aprobar la empresa y enviar los datos a Subsidio</p>";
        $response .= "<hr class='my-3'>";
        $response .= "<p class='lead'>";
        $response .= "<div class='form-group'>";
        $response .= "<textarea class='form-control' id='nota_aprobar' nota='nota_aprobar' rows='3'></textarea>";
        $response .= "</div>";
        $response .= "<button type='button' class='btn btn-success btn-block' onclick='aprobar($id)'>Aprobar</button>";
        $response .= "</p>";
        $response .= "</div>";
        $response .= "</div>";
        $response .= "</div>";

        $response .= "</div>";
        $response .= "<div class='tab-pane fade' id='v-rechazar' role='tabpanel' aria-labelledby='v-pills-messages-tab'>";

        $response .= "<div class='row'>";
        $response .= "<div class='col'>";
        $response .= "<div class='jumbotron mb-1 py-4'>";
        $response .= "<h2>Rechazar</h2>";
        $response .= "<p>Esta opcion es para rechazar a la empresa e informarle la causal del rechazo</p>";
        $response .= "<hr class='my-3'>";
        $response .= "<p class='lead'>";
        $response .= "<div class='form-group'>";
        #$response .= Tag::selectStatic("codest", $this->Mercurio11->find(), "using: codest,detalle", "use_dummy: true", "dummyValue: ", "class: form-control");
        $response .= "</div>";
        $response .= "<div class='form-group'>";
        $response .= "<textarea class='form-control' id='nota_rechazar' nota='nota_rechazar' rows='3'></textarea>";
        $response .= "</div>";
        $response .= "<button type='button' class='btn btn-danger btn-block' onclick='rechazar($id)'>Rechazar</button>";
        $response .= "</p>";
        $response .= "</div>";
        $response .= "</div>";
        $response .= "</div>";

        $response .= "</div>";
        $response .= "</div>";
        $response .= "</div>";
        $response .= "</div>";

        return $this->renderText($response);
    }

    public function aprobarAction(Request $request)
    {
        try {
            
                $this->setResponse("ajax");
                $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
                $nota = $request->input('nota', "addslaches", "alpha", "extraspaces", "striptags");
                $fecest = $request->input('fecest', "addslaches", "alpha", "extraspaces", "striptags");

                $modelos = array("mercurio10", "mercurio35");
                
                $response = $this->db->begin();
                $today = new \DateTime();
                $mercurio35 = $this->Mercurio35->findFirst("id='$id'");
                if (!$fecest) {
                    $fecest = $today->format('Y-m-d');
                }
                $this->Mercurio35->updateAll("estado='A',fecest='{$fecest}'", "conditions: id='$id' ");
                $item = $this->Mercurio10->maximum("item", "conditions: tipopc='$this->tipopc' and numero='$id'") + 1;
                $mercurio10 = new Mercurio10();
                
                $mercurio10->setTipopc($this->tipopc);
                $mercurio10->setNumero($id);
                $mercurio10->setItem($item);
                $mercurio10->setEstado("A");
                $mercurio10->setNota($nota);
                $mercurio10->setFecsis($today->format('Y-m-d'));
                if (!$mercurio10->save()) {
                    
                    $this->db->rollback();
                }
                $params['nit'] = $mercurio35->getNit();
                $params['cedtra'] = $mercurio35->getCedtra();
                $params['codest'] = $mercurio35->getCodest();
                $params['fecest'] = $mercurio35->getFecest();

                $consultasOldServices = new GeneralService();
                $result = $consultasOldServices->webService("retiroTrabajador", $params);
                if ($result['flag'] == false) {
                    $response = parent::errorFunc($result['msg']);
                    return $this->renderObject($response, false);
                }
                if ($result['data']['flag'] == false) {
                    $response = parent::errorFunc($result['data']['msg']);
                    return $this->renderObject($response, false);
                }
                $mercurio07 = $this->Mercurio07->findFirst("tipo='{$mercurio35->getTipo()}' and coddoc='{$mercurio35->getCoddoc()}' and documento = '{$mercurio35->getDocumento()}'");
                $asunto = "Retiro Trabajador";
                $msj  = "se informa que el trabajador {$mercurio35->getCedtra()} fue retirado exitsomante";
                $senderEmail = new SenderEmail();
                $senderEmail->send($mercurio07->getEmail(), $msj);
                $this->db->commit();
                $response = parent::successFunc("Movimiento Realizado Con Exito");
                return $this->renderObject($response, false);
           
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }

    public function rechazarAction(Request $request)
    {
        try {
            
            $this->setResponse("ajax");
            $id = $request->input('id', "addslaches", "alpha", "extraspaces", "striptags");
            $nota = $request->input('nota', "addslaches", "alpha", "extraspaces", "striptags");
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $modelos = array("mercurio10", "mercurio35");
            
            $response = $this->db->begin();
            $today = new \DateTime();
            $mercurio35 = $this->Mercurio35->findFirst("id='$id'");
            $this->Mercurio35->updateAll(
                "estado='X',motivo='$nota',motrec='$codest',fecest='{$today->format('Y-m-d')}'", "conditions: id='$id' ");
            $item = $this->Mercurio10->maximum("item", "conditions: tipopc='$this->tipopc' and numero='$id'") + 1;
            $mercurio10 = new Mercurio10();
            
            $mercurio10->setTipopc($this->tipopc);
            $mercurio10->setNumero($id);
            $mercurio10->setItem($item);
            $mercurio10->setEstado("X");
            $mercurio10->setNota($nota);
            $mercurio10->setCodest($codest);
            $mercurio10->setFecsis($today->format('Y-m-d'));
            if (!$mercurio10->save()) {
                
                $this->db->rollback();
            }
            $mercurio07 = $this->Mercurio07->findFirst("tipo='{$mercurio35->getTipo()}' and coddoc='{$mercurio35->getCoddoc()}' and documento = '{$mercurio35->getDocumento()}'");
            $asunto = "Retiro Trabajador";
            $msj  = "acabas de utilizar";
            $senderEmail = new SenderEmail();
            $senderEmail->send($mercurio07->getEmail(), $msj);
            $this->db->commit();
            $response = parent::successFunc("Movimiento Realizado Con Exito");
            return $this->renderObject($response, false);
            
        } catch (DebugException $e) {
            $response = parent::errorFunc("No se pudo realizar el movimiento");
            return $this->renderObject($response, false);
        }
    }
}
