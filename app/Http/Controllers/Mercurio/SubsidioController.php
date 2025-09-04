<?php
namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Auth\AuthJwt;
use App\Library\Auth\SessionCookies;
use App\Models\Adapter\DbBase;

class SubsidioController extends ApplicationController
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

    public function indexAction()
    {
        $this->setParamToView("title", "Subsidio");
    }

    public function historialAction()
    {
        $help = "Esta opcion permite manejar los ";
        $this->setParamToView("help", $help);
        $this->setParamToView("title", "Historial");
        Tag::setDocumentTitle('Historial');
        $mercurio32 = $this->Mercurio32->find(
            "tipo='" . parent::getActUser("tipo") . "' and coddoc='" .
                parent::getActUser("coddoc") . "' and documento='" .
                parent::getActUser("documento") . "'",
            "order: id DESC"
        );
        $mercurio33 = $this->Mercurio33->find(
            "tipo='" . parent::getActUser("tipo") . "' and coddoc='" .
                parent::getActUser("coddoc") . "' and documento='" .
                parent::getActUser("documento") . "'",
            "order: id DESC"
        );
        $mercurio34 = $this->Mercurio34->find(
            "tipo='" . parent::getActUser("tipo") . "' and coddoc='" .
                parent::getActUser("coddoc") . "' and documento='" .
                parent::getActUser("documento") . "'",
            "order: id DESC"
        );
        $mercurio45 = $this->Mercurio45->find(
            "tipo='" . parent::getActUser("tipo") . "' and coddoc='" .
                parent::getActUser("coddoc") . "' and documento='" .
                parent::getActUser("documento") . "'",
            "order: id DESC"
        );

        $actualizacion_basico  = "<table class='table table-hover align-items-center table-bordered'>";
        $actualizacion_basico .= "<thead>";
        $actualizacion_basico .= "<tr>";
        $actualizacion_basico .= "<th scope='col'>Campo</th>";
        $actualizacion_basico .= "<th scope='col'>Valor Anterior </th>";
        $actualizacion_basico .= "<th scope='col'>Valor Nuevo</th>";
        $actualizacion_basico .= "<th scope='col'>Estado</th>";
        $actualizacion_basico .= "<th scope='col'>Fecha Estado</th>";
        $actualizacion_basico .= "<th scope='col'>Motivo</th>";
        $actualizacion_basico .= "</tr>";
        $actualizacion_basico .= "</thead>";
        $actualizacion_basico .= "<tbody class='list'>";
        if ($mercurio33->count() == 0) {
            $actualizacion_basico .= "<tr align='center'>";
            $actualizacion_basico .= "<td colspan=6><p>No hay datos para mostrar</p></td>";
            $actualizacion_basico .= "<tr>";
            $actualizacion_basico .= "</tr>";
        } else {
            foreach ($mercurio33 as $mmercurio33) {
                $mmercurio28 = (new Mercurio28)->findFirst("campo='{$mmercurio33->getCampo()}'");
                if (!$mmercurio28) continue;
                $actualizacion_basico .= "<tr>";
                $actualizacion_basico .= "<td>{$mmercurio28->getDetalle()}</td>";
                $actualizacion_basico .= "<td>{$mmercurio33->getAntval()}</td>";
                $actualizacion_basico .= "<td>{$mmercurio33->getValor()}</td>";
                $actualizacion_basico .= "<td>{$mmercurio33->getEstadoDetalle()}</td>";
                $actualizacion_basico .= "<td>{$mmercurio33->getFecest()}</td>";
                $actualizacion_basico .= "<td>{$mmercurio33->getMotivo()}</td>";
                $actualizacion_basico .= "</tr>";
            }
        }

        $actualizacion_basico .= "</tbody>";
        $actualizacion_basico .= "</table>";

        $html_afiliacion_conyuge  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_afiliacion_conyuge .= "<thead>";
        $html_afiliacion_conyuge .= "<tr>";
        $html_afiliacion_conyuge .= "<th scope='col'>Cedula</th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Nombre </th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Estado</th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Fecha Estado</th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Motivo</th>";
        $html_afiliacion_conyuge .= "</tr>";
        $html_afiliacion_conyuge .= "</thead>";
        $html_afiliacion_conyuge .= "<tbody class='list'>";
        if (count($mercurio32) == 0) {
            $html_afiliacion_conyuge .= "<tr align='center'>";
            $html_afiliacion_conyuge .= "<td colspan=5><p>No hay datos para mostrar</p></td>";
            $html_afiliacion_conyuge .= "<tr>";
            $html_afiliacion_conyuge .= "</tr>";
        }
        foreach ($mercurio32 as $mmercurio32) {
            $html_afiliacion_conyuge .= "<tr>";
            $html_afiliacion_conyuge .= "<td>{$mmercurio32->getCedcon()}</td>";
            $html_afiliacion_conyuge .= "<td>{$mmercurio32->getPriape()} {$mmercurio32->getPrinom()}</td>";
            $html_afiliacion_conyuge .= "<td>{$mmercurio32->getEstadoDetalle()}</td>";
            $html_afiliacion_conyuge .= "<td>{$mmercurio32->getFecest()}</td>";
            $html_afiliacion_conyuge .= "<td>{$mmercurio32->getMotivo()}</td>";
            $html_afiliacion_conyuge .= "</tr>";
        }
        $html_afiliacion_conyuge .= "</tbody>";
        $html_afiliacion_conyuge .= "</table>";

        $html_afiliacion_beneficiario  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_afiliacion_beneficiario .= "<thead>";
        $html_afiliacion_beneficiario .= "<tr>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Documento</th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Nombre </th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Estado</th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Fecha Estado</th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Motivo</th>";
        $html_afiliacion_beneficiario .= "</tr>";
        $html_afiliacion_beneficiario .= "</thead>";
        $html_afiliacion_beneficiario .= "<tbody class='list'>";
        if (count($mercurio34) == 0) {
            $html_afiliacion_beneficiario .= "<tr align='center'>";
            $html_afiliacion_beneficiario .= "<td colspan=5><p>No hay datos para mostrar</p></td>";
            $html_afiliacion_beneficiario .= "<tr>";
            $html_afiliacion_beneficiario .= "</tr>";
        }
        foreach ($mercurio34 as $mmercurio34) {
            $html_afiliacion_beneficiario .= "<tr>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getNumdoc()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getPriape()} {$mmercurio34->getPrinom()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getEstadoDetalle()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getFecest()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getMotivo()}</td>";
            $html_afiliacion_beneficiario .= "</tr>";
        }
        $html_afiliacion_beneficiario .= "</tbody>";
        $html_afiliacion_beneficiario .= "</table>";

        $html_certificados  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_certificados .= "<thead>";
        $html_certificados .= "<tr>";
        $html_certificados .= "<th scope='col'>Beneficiario</th>";
        $html_certificados .= "<th scope='col'>Certificado</th>";
        $html_certificados .= "<th scope='col'>Estado</th>";
        $html_certificados .= "<th scope='col'>Fecha Estado</th>";
        $html_certificados .= "<th scope='col'>Motivo</th>";
        $html_certificados .= "</tr>";
        $html_certificados .= "</thead>";
        $html_certificados .= "<tbody class='list'>";
        if (count($mercurio45) == 0) {
            $html_certificados .= "<tr align='center'>";
            $html_certificados .= "<td colspan=5><p>No hay datos para mostrar</p></td>";
            $html_certificados .= "<tr>";
            $html_certificados .= "</tr>";
        }
        foreach ($mercurio45 as $mmercurio45) {
            $html_certificados .= "<tr>";
            $html_certificados .= "<td>{$mmercurio45->getNombre()}</td>";
            $html_certificados .= "<td>{$mmercurio45->getNomcer()}</td>";
            $html_certificados .= "<td>{$mmercurio45->getEstadoDetalle()}</td>";
            $html_certificados .= "<td>{$mmercurio45->getFecest()}</td>";
            $html_certificados .= "<td>{$mmercurio45->getMotivo()}</td>";
            $html_certificados .= "</tr>";
        }
        $html_certificados .= "</tbody>";
        $html_certificados .= "</table>";



        $this->setParamToView("actualizacion_basico", $actualizacion_basico);
        $this->setParamToView("html_afiliacion_beneficiario", $html_afiliacion_beneficiario);
        $this->setParamToView("html_afiliacion_conyuge", $html_afiliacion_conyuge);
        $this->setParamToView("html_certificados", $html_certificados);
    }

    /**
     * consulta_nucleoAction function
     * @param string $cedtra
     * @return void
     */
    public function consulta_nucleo_viewAction($cedtra = '')
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("help", false);
        $this->setParamToView("title", "Consulta nucleo familiar");
        $this->setParamToView("cedtra", $cedtra);
    }

    public function consulta_nucleoAction()
    {
        Core::importLibrary("ParamsTrabajador", "Collections");
        Core::importLibrary("ParamsConyuge", "Collections");
        Core::importLibrary("ParamsBeneficiario", "Collections");

        $this->setResponse("ajax");
        $cedtra = parent::getActUser("documento");
        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "PoblacionAfiliada",
                "metodo" => "nucleo_familiar_trabajador",
                "params" => array(
                    "cedtra" => $cedtra
                )
            )
        );

        $out = $ps->toArray();
        if (!$out['success']) {
            $dataTrabajador = array();
            $dataConyuges = array();
            $dataBeneficiarios = array();
        } else {
            $dataTrabajador = $out['data']['trabajador'];
            $dataConyuges = $out['data']['conyuges'];
            $dataBeneficiarios = $out['data']['beneficiarios'];
        }

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo"  => "parametros_trabajadores",
            )
        );
        $paramsTrabajador = new ParamsTrabajador();
        $paramsTrabajador->setDatosCaptura($ps->toArray());


        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo"  => "parametros_conyuges",
            )
        );
        $paramsConyuge = new ParamsConyuge();
        $paramsConyuge->setDatosCaptura($ps->toArray());

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo"  => "parametros_beneficiarios",
            )
        );
        $paramsBeneficiario = new ParamsBeneficiario();
        $paramsBeneficiario->setDatosCaptura($ps->toArray());

        $this->renderObject(
            array(
                "success" => true,
                "data" => array(
                    "trabajador" => $dataTrabajador,
                    "conyuges" => $dataConyuges,
                    "beneficiarios" => $dataBeneficiarios,
                    "params" => array(
                        "_coddoc" => ParamsTrabajador::getTiposDocumentos(),
                        "_sexo" => ParamsTrabajador::getSexos(),
                        "_estciv" => ParamsTrabajador::getEstadoCivil(),
                        "_cabhog" => ParamsTrabajador::getCabezaHogar(),
                        "_codciu" => ParamsTrabajador::getCiudades(),
                        "_codzon" => ParamsTrabajador::getZonas(),
                        "_captra" => ParamsTrabajador::getCapacidadTrabajar(),
                        "_tipdis" => ParamsTrabajador::getTipoDiscapacidad(),
                        "_nivedu" => ParamsTrabajador::getNivelEducativo(),
                        "_tippag" => ParamsTrabajador::getTipoPago(),
                        "_rural" => ParamsTrabajador::getRural(),
                        "_tipcon" => ParamsTrabajador::getTipoContrato(),
                        "_trasin" => ParamsTrabajador::getSindicalizado(),
                        "_vivienda" => ParamsTrabajador::getVivienda(),
                        "_tipafi" => ParamsTrabajador::getTipoAfiliado(),
                        "_estado" => ParamsTrabajador::getEstados(),
                        "_comper" => ParamsConyuge::getCompaneroPermanente(),
                        "_parent" => ParamsBeneficiario::getParentesco(),
                        "_huerfano" => ParamsBeneficiario::getHuerfano(),
                        "_tiphij" => ParamsBeneficiario::getTipoHijo(),
                        "_calendario" => ParamsBeneficiario::getCalendario(),
                        "_huerfano" => ParamsBeneficiario::getHuerfano(),
                        "_tiphij" => ParamsBeneficiario::getTipoHijo(),
                        "_calendario" => ParamsBeneficiario::getCalendario(),
                        '_codcat' => ParamsTrabajador::getCategoria(),
                    )
                )
            )
        );
    }

    public function consulta_giro_viewAction()
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("help", false);
        $this->setParamToView("title", "Consulta Giro");
        Tag::setDocumentTitle('Consulta Giro');
    }

    public function consulta_giroAction()
    {
        $this->setResponse("ajax");
        try {

            $perini = $request->input('perini');
            $perfin = $request->input('perfin');
            $params['cedtra'] = parent::getActUser("documento");
            $params['perini'] = $perini;
            $params['perfin'] = $perfin;

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "CuotaMonetaria",
                    "metodo" => "cuotas_by_trabajador",
                    "params" => array(
                        "post" => $params
                    )
                )
            );

            $out = $ps->toArray();
            if (!$out['success']) {
                $response = array(
                    "success" => false,
                    "message" => $out['message']
                );
            } else {
                $response = array(
                    "success" => true,
                    "data" => $out['data']
                );
            }
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "message" => $e->getMessage()
            );
        }
        return $this->renderObject($response);
    }

    public function consulta_no_giro_viewAction()
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("help", false);
        $this->setParamToView("title", "Consulta No Giro");
        Tag::setDocumentTitle('Consulta No Giro');
    }

    public function consulta_no_giroAction()
    {
        $this->setResponse("ajax");
        try {
            $perini = $request->input('perini');
            $perfin = $request->input('perfin');
            $params['cedtra'] = parent::getActUser("documento");
            $params['perini'] = $perini;
            $params['perfin'] = $perfin;

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "CuotaMonetaria",
                    "metodo" => "nogiro_by_trabajador",
                    "params" => array(
                        "post" => $params
                    )
                )
            );

            $out = $ps->toArray();
            if (!$out['success']) {
                $response = array(
                    "success" => false,
                    "message" => $out['message']
                );
            } else {
                $response = array(
                    "success" => true,
                    "data" => $out['data']
                );
            }
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "message" => $e->getMessage()
            );
        }
        return $this->renderObject($response);
    }

    public function consulta_planilla_trabajador_viewAction()
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("help", false);
        $this->setParamToView("title", "Planillas Pila");
        Tag::setDocumentTitle('Planillas Pila');
    }

    public function consulta_planilla_trabajadorAction()
    {
        $this->setResponse("ajax");
        try {
            $perini = $request->input('perini');
            $perfin = $request->input('perfin');
            $params['cedtra'] = parent::getActUser("documento");
            $params['perini'] = $perini;
            $params['perfin'] = $perfin;

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "AportesEmpresas",
                    "metodo" => "planilla_trabajador",
                    "params" => array(
                        "post" => $params
                    )
                )
            );
            $out = $ps->toArray();
            if (!$out['success']) {
                $response = array(
                    "success" => false,
                    "message" => $out['message']
                );
            } else {
                $response = array(
                    "success" => true,
                    "data" => $out['data']
                );
            }
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "message" => $e->getMessage()
            );
        }
        return $this->renderObject($response);
    }

    public function consulta_tarjetaAction()
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("help", false);
        $this->setParamToView("title", "Consulta Saldos");
        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "CuotaMonetaria",
                "metodo" => "saldo_pendiente_cobrar_trabajador",
                "params" =>  array(
                    "cedtra" => parent::getActUser("documento")
                )
            )
        );
        $out = $ps->toArray();
        if ($out['success'] == false) {
            $response = $out;
        } else {
            $response = $out['data'];
        }
        $this->setParamToView("saldos", $response);
    }

    public function certificado_afiliacion_viewAction()
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("help", false);
        $this->setParamToView("title", "Certificado Afiliacion");
        $this->setParamToView('tipo', array(
            "A" => "Certificado Afiliacion Principal",
            "I" => "Certificacion Con Nucleo",
            "T" => "Certificacion de Multiafiliacion",
            "P" => "Reporte trabajador en planillas"
        ));
        Tag::setDocumentTitle('Certificado Afiliacion');
    }

    public function certificado_afiliacionAction()
    {
        $generalService = new GeneralService();
        $tipo = $request->input("tipo");
        $generalService->registrarLog(false, "Certificado De Afiliacion", $tipo);
        header("Location: https://comfacaenlinea.com.co/SYS/Subsidio/subflo/gene_certi_tra/$tipo/" . parent::getActUser("documento"));
    }
}
