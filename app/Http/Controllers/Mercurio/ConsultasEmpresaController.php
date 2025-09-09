<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Auth\AuthJwt;
use App\Library\Auth\SessionCookies;
use App\Library\Collections\ParamsBeneficiario;
use App\Library\Collections\ParamsConyuge;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio10;
use App\Models\Mercurio14;
use App\Models\Mercurio28;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio33;
use App\Models\Mercurio34;
use App\Models\Mercurio35;
use App\Models\Mercurio37;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\Logger;
use App\Services\Utils\UploadFile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConsultasEmpresaController extends ApplicationController
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
        return view("mercurio/subsidioemp/index", [
            "title" => "Subsidio Empresa"
        ]);
    }

    public function historialAction()
    {
        $tipo = $this->tipo;
        $coddoc = $this->user['coddoc'];
        $documento = $this->user['documento'];

        $mercurio31 = Mercurio31::where('nit', $documento)
            ->orderBy('id', 'desc');

        $mercurio33 = Mercurio33::where([
            ['tipo', $tipo],
            ['coddoc', $coddoc],
            ['documento', $documento]
        ])->orderBy('id', 'desc');

        $mercurio35 = Mercurio35::where('nit', $documento)
            ->orderBy('id', 'desc');

        $mercurio32 = Mercurio32::where([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
        ])->orderBy('id', 'desc');

        $mercurio34 = Mercurio34::where([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
        ])->orderBy('id', 'desc');

        $html_afiliacion  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_afiliacion .= "<thead>";
        $html_afiliacion .= "<tr>";
        $html_afiliacion .= "<th scope='col'>Cedula</th>";
        $html_afiliacion .= "<th scope='col'>Nombre </th>";
        $html_afiliacion .= "<th scope='col'>Fecha de Solicitud</th>";
        $html_afiliacion .= "<th scope='col'>Estado</th>";
        $html_afiliacion .= "<th scope='col'>Fecha de Estado</th>";
        $html_afiliacion .= "<th scope='col'>Motivo</th>";
        $html_afiliacion .= "</tr>";
        $html_afiliacion .= "</thead>";
        $html_afiliacion .= "<tbody class='list'>";
        if ($mercurio31->count() == 0) {
            $html_afiliacion .= "<tr align='center'>";
            $html_afiliacion .= "<td colspan=6><label>No hay datos para mostrar</label></td>";
            $html_afiliacion .= "<tr>";
            $html_afiliacion .= "</tr>";
        }
        foreach ($mercurio31->get() as $mmercurio31) {
            $html_afiliacion .= "<tr>";
            $html_afiliacion .= "<td>{$mmercurio31->getCedtra()}</td>";
            $html_afiliacion .= "<td>{$mmercurio31->getPriape()} {$mmercurio31->getPrinom()}</td>";
            $html_afiliacion .= "<td>{$mmercurio31->getFecsol()}</td>";
            $html_afiliacion .= "<td>{$mmercurio31->getEstadoDetalle()}</td>";
            $html_afiliacion .= "<td>{$mmercurio31->getFecest()}</td>";
            $html_afiliacion .= "<td>{$mmercurio31->getMotivo()}</td>";
            $html_afiliacion .= "</tr>";
        }
        $html_afiliacion .= "</tbody>";
        $html_afiliacion .= "</table>";

        $html_retiro  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_retiro .= "<thead>";
        $html_retiro .= "<tr>";
        $html_retiro .= "<th scope='col'>Cedula</th>";
        $html_retiro .= "<th scope='col'>Nombre </th>";
        $html_retiro .= "<th scope='col'>Estado</th>";
        $html_retiro .= "<th scope='col'>Fecha Estado</th>";
        $html_retiro .= "<th scope='col'>Motivo</th>";
        $html_retiro .= "</tr>";
        $html_retiro .= "</thead>";
        $html_retiro .= "<tbody class='list'>";
        if ($mercurio35->count() == 0) {
            $html_retiro .= "<tr align='center'>";
            $html_retiro .= "<td colspan=5><label>No hay datos para mostrar</label></td>";
            $html_retiro .= "<tr>";
            $html_retiro .= "</tr>";
        }
        foreach ($mercurio35->get() as $mmercurio35) {
            $html_retiro .= "<tr>";
            $html_retiro .= "<td>{$mmercurio35->getCedtra()}</td>";
            $html_retiro .= "<td>{$mmercurio35->getNomtra()}</td>";
            $html_retiro .= "<td>{$mmercurio35->getEstadoDetalle()}</td>";
            $html_retiro .= "<td>{$mmercurio35->getFecest()}</td>";
            $html_retiro .= "<td>{$mmercurio35->getMotivo()}</td>";
            $html_retiro .= "</tr>";
        }
        $html_retiro .= "</tbody>";
        $html_retiro .= "</table>";


        $html_afiliacion_conyuge  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_afiliacion_conyuge .= "<thead>";
        $html_afiliacion_conyuge .= "<tr>";
        $html_afiliacion_conyuge .= "<th scope='col'>Cedula Trabajador</th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Cedula</th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Nombre </th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Estado</th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Fecha Estado</th>";
        $html_afiliacion_conyuge .= "<th scope='col'>Motivo</th>";
        $html_afiliacion_conyuge .= "</tr>";
        $html_afiliacion_conyuge .= "</thead>";
        $html_afiliacion_conyuge .= "<tbody class='list'>";

        if ($mercurio32->count() == 0) {
            $html_afiliacion_conyuge .= "<tr align='center'>";
            $html_afiliacion_conyuge .= "<td colspan=6><label>No hay datos para mostrar</label></td>";
            $html_afiliacion_conyuge .= "<tr>";
            $html_afiliacion_conyuge .= "</tr>";
        } else {

            if ($mercurio32) {
                foreach ($mercurio32->get() as $mmercurio32) {
                    $html_afiliacion_conyuge .= "<tr>";
                    $html_afiliacion_conyuge .= "<td>{$mmercurio32->getCedtra()}</td>";
                    $html_afiliacion_conyuge .= "<td>{$mmercurio32->getCedcon()}</td>";
                    $html_afiliacion_conyuge .= "<td>{$mmercurio32->getPriape()} {$mmercurio32->getPrinom()}</td>";
                    $html_afiliacion_conyuge .= "<td>{$mmercurio32->getEstadoDetalle()}</td>";
                    $html_afiliacion_conyuge .= "<td>{$mmercurio32->getFecest()}</td>";
                    $html_afiliacion_conyuge .= "<td>{$mmercurio32->getMotivo()}</td>";
                    $html_afiliacion_conyuge .= "</tr>";
                }
            }
        }


        $html_afiliacion_conyuge .= "</tbody>";
        $html_afiliacion_conyuge .= "</table>";

        $html_afiliacion_beneficiario  = "<table class='table table-hover align-items-center table-bordered'>";
        $html_afiliacion_beneficiario .= "<thead>";
        $html_afiliacion_beneficiario .= "<tr>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Cedula Trabajador</th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Documento</th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Nombre </th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Estado</th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Fecha Estado</th>";
        $html_afiliacion_beneficiario .= "<th scope='col'>Motivo</th>";
        $html_afiliacion_beneficiario .= "</tr>";
        $html_afiliacion_beneficiario .= "</thead>";
        $html_afiliacion_beneficiario .= "<tbody class='list'>";
        if ($mercurio34->count() == 0) {
            $html_afiliacion_beneficiario .= "<tr align='center'>";
            $html_afiliacion_beneficiario .= "<td colspan=6><label>No hay datos para mostrar</label></td>";
            $html_afiliacion_beneficiario .= "<tr>";
            $html_afiliacion_beneficiario .= "</tr>";
        }
        foreach ($mercurio34->get() as $mmercurio34) {
            $html_afiliacion_beneficiario .= "<tr>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getCedtra()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getNumdoc()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getPriape()} {$mmercurio34->getPrinom()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getEstadoDetalle()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getFecest()}</td>";
            $html_afiliacion_beneficiario .= "<td>{$mmercurio34->getMotivo()}</td>";
            $html_afiliacion_beneficiario .= "</tr>";
        }
        $html_afiliacion_beneficiario .= "</tbody>";
        $html_afiliacion_beneficiario .= "</table>";



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
            $actualizacion_basico .= "<td colspan=6><label>No hay datos para mostrar</label></td>";
            $actualizacion_basico .= "<tr>";
            $actualizacion_basico .= "</tr>";
        }
        foreach ($mercurio33->get() as $mmercurio33) {
            $mmercurio28 = Mercurio28::where('campo', $mmercurio33->campo)->first();
            $actualizacion_basico .= "<tr>";
            $actualizacion_basico .= "<td>{$mmercurio28->getDetalle()}</td>";
            $actualizacion_basico .= "<td>{$mmercurio33->antval}</td>";
            $actualizacion_basico .= "<td>{$mmercurio33->valor}</td>";
            $actualizacion_basico .= "<td>{$mmercurio33->getEstadoDetalle()}</td>";
            $actualizacion_basico .= "<td>{$mmercurio33->fecest}</td>";
            $actualizacion_basico .= "<td>{$mmercurio33->motivo}</td>";
            $actualizacion_basico .= "</tr>";
        }
        $actualizacion_basico .= "</tbody>";
        $actualizacion_basico .= "</table>";

        return view("mercurio/subsidioemp/historial", [
            "hide_header" => true,
            "help" => false,
            "title" => "Historial",
            "html_afiliacion" => $html_afiliacion,
            "html_retiro" => $html_retiro,
            "html_afiliacion_beneficiario" => $html_afiliacion_beneficiario,
            "html_afiliacion_conyuge" => $html_afiliacion_conyuge,
            "actualizacion_basico" => $actualizacion_basico
        ]);
    }

    public function consultaTrabajadoresViewAction()
    {
        return view("mercurio/subsidioemp/consulta_trabajadores", [
            "hide_header" => true,
            "help" => false,
            "title" => "Consulta Trabajadores",
            'documento' => $this->user['documento'],
            'coddoc' => $this->user['coddoc'],
            'tipo' => $this->tipo
        ]);
    }

    public function consultaTrabajadoresAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $estado = $request->input("estado");
            $nit = $request->input("nit") ? $request->input("nit") : $this->user['documento'];
            $estado = $estado == "T" ? "" : $estado;

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "listar_trabajadores",
                    "params" => array(
                        "nit" => $nit,
                        "estado" => $estado
                    )
                )
            );

            $out = $ps->toArray();
            if (!$out['success'] || count($out['data']) == 0) {
                throw new DebugException("Error no hay respuesta del servidor SISU", 501);
            }

            $html = view(
                'mercurio/subsidioemp/tmp/tmp_afiliados',
                array('trabajadores' => $out['data'])
            )->render();

            $response = array(
                'flag' => true,
                'success' => true,
                'data' => $html
            );
        } catch (DebugException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage() . ' ' . $e->getLine()
            );
        }
        return $this->renderObject($response, false);
    }

    public function consultaGiroViewAction()
    {
        return view("mercurio/subsidioemp/consulta_giro", [
            "hide_header" => true,
            "help" => false,
            "title" => "Consulta Giro"
        ]);
    }

    public function consultaGiroAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $nit = $this->user['documento'];
            $perini = $request->input('perini');
            $perfin = $request->input('perfin');

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "CuotaMonetaria",
                    "metodo" => "cuotas_by_empresa_and_periodo",
                    "params" => array(
                        "post" => array(
                            "nit" => $nit,
                            "perini" => $perini,
                            "perfin" => $perfin
                        )
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

    public function consultaNominaViewAction()
    {
        return view("mercurio/subsidioemp/consulta_nomina", [
            "hide_header" => true,
            "help" => false,
            "title" => "Consulta Nomina"
        ]);
    }

    public function consultaNominaAction(Request $request)
    {
        try {
            $periodo = $request->input("periodo");
            $nit = $this->user['documento'];

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "AportesEmpresas",
                    'metodo' => 'nomina_by_nit_and_periodo',
                    "params" =>  array(
                        "nit" => $nit,
                        "periodo" => $periodo
                    )
                )
            );

            $out = $ps->toArray();
            if (!$out['success'] || count($out['data']) == 0) {
                throw new DebugException("Error no hay respuesta del servidor SISU", 501);
            }

            $html = view(
                'mercurio/subsidioemp/tmp/tmp_nomina',
                array('nominas' => $out['data'])
            )->render();


            $response = array(
                'flag' => true,
                'success' => true,
                'data' => $html
            );
        } catch (DebugException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage() . ' ' . $e->getLine()
            );
        }
        return $this->renderObject($response);
    }

    public function consultaAportesViewAction()
    {
        return view("mercurio/subsidioemp/consulta_aportes", [
            "hide_header" => true,
            "help" => false,
            "title" => "Consulta Aportes"
        ]);
    }

    public function consultaAportesAction(Request $request)
    {
        $this->setResponse("ajax");
        try {

            $perini = $request->input("perini");
            $perfin = $request->input("perfin");
            $nit = $this->user['documento'];

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "AportesEmpresas",
                    'metodo' => 'aportes_by_nit_and_periodos',
                    "params" => array(
                        "nit" => $nit,
                        "perini" => $perini,
                        "perfin" => $perfin
                    )
                )
            );

            $out = $ps->toArray();
            if (!$out['success'] || count($out['data']) == 0) {
                throw new DebugException("Error no hay respuesta del servidor SISU", 501);
            }

            $html = view(
                'mercurio/subsidioemp/tmp/tmp_aportes',
                array('aportes' => $out['data'])
            )->render();

            $response = array(
                'flag' => true,
                'success' => true,
                'data' => $html
            );
        } catch (DebugException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage() . ' ' . $e->getLine()
            );
        }
        return $this->renderObject($response, false);
    }

    public function consulta_mora_presuntaAction()
    {
        return view("mercurio/subsidioemp/consulta_mora_presunta", [
            "hide_header" => true,
            "help" => false,
            "title" => "Consulta Mora Presunta"
        ]);
    }

    public function mora_presuntaAction()
    {
        $this->setResponse("ajax");
        try {
            $nit = parent::getActUser("documento");
            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "AportesEmpresas",
                    'metodo' => 'mora_presunta_by_nit',
                    "params" => array(
                        "nit" => $nit
                    )
                )
            );
            $consulta  = $ps->toArray();
            if (!$consulta['success']) {
                throw new DebugException($consulta['msj']);
            }

            $ps = Comman::Api();
            $ps->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "buscar_sucursales_en_empresa",
                    "params" => $nit
                )
            );
            $out  = $ps->toArray();
            if (!$out['success']) {
                throw new DebugException($out['msj']);
            }
            $sucursales = array();
            foreach ($out['data'] as $sucursal) {
                $sucursales[] = array(
                    'codsuc' => $sucursal['codsuc'],
                    'detalle' => $sucursal['detalle'],
                    'codzon' => $sucursal['codzon']
                );
            }

            $data = $consulta['data'];
            $salida = array(
                'success' => true,
                'data' => array(
                    'cartera' => $data['moras'],
                    'periodos' => $data['periodos'],
                    'sucursales' => $sucursales,
                )
            );
        } catch (DebugException $e) {
            $salida = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function novedad_retiro_viewAction()
    {

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "motivosRechazo",
                "params" => array()
            )
        );
        $out = $ps->toArray();
        if (!$out['success']) {
            throw new DebugException($out['msj']);
        }

        $_codest = array();
        foreach ($out['data'] as $mcodest) {
            $_codest[$mcodest['codest']] = $mcodest['detalle'];
        }
        return view("mercurio/subsidioemp/novedad_retiro", [
            "hide_header" => true,
            "help" => false,
            "title" => "Novedad Retiro",
            "codest" => $_codest
        ]);
    }

    public function buscar_trabajadorAction(Request $request)
    {
        try {
            $cedtra = $request->input("cedtra");

            $ps = Comman::Api();
            $ps->runCli(array(
                "servicio" => "PoblacionAfiliada",
                "metodo" => "datosTrabajador",
                "params" => array("cedtra" => $cedtra)
            ));

            $out = $ps->toArray();
            if (!$out['success']) {
                return $this->renderObject(array('flag' => false, 'success' => false, 'msj' => $out['msj']));
            }

            $subsi15 = $out['data'];
            if (count($subsi15) == 0) {
                return $this->renderObject(array('flag' => false, 'success' => false, 'msj' => "No Existe la cedula dada"));
            }

            if ($subsi15['nit'] != parent::getActUser("documento")) {
                return $this->renderObject(array('flag' => false, 'success' => false, 'msj' => "el trabajador no esta registrado a su empresa"));
            }

            return $this->renderObject(array('flag' => true, 'success' => true, 'data' => $subsi15));
        } catch (DebugException $e) {
            return $this->renderObject(
                array('flag' => false, 'success' => false, 'msj' => $e->getMessage())
            );
        }
    }

    public function novedad_retiroAction(Request $request)
    {
        try {

            $this->setResponse("ajax");
            $cedtra = $request->input('cedtra', "addslaches", "alpha", "extraspaces", "striptags");
            $nombre = $request->input('nombre', "addslaches", "alpha", "extraspaces", "striptags");
            $codest = $request->input('codest', "addslaches", "alpha", "extraspaces", "striptags");
            $fecafi = $request->input('fecafi', "addslaches", "extraspaces", "striptags");
            $fecret = $request->input('fecret', "addslaches", "extraspaces", "striptags");
            $nota = $request->input('nota', "addslaches", "alpha", "extraspaces", "striptags");
            $today = Carbon::now();

            if (Carbon::compareDates($fecafi, $fecret) > 0) {
                $response = "La fecha de retiro no puede ser menor a la de afiliacion";
                return $this->renderObject($response);
            }
            if (Carbon::compareDates($fecret, $today) > 0) {
                $response = "La fecha de retiro no puede ser mayor a la de hoy";
                return $this->renderObject($response);
            }
            $today = Carbon::now();
            $modelos = array("mercurio08", "mercurio10", "mercurio20", "Mercurio35");
            #$Transaccion = parent::startTrans($modelos);
            #$response = parent::startFunc();
            $logger = new Logger();
            $id_log = $logger->registrarLog(true, "retiro trabajador", "");
            $mercurio35 = new Mercurio35();
            #$mercurio35->setTransaction($Transaccion);
            $mercurio35->setId(0);
            $mercurio35->setLog($id_log);
            $mercurio35->setNit(parent::getActUser("documento"));
            $mercurio35->setTipdoc(parent::getActUser("coddoc"));
            $mercurio35->setCedtra($cedtra);
            $mercurio35->setNomtra($nombre);
            $mercurio35->setCodest($codest);
            $mercurio35->setFecret($fecret);
            $mercurio35->setNota($nota);
            $mercurio35->setEstado("P");
            $asignarFuncionario = new AsignarFuncionario();
            $usuario = $asignarFuncionario->asignar("7", $this->user['codciu']);

            if ($usuario == "") {
                $response = "No se puede realizar el registro,Comuniquese con la Atencion al cliente";
                return $this->renderObject($response);
            }
            $mercurio35->setUsuario($usuario);
            $mercurio35->setTipo($this->tipo);

            $mercurio35->setCoddoc($this->user['coddoc']);
            $mercurio35->setDocumento($this->user['documento']);
            $mercurio01 = Mercurio01::first();

            if (isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] != "") {
                $extension = explode(".", $_FILES['archivo']['name']);
                $name = "{$mercurio35->getNit()}_{$mercurio35->getCedtra()}_{$id_log}_retiro." . end($extension);
                $_FILES['archivo']['name'] = $name;

                $estado = UploadFile::upload("archivo", $mercurio01->getPath());
                if ($estado != false) {
                    $mercurio35->setArchivo($name);
                    $mercurio35->save();

                    $item = Mercurio10::where('tipopc', '7')
                        ->where('numero', $mercurio35->getId())
                        ->max('item') + 1;

                    $mercurio10 = new Mercurio10();
                    #$mercurio10->setTransaction($Transaccion);
                    $mercurio10->setTipopc("7");
                    $mercurio10->setNumero($mercurio35->getId());
                    $mercurio10->setItem($item);
                    $mercurio10->setEstado("P");
                    $mercurio10->setNota("Envio a la Caja para Verificacion");
                    $mercurio10->setFecsis($today->format('Y-m-d'));
                    $mercurio10->save();

                    $response = "Se adjunto con exito el archivo";
                } else {
                    $response = "No se cargo: Tamano del archivo muy grande o No es Valido";
                }
            } else {
                $response = "No se cargo el archivo";
            }
            $asunto = "Retiro Trabajador";
            $msj  = "acabas de utilizar nuestra opcion de retirar un trabajador. Nuestro Equipo revisara la novedad";
            $generalService = new GeneralService();
            $generalService->sendEmail(parent::getActUser("email"), parent::getActUser("nombre"), $asunto, $msj, "");

            #parent::finishTrans();
            $salida = array('success' => true, 'msj' => $response);
        } catch (DebugException $e) {
            $salida = array('success' => false, 'msj' => $e->getMessage());
        }
        return $this->renderObject(
            $salida
        );
    }

    /**
     * actualiza_datos_basicos_viewAction function
     * @return void
     */
    public function actualiza_datos_basicos_viewAction()
    {
        set_flashdata("error", array(
            "msj" => "El modulo de actualización de datos está en mantenimiento.",
            "code" => '505'
        ));
        redirect()->route("principal.index");
        exit;

        $mercurio28 = Mercurio28::where('tipo', parent::getActUser("tipo"))->orderBy('orden')->get();
        foreach ($mercurio28 as $mmercurio28) {
            $campos[$mmercurio28->getCampo()] = $mmercurio28->getDetalle();
        }

        $this->load_parametros_view();
        $documento = parent::getActUser('documento');
        $solicitante = Mercurio30::where('documento', $documento)->first();

        $empresa = false;
        $rqs = $this->buscarEmpresaSubsidio($solicitante->getNit());
        if ($rqs) {

            $empresa = (count($rqs['data']) > 0) ? $rqs['data'] : false;
        }
        $mercurio14 = Mercurio14::where('tipopc', '5')->get();

        return view("mercurio/subsidioemp/tmp/actualiza_datos_basicos", [
            "path" => base_path(),
            "empresa" => $empresa,
            "archivos_adjuntar" => $mercurio14,
            "datosBasicos" => $empresa,
            "title" => "Solicitud actualizar datos basicos"
        ]);
    }

    public function actualiza_datos_basicosAction(Request $request)
    {
        $cedtra = $request->input('cedtra', "addslaches", "alpha", "extraspaces", "striptags");
        $modelos = array("mercurio08", "mercurio10", "mercurio20", "mercurio33", "mercurio37");
        #$Transaccion = parent::startTrans($modelos);
        # $response = parent::startFunc();
        $today = Carbon::now();

        $flag_email = false;
        $logger = new Logger();
        $id_log = $logger->registrarLog(true, "actualización datos basicos", "");
        $mercurio28 = Mercurio28::where('tipo', parent::getActUser("tipo"))->get();

        if (parent::getActUser("tipo") == 'T') {
            $tipopc = 14;
        } else {
            $tipopc = 5;
        }
        $asignarFuncionario = new AsignarFuncionario();
        $usuario = $asignarFuncionario->asignar($tipopc, $this->user['codciu']);
        if ($usuario == "") {
            return $this->renderObject(['success' => false, 'msj' => 'No se puede realizar el registro, comuniquese con la atención al cliente']);
        }
        foreach ($mercurio28 as $mmercurio28) {
            $antval = $request->input($mmercurio28->getCampo() . "_ant");
            $valor = $request->input($mmercurio28->getCampo());
            $cedrep = $request->input($mmercurio28->getCampo());
            $repleg = $request->input($mmercurio28->getCampo());
            if ($valor == "") continue;
            if ($antval == "") continue;
            if ($cedrep == "") continue;
            if ($repleg == "") continue;
            if ($antval == $valor) continue;
            $mercurio33 = new Mercurio33();
            #$mercurio33->setTransaction($Transaccion);
            $mercurio33->setId(0);
            $mercurio33->setLog($id_log);
            $mercurio33->setTipo(parent::getActUser("tipo"));
            $mercurio33->setCoddoc(parent::getActUser("coddoc"));
            $mercurio33->setDocumento(parent::getActUser("documento"));
            $mercurio33->setCampo($mmercurio28->getCampo());
            $mercurio33->setAntval($antval);
            $mercurio33->setValor($valor);
            $mercurio33->setEstado("P");
            $mercurio33->setUsuario($usuario);
            $flag_email = true;
            $mercurio33->save();

            $item = Mercurio10::where('tipopc', '5')
                ->where('numero', $mercurio33->getId())
                ->max('item') + 1;

            $mercurio10 = new Mercurio10();
            #$mercurio10->setTransaction($Transaccion);
            $mercurio10->setTipopc("5");
            $mercurio10->setNumero($mercurio33->getId());
            $mercurio10->setItem($item);
            $mercurio10->setEstado("P");
            $mercurio10->setNota("Envio a la caja para verificación");
            $mercurio10->setFecsis($today->format('Y-m-d'));
            $mercurio10->save();

            $mercurio01 = Mercurio01::first();

            foreach (Mercurio14::where('tipopc', '5')->get() as $m14) {
                $coddoc = $m14->getCoddoc();
                $mercurio37 = new Mercurio37();

                $mercurio37->setTipopc("5");
                $mercurio37->setNumero($mercurio33->getId());
                $mercurio37->setCoddoc($coddoc);
                if (isset($_FILES['archivo_' . $coddoc]['name']) && $_FILES['archivo_' . $coddoc]['name'] != "") {
                    $extension = explode(".", $_FILES['archivo_' . $coddoc]['name']);
                    $name = "5_" . $mercurio33->getId() . "_{$coddoc}." . end($extension);
                    $_FILES['archivo_' . $coddoc]['name'] = $name;
                    $estado = UploadFile::upload("archivo_" . $coddoc, $mercurio01->getPath());
                    if ($estado != false) {
                        $mercurio37->setArchivo($name);
                        $mercurio37->save();

                        $response = ("Se adjunto con exito el archivo");
                    } else {
                        $response = ("No se cargo: tamano del archivo muy grande o no es valido");
                    }
                } else {
                    $response = ("No se cargo el archivo");
                }
            }
        }
        if ($flag_email == true) {
            $asunto = "Actualizacion datos";
            $msj  = "acabas de utilizar";
            $generalService = new GeneralService();
            $generalService->sendEmail(parent::getActUser("email"), parent::getActUser("nombre"), $asunto, $msj, "");
        }

        $response = "Movimiento realizado con exito el archivo";

        return $this->renderText(json_encode("Movimiento realizado con exito el archivo"));
    }

    public function afilia_masiva_trabajador_viewAction()
    {
        return view("mercurio/subsidioemp/tmp/afilia_masiva_trabajador", [
            "path" => base_path(),
            "title" => "Afiliacion Masiva"
        ]);
    }

    public function errorCarga($l, $cedtra, $nombre, $nota)
    {
        $html = "<tr>";
        $html .= "<td>$l</td>";
        $html .= "<td>$cedtra</td>";
        $html .= "<td>$nombre</td>";
        $html .= "<td>$nota</td>";
        $html .= "</tr>";
        return $html;
    }

    public function ejemplo_planilla_masivaAction()
    {
        $file = "public/docs/" . "ejemplo_planilla_masiva.xlsx";
    }

    public function certificado_afiliacion_viewAction()
    {
        return view("subsidioemp/certificado_afiliacion", [
            "hide_header" => true,
            "help" => false,
            "title" => "Certificado Afiliacion",
            "document_title" => "Certificado Afiliacion"
        ]);
    }

    public function certificado_afiliacionAction()
    {
        $logger = new Logger();
        $logger->registrarLog(false, "Certificado De Afiliacion", "");
        header("Location: https://comfacaenlinea.com.co/SYS/Subsidio/subflo/gene_certi_emp/x/" . parent::getActUser("documento"));
    }


    public function certificado_para_trabajador_viewAction()
    {
        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "listar_trabajadores",
                "params" => array(
                    "nit" => parent::getActUser("documento"),
                    "estado" => "A"
                )
            )
        );
        $out = $ps->toArray();
        if (!$out['success']) {
            throw new DebugException($out['msj']);
        }

        $_cedtra = array();
        $subsi15 = $out['data'];
        foreach ($subsi15 as $msubsi15) {
            $_cedtra[$msubsi15['cedtra']] = $msubsi15['nombre'];
        }

        return view("subsidioemp/certificado_para_trabajador", [
            "hide_header" => true,
            "help" => false,
            "title" => "Certificado Para Trabajador",
            "document_title" => "Certificado Para Trabajador",
            "_cedtra" => $_cedtra,
            "tipo" => array(
                "A" => "Certificado Afiliacion Principal",
                "I" => "Certificacion Con Nucleo",
                "T" => "Certificacion de Multiafiliacion",
                "P" => "Reporte trabajador en planillas"
            )
        ]);
    }

    public function certificado_para_trabajadorAction(Request $request)
    {
        $logger = new Logger();
        $tipo = $request->input("tipo");
        $cedtra = $request->input("cedtra");
        $logger->registrarLog(false, "Certificado Para Trabajador", "$tipo - $cedtra");
        header("Location: https://comfacaenlinea.com.co/SYS/Subsidio/subflo/gene_certi_tra/{$tipo}/" . $cedtra);
    }


    public function ejemplo_planilla_activacion_masivaAction()
    {
        $file = "public/docs/" . "ejemplo_planilla_activacion_masiva.csv";
    }

    public function activacion_masiva_trabajador_viewAction()
    {
        return view("subsidioemp/activacion_masiva_trabajador", [
            "hide_header" => true,
            "help" => false,
            "title" => "Activacion Masiva",
            "document_title" => "Activacion Masiva"
        ]);
    }

    function load_parametros_view()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_empresa"
            ),
            false
        );

        $datos_captura =  $procesadorComando->toArray();
        $_calemp = array();
        foreach ($datos_captura['calidad_empresa'] as $data) $_calemp[$data['estado']] = $data['detalle'];

        $_ciupri = array();
        foreach ($datos_captura['ciudad_comercial'] as $data) $_ciupri["{$data['codciu']}"] = $data['codciu'] . "-" . $data['detciu'];

        $_codciu = array();
        foreach ($datos_captura['ciudades'] as $data) $_codciu["{$data['codciu']}"] = $data['codciu'] . "-" . $data['detciu'];

        $_codzon = array();
        foreach ($datos_captura['zonas'] as $data) $_codzon[$data['codzon']] = $data['codzon'] . "-" . $data['detzon'];

        $_codcaj = array();
        foreach ($datos_captura['codigo_cajas'] as $data) $_codcaj[$data['codcaj']] = $data['detalle'];

        $_coddoc = array();
        foreach ($datos_captura['tipo_documentos'] as $data) {
            if ($data['coddoc'] == '7' || $data['coddoc'] == '2') continue;
            $_coddoc["{$data['coddoc']}"] = $data['detdoc'];
        }

        $_tipsoc = array();
        foreach ($datos_captura['tipo_sociedades'] as $data) $_tipsoc["{$data['tipsoc']}"] = $data['detalle'];

        $_codact = array();
        foreach ($datos_captura['actividades'] as $data) $_codact["{$data['codact']}"] = "{$data['codact']} - " . $data['detalle'];

        $_tipper = array();
        foreach ($datos_captura['tipo_persona'] as $data) $_tipper["{$data['estado']}"] = $data['detalle'];

        $_tipemp = array();
        foreach ($datos_captura['tipo_empresa'] as $data) $_tipemp["{$data['estado']}"] = $data['detalle'];

        return [
            "_tipper" => $_tipper,
            "_coddoc" => $_coddoc,
            "_calemp" => $_calemp,
            "_codciu" => $_codciu,
            "_codzon" => $_codzon,
            "_codact" => $_codact,
            "_tipsoc" => $_tipsoc,
            "_tipemp" => $_tipemp,
            "_codcaj" => $_codcaj,
            "_ciupri" => $_ciupri,
            "tipo" => session("tipo")
        ];
    }

    function buscarEmpresaSubsidio($nit)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" => array("nit" => $nit)
            )
        );
        $salida =  $procesadorComando->toArray();
        if ($salida['success']) {
            return $salida;
        } else {
            return false;
        }
    }

    function consulta_nucleoAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $cedtra = $request->input("cedtra");
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
            $salida =  $ps->toArray();
            if (!$salida['success']) {
                $salida['data'] = array();
            }
            $conyuges = $salida['data']['conyuges'];
            $beneficiarios = $salida['data']['beneficiarios'];

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

            $params = array(
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
                "_estado" => (new Mercurio31())->getEstados(),
                "_comper" => ParamsConyuge::getCompaneroPermanente(),
                "_parent" => ParamsBeneficiario::getParentesco(),
                "_huerfano" => ParamsBeneficiario::getHuerfano(),
                "_tiphij" => ParamsBeneficiario::getTipoHijo(),
                "_calendario" => ParamsBeneficiario::getCalendario(),
                "_huerfano" => ParamsBeneficiario::getHuerfano(),
                "_tiphij" => ParamsBeneficiario::getTipoHijo(),
                "_calendario" => ParamsBeneficiario::getCalendario(),
                '_codcat' => (new Mercurio31)->getCategoria(),
            );

            $salida = array(
                'success' => true,
                'data' => array(
                    'conyuges' => $conyuges,
                    'beneficiarios' => $beneficiarios,
                    'params' => $params
                )
            );
        } catch (DebugException $e) {
            $salida = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }
}
