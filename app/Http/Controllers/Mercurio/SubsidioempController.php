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
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio33;
use App\Models\Mercurio34;
use App\Models\Mercurio35;
use App\Models\Mercurio37;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubsidioempController extends ApplicationController
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

        $mercurio31 = Mercurio31::where('nit', $documento)->orderBy('id', 'desc')->get();
        $mercurio33 = Mercurio33::where([
            ['tipo', $tipo],
            ['coddoc', $coddoc],
            ['documento', $documento]
        ])->orderBy('id', 'desc')->get();

        $mercurio35 = Mercurio35::where('nit', $documento)->orderBy('id', 'desc')->get();

        $mercurio32 = Mercurio32::where([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
        ])->orderBy('id', 'desc')->first();

        $mercurio34 = Mercurio34::where([
            'tipo' => $tipo,
            'coddoc' => $coddoc,
            'documento' => $documento,
        ])->orderBy('id', 'desc')->get();

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
        if (count($mercurio31) == 0) {
            $html_afiliacion .= "<tr align='center'>";
            $html_afiliacion .= "<td colspan=6><label>No hay datos para mostrar</label></td>";
            $html_afiliacion .= "<tr>";
            $html_afiliacion .= "</tr>";
        }
        foreach ($mercurio31 as $mmercurio31) {
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
        if (count($mercurio35) == 0) {
            $html_retiro .= "<tr align='center'>";
            $html_retiro .= "<td colspan=5><label>No hay datos para mostrar</label></td>";
            $html_retiro .= "<tr>";
            $html_retiro .= "</tr>";
        }
        foreach ($mercurio35 as $mmercurio35) {
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

        if (count($mercurio32) == 0) {
            $html_afiliacion_conyuge .= "<tr align='center'>";
            $html_afiliacion_conyuge .= "<td colspan=6><label>No hay datos para mostrar</label></td>";
            $html_afiliacion_conyuge .= "<tr>";
            $html_afiliacion_conyuge .= "</tr>";
        } else {

            if ($mercurio32) {
                foreach ($mercurio32 as $mmercurio32) {
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
        if (count($mercurio34) == 0) {
            $html_afiliacion_beneficiario .= "<tr align='center'>";
            $html_afiliacion_beneficiario .= "<td colspan=6><label>No hay datos para mostrar</label></td>";
            $html_afiliacion_beneficiario .= "<tr>";
            $html_afiliacion_beneficiario .= "</tr>";
        }
        foreach ($mercurio34 as $mmercurio34) {
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
        if (count($mercurio33) == 0) {
            $actualizacion_basico .= "<tr align='center'>";
            $actualizacion_basico .= "<td colspan=6><label>No hay datos para mostrar</label></td>";
            $actualizacion_basico .= "<tr>";
            $actualizacion_basico .= "</tr>";
        }
        foreach ($mercurio33 as $mmercurio33) {
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

    public function consulta_trabajadores_viewAction()
    {
        return view("mercurio/subsidioemp/consulta_trabajadores", [
            "hide_header" => true,
            "help" => false,
            "title" => "Consulta Trabajadores"
        ]);
    }

    public function consulta_trabajadoresAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $estado = $request->input("estado");
            $nit = $request->input("nit") ? $request->input("nit") : parent::getActUser("documento");
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

            $html = view('subsidioemp/tmp/tmp_afiliados', array('trabajadores' => $out['data']))->render();
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

    public function consulta_giro_viewAction()
    {
        return view("mercurio/subsidioemp/consulta_giro", [
            "hide_header" => true,
            "help" => false,
            "title" => "Consulta Giro"
        ]);
    }

    public function consulta_giroAction(Request $request)
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

    public function consulta_nomina_viewAction()
    {
        return view("mercurio/subsidioemp/consulta_nomina", [
            "hide_header" => true,
            "help" => false,
            "title" => "Consulta Nomina"
        ]);
    }

    public function consulta_nominaAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $periodo = $request->input("periodo");
            $nit = parent::getActUser("documento");

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

            $html = view('subsidioemp/tmp/tmp_nomina', array('nominas' => $out['data']))->render();
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

    public function consulta_aportes_viewAction()
    {
        return view("mercurio/subsidioemp/consulta_aportes", [
            "hide_header" => true,
            "help" => false,
            "title" => "Consulta Aportes"
        ]);
    }

    public function consulta_aportesAction(Request $request)
    {
        $this->setResponse("ajax");
        try {

            $perini = $request->input("perini");
            $perfin = $request->input("perfin");
            $nit = parent::getActUser("documento");

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

            $html = view('subsidioemp/tmp/tmp_aportes', array('aportes' => $out['data']))->render();
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
            $generalService = new GeneralService();
            $id_log = $generalService->registrarLog(true, "retiro trabajador", "");
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
            $usuario = $asignarFuncionario->asignar("7", parent::getActUser("codciu"));

            if ($usuario == "") {
                $response = "No se puede realizar el registro,Comuniquese con la Atencion al cliente";
                return $this->renderObject($response);
            }
            $mercurio35->setUsuario($usuario);
            $mercurio35->setTipo(parent::getActUser("tipo"));
            $mercurio35->setCoddoc(parent::getActUser("coddoc"));
            $mercurio35->setDocumento(parent::getActUser("documento"));
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
            $this->loadDisplaySubsidio($empresa);
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
        $generalService = new GeneralService();
        $id_log = $generalService->registrarLog(true, "actualización datos basicos", "");
        $mercurio28 = Mercurio28::where('tipo', parent::getActUser("tipo"))->get();

        if (parent::getActUser("tipo") == 'T') {
            $tipopc = 14;
        } else {
            $tipopc = 5;
        }
        $asignarFuncionario = new AsignarFuncionario();
        $usuario = $asignarFuncionario->asignar($tipopc, parent::getActUser("codciu"));
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

            foreach ($Mercurio14::where('tipopc', '5')->get() as $m14) {
                $coddoc = $m14->getCoddoc();
                $mercurio37 = new Mercurio37();

                $mercurio37->setTipopc("5");
                $mercurio37->setNumero($mercurio33->getId());
                $mercurio37->setCoddoc($coddoc);
                if (isset($_FILES['archivo_' . $coddoc]['name']) && $_FILES['archivo_' . $coddoc]['name'] != "") {
                    $extension = explode(".", $_FILES['archivo_' . $coddoc]['name']);
                    $name = "5_" . $mercurio33->getId() . "_{$coddoc}." . end($extension);
                    $_FILES['archivo_' . $coddoc]['name'] = $name;
                    $estado = $this->uploadFile("archivo_" . $coddoc, $mercurio01->getPath());
                    if ($estado != false) {
                        $mercurio37->setArchivo($name);
                        $mercurio37->save();

                        $response = parent::successFunc("Se adjunto con exito el archivo");
                    } else {
                        $response = parent::errorFunc("No se cargo: tamano del archivo muy grande o no es valido");
                    }
                } else {
                    $response = parent::errorFunc("No se cargo el archivo");
                }
            }
        }
        if ($flag_email == true) {
            $asunto = "Actualizacion datos";
            $msj  = "acabas de utilizar";
            $generalService = new GeneralService();
            $generalService->sendEmail(parent::getActUser("email"), parent::getActUser("nombre"), $asunto, $msj, "");
        }

        $response = parent::successFunc("Movimiento realizado con exito el archivo");
        Router::routeTo("controller: principal", "action: index");
        return $this->renderText(json_encode("Movimiento realizado con exito el archivo"));
    }

    public function afilia_masiva_trabajador_viewAction()
    {
        //opcion no disponible temporalmente
        Router::routeTo("controller: principal", "action: index");

        $this->setParamToView("hide_header", true);
        $this->setParamToView("help", false);

        $this->setParamToView("title", "Afiliacion Masiva");
        Tag::setDocumentTitle('Afiliacion Masiva');
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

    public function afilia_masiva_trabajadorAction()
    {

        $this->setResponse("ajax");

        try {
            try {
                if (!isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] == "") {
                    $response = parent::errorFunc("No se cargo el archivo");
                    return $this->renderText(json_encode($response));
                }
                $estado = $this->uploadFile("archivo", "public/temp/");
                if ($estado == false) {
                    $response = parent::errorFunc("No se cargo el archivo");
                    return $this->renderText(json_encode($response));
                }
                $modelos = array("mercurio10", "mercurio20", "mercurio31");
                $Transaccion = parent::startTrans($modelos);
                $response = parent::startFunc();
                $tipopc = 1;


                $ps = Comman::Api();
                $ps->runCli(
                    array(
                        "servicio" => "PoblacionAfilia",
                        "metodo" => "captura_trabajador"
                    )
                );

                $out = $ps->toArray();
                if (!$out['success']) {
                    throw new DebugException($out['msj']);
                }
                $datos_captura = $out['data'];
                $_coddoc = array();
                foreach ($datos_captura['coddoc'] as $data) $_coddoc[$data['coddoc']] = $data['detalle'];
                $_sexo = array();
                foreach ($datos_captura['sexo'] as $data) $_sexo[$data['sexo']] = $data['detalle'];
                $_estciv = array();
                foreach ($datos_captura['estciv'] as $data) $_estciv[$data['estciv']] = $data['detalle'];
                $_cabhog = array();
                foreach ($datos_captura['cabhog'] as $data) $_cabhog[$data['cabhog']] = $data['detalle'];
                $_codciu = array();
                foreach ($datos_captura['codciu'] as $data) $_codciu[$data['codciu']] = $data['detalle'];
                $_codzon = array();
                foreach ($datos_captura['codzon'] as $data) $_codzon[$data['codzon']] = $data['detalle'];
                $_captra = array();
                foreach ($datos_captura['captra'] as $data) $_captra[$data['captra']] = $data['detalle'];
                $_tipdis = array();
                foreach ($datos_captura['tipdis'] as $data) $_tipdis[$data['tipdis']] = $data['detalle'];
                $_nivedu = array();
                foreach ($datos_captura['nivedu'] as $data) $_nivedu[$data['nivedu']] = $data['detalle'];
                $_rural = array();
                foreach ($datos_captura['rural'] as $data) $_rural[$data['rural']] = $data['detalle'];
                $_tipcon = array();
                foreach ($datos_captura['tipcon'] as $data) $_tipcon[$data['tipcon']] = $data['detalle'];
                $_trasin = array();
                foreach ($datos_captura['trasin'] as $data) $_trasin[$data['trasin']] = $data['detalle'];
                $_vivienda = array();
                foreach ($datos_captura['vivienda'] as $data) $_vivienda[$data['vivienda']] = $data['detalle'];
                $_tipafi = array();
                foreach ($datos_captura['tipafi'] as $data) $_tipafi[$data['tipafi']] = $data['detalle'];
                $_autoriza[] = "SI";
                $_autoriza[] = "NO";
                $today = new Date();
                $mensajes_error = "";
                $l = 0;
                $file = file("public/temp/" . $_FILES['archivo']['name']);
                foreach ($file as $mlinea) {
                    $l++;
                    $datos = preg_split("/\|/", $mlinea);
                    $tipdoc = strtoupper(trim($datos[0]));
                    $cedtra = strtoupper(trim($datos[1]));
                    $priape = strtoupper(trim($datos[2]));
                    $segape = strtoupper(trim($datos[3]));
                    $prinom = strtoupper(trim($datos[4]));
                    $segnom = strtoupper(trim($datos[5]));
                    $fecnac = strtoupper(trim($datos[6]));
                    $ciunac = strtoupper(trim($datos[7]));
                    $sexo = strtoupper(trim($datos[8]));
                    $estciv = strtoupper(trim($datos[9]));
                    $cabhog = strtoupper(trim($datos[10]));
                    $codciu = strtoupper(trim($datos[11]));
                    $codzon = strtoupper(trim($datos[12]));
                    $direccion = strtoupper(trim($datos[13]));
                    $barrio = strtoupper(trim($datos[14]));
                    $telefono = strtoupper(trim($datos[15]));
                    $celular = strtoupper(trim($datos[16]));
                    $fax = strtoupper(trim($datos[17]));
                    $email = strtoupper(trim($datos[18]));
                    $fecing = strtoupper(trim($datos[19]));
                    $salario = strtoupper(trim($datos[20]));
                    $captra = strtoupper(trim($datos[21]));
                    $tipdis = strtoupper(trim($datos[22]));
                    $nivedu = strtoupper(trim($datos[23]));
                    $rural = strtoupper(trim($datos[24]));
                    $horas = strtoupper(trim($datos[25]));
                    $tipcon = strtoupper(trim($datos[26]));
                    $trasin = strtoupper(trim($datos[27]));
                    $vivienda = strtoupper(trim($datos[28]));
                    $tipafi = strtoupper(trim($datos[29]));
                    $profesion = strtoupper(trim($datos[30]));
                    $cargo = strtoupper(trim($datos[31]));
                    $autoriza = strtoupper(trim($datos[32]));
                    $nombre = $priape . " " . $segape . " " . $prinom . " " . $segnom;
                    $key_tipdoc = array_search($tipdoc, $_coddoc);
                    $key_ciunac = array_search($ciunac, $_codciu);
                    $key_sexo = array_search($sexo, $_sexo);
                    $key_estciv = array_search($estciv, $_estciv);
                    $key_cabhog = array_search($cabhog, $_cabhog);
                    $key_codciu = array_search($codciu, $_codciu);
                    $key_codzon = array_search($codzon, $_codciu);
                    $key_captra = array_search($captra, $_captra);
                    $key_tipdis = array_search($tipdis, $_tipdis);
                    $key_nivedu = array_search($nivedu, $_nivedu);
                    $key_rural = array_search($rural, $_rural);
                    $key_tipcon = array_search($tipcon, $_tipcon);
                    $key_trasin = array_search($trasin, $_trasin);
                    $key_vivienda = array_search($vivienda, $_vivienda);
                    $key_tipafi = array_search($tipafi, $_tipafi);
                    $key_autoriza = array_search($autoriza, $_autoriza);
                    if ((new Mercurio31)->getCount(
                        "*",
                        "conditions: tipo='" . parent::getActUser('tipo') . "' and coddoc = '" . parent::getActUser("coddoc") . "' and documento='" . parent::getActUser("documento") . "' and cedtra='$cedtra' and estado<>'X'"
                    ) > 0) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Ya tiene un formulario presentado");
                    if ($key_tipdoc === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Tipo de Documento No existe $tipdoc");
                    if ($key_ciunac === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Ciudad de Nacimiento No existe $ciunac");
                    if ($key_sexo === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Sezo No existe $sexo");
                    if ($key_estciv === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Estado Civil No existe $estciv");
                    if ($key_cabhog === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Cabeza de hogar No existe $cabhog");
                    if ($key_codciu === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Ciudad No existe $codciu");
                    if ($key_codzon === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Zona No existe $codzon");
                    if ($key_captra === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Capacidad Trabajo No existe $captra");
                    if ($key_tipdis === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Tipo Discapacidad No existe $tipdis");
                    if ($key_nivedu === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "nivel educacion No existe $nivedu");
                    if ($key_rural === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "rural No existe $rural");
                    if ($key_tipcon === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Tipo de Contrato No existe $tipcon");
                    if ($key_trasin === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Trabajador sindicalizado No existe $trasin");
                    if ($key_vivienda === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Vivienda No existe $vivienda");
                    if ($key_tipafi === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Tipo de Afiliado No existe $tipafi");
                    if ($key_autoriza === false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Autoriza No existe $autoriza");
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Email no tiene formato valido $email");
                    try {
                        $fecha = new Date($fecnac);
                    } catch (DateException $e) {
                        $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Fecha Nacimiento no tiene formato valido $fecnac");
                    }
                    try {
                        $fecha = new Date($fecing);
                    } catch (DateException $e) {
                        $mensajes_error .= self::errorCarga($l, $cedtra, $nombre, "Fecha Ingreso no tiene formato valido  $fecing");
                    }
                    if ($mensajes_error == "") {
                        $generalService = new GeneralService();
                        $id_log = $generalService->registrarLog(true, "Afiliacion Trabajador", "");
                        $mercurio31 = new Mercurio31();
                        $mercurio31->setTransaction($Transaccion);
                        $mercurio31->setId(0);
                        $mercurio31->setLog($id_log);
                        $mercurio31->setNit(parent::getActUser("documento"));
                        $mercurio31->setRazsoc(parent::getActUser("nombre"));
                        $mercurio31->setCedtra($cedtra);
                        $mercurio31->setTipdoc($_coddoc[$key_tipdoc]);
                        $mercurio31->setPriape($priape);
                        $mercurio31->setSegape($segape);
                        $mercurio31->setPrinom($prinom);
                        $mercurio31->setSegnom($segnom);
                        $mercurio31->setFecnac($fecnac);
                        //  $mercurio31->setFecnac(date("Y-m-d",strtotime($fecnac)));
                        $mercurio31->setCiunac($_codciu[$key_ciunac]);
                        $mercurio31->setSexo($_sexo[$key_sexo]);
                        $mercurio31->setEstciv($_estciv[$key_estciv]);
                        $mercurio31->setCabhog($_cabhog[$key_cabhog]);
                        $mercurio31->setCodciu($_codciu[$key_codciu]);
                        $mercurio31->setCodzon($_codciu[$key_codzon]);
                        $mercurio31->setDireccion($direccion);
                        $mercurio31->setBarrio($barrio);
                        $mercurio31->setTelefono($telefono);
                        $mercurio31->setCelular($celular);
                        $mercurio31->setFax($fax);
                        $mercurio31->setEmail($email);
                        $mercurio31->setFecing($fecing);
                        // $mercurio31->setFecing(date("Y-m-d",strtotime($fecing)));
                        $mercurio31->setSalario($salario);
                        $mercurio31->setCaptra($_captra[$key_captra]);
                        $mercurio31->setTipdis($_tipdis[$key_tipdis]);
                        $mercurio31->setNivedu($_nivedu[$key_nivedu]);
                        $mercurio31->setRural($_rural[$key_rural]);
                        $mercurio31->setHoras($horas);
                        $mercurio31->setTipcon($_tipcon[$key_tipcon]);
                        $mercurio31->setTrasin($_trasin[$key_trasin]);
                        $mercurio31->setVivienda($_vivienda[$key_vivienda]);
                        $mercurio31->setTipafi($_tipafi[$key_tipafi]);
                        $mercurio31->setProfesion($profesion);
                        $mercurio31->setCargo($cargo);
                        $mercurio31->setAutoriza($_autoriza[$key_autoriza]);
                        $mercurio31->setEstado("T");
                        $asignarFuncionario = new AsignarFuncionario();
                        $usuario = $asignarFuncionario->asignar($tipopc, parent::getActUser("codciu"));
                        if ($usuario == "") {
                            $response = parent::errorFunc("No se puede realizar el registro,Comuniquese con la Atencion al cliente");
                            return $this->renderText(json_encode($response));
                        }
                        $mercurio31->setUsuario($usuario);
                        $mercurio31->setTipo(parent::getActUser("tipo"));
                        $mercurio31->setCoddoc(parent::getActUser("coddoc"));
                        $mercurio31->setDocumento(parent::getActUser("documento"));
                        if (!$mercurio31->save()) {
                            parent::setLogger($mercurio31->getMessages());
                            parent::ErrorTrans();
                        }
                    }
                }
                $html = "";
                $html .= "<table class='table'>";
                $html .= "<thead>";
                $html .= "<tr>";
                $html .= "<th>Linea</th>";
                $html .= "<th>Cedula</th>";
                $html .= "<th>Nombre</th>";
                $html .= "<th>Nota</th>";
                $html .= "</tr>";
                $html .= "</thead>";
                $html .= "<tbody>";
                $html .= $mensajes_error;
                $html .= "<tbody>";
                $html .= "</table>";
                if ($mensajes_error == "") {
                    parent::finishTrans();
                    $response = parent::successFunc("Se digitaron $l trabajador con exito", $html);
                } else {
                    $response = parent::errorFunc("No se pudo realizar la accion", $html);
                }
                return $this->renderText(json_encode($response));
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se pudo realizar la accion xxx");
            return $this->renderText(json_encode($response));
        }
    }

    public function ejemplo_planilla_masivaAction()
    {
        $file = "public/docs/" . "ejemplo_planilla_masiva.xlsx";
        header("location: " . Core::getInstancePath() . "/{$file}");
    }

    public function certificado_afiliacion_viewAction()
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("help", false);
        $this->setParamToView("title", "Certificado Afiliacion");
        Tag::setDocumentTitle('Certificado Afiliacion');
    }

    public function certificado_afiliacionAction()
    {
        $generalService = new GeneralService();
        $generalService->registrarLog(false, "Certificado De Afiliacion", "");
        header("Location: https://comfacaenlinea.com.co/SYS/Subsidio/subflo/gene_certi_emp/x/" . parent::getActUser("documento"));
    }


    public function certificado_para_trabajador_viewAction()
    {
        $this->setParamToView("hide_header", true);
        $this->setParamToView("help", false);
        $this->setParamToView("title", "Certificado Afiliacion");
        Tag::setDocumentTitle('Certificado Afiliacion');


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

        $this->setParamToView("_cedtra", $_cedtra);
        $this->setParamToView("tipo", array(
            "A" => "Certificado Afiliacion Principal",
            "I" => "Certificacion Con Nucleo",
            "T" => "Certificacion de Multiafiliacion",
            "P" => "Reporte trabajador en planillas"
        ));
    }

    public function certificado_para_trabajadorAction()
    {
        $generalService = new GeneralService();
        $tipo = $request->input("tipo");
        $cedtra = $request->input("cedtra");
        $generalService->registrarLog(false, "Certificado Para Trabajador", "$tipo - $cedtra");
        header("Location: https://comfacaenlinea.com.co/SYS/Subsidio/subflo/gene_certi_tra/{$tipo}/" . $cedtra);
    }


    public function ejemplo_planilla_activacion_masivaAction()
    {
        $file = "public/docs/" . "ejemplo_planilla_activacion_masiva.csv";
        header("location: " . Core::getInstancePath() . "/{$file}");
    }

    public function activacion_masiva_trabajador_viewAction()
    {
        //opcion no disponible
        Router::routeTo("controller: principal", "action: index");

        $this->setParamToView("hide_header", true);
        $this->setParamToView("help", false);
        $this->setParamToView("title", "Activacion Masiva");
        Tag::setDocumentTitle('Activacion Masiva');
    }


    public function activacion_masiva_trabajadorAction()
    {

        try {
            try {
                $this->setResponse("ajax");
                if (!isset($_FILES['archivo']['name']) && $_FILES['archivo']['name'] == "") {
                    $response = parent::errorFunc("No se cargo el archivo");
                    return $this->renderText(json_encode($response));
                }
                $estado = $this->uploadFile("archivo", "public/temp/");
                if ($estado == false) {
                    $response = parent::errorFunc("No se cargo el archivo");
                    return $this->renderText(json_encode($response));
                }
                $modelos = array("mercurio10", "mercurio20", "mercurio31", "mercurio46");
                # $Transaccion = parent::startTrans($modelos);

                $tipopc = 1;

                $ps = Comman::Api();
                $ps->runCli(
                    array(
                        "servicio" => "PoblacionAfilia",
                        "metodo" => "captura_trabajador"
                    )
                );
                $datos_captura = $ps->toArray();

                if (!$datos_captura['success']) {
                    throw new DebugException($datos_captura['msj']);
                }

                $datos_captura = $datos_captura['data'];
                $_autoriza[] = "SI";
                $_autoriza[] = "NO";
                $today = Carbon::now();
                $mensajes_error = "";
                $error = false;
                $l = 0;
                $file = file("public/temp/" . $_FILES['archivo']['name']);

                $array = array();
                $aprobados = 0;
                $apro = array("subsi15" => array(), "subsi168" => array());
                $errores = array("subsi15" => array(), "subsi168" => array(), "formato_fecha" => array());
                $actmult = array("s168act" => array());
                $verifico = 0;
                foreach ($file as $mlinea) {
                    $datos = preg_split("/;/", $mlinea);
                    if (isset($datos[1]) == false) {
                        $datos = preg_split("/,/", $mlinea);
                        if (isset($datos[1]) == false) {
                            continue;
                        }
                    };
                    $cedtra = trim($datos[0]);
                    $fecafi = trim($datos[1]);
                    $valida_fecha = date('Y-m-d', strtotime("$fecafi"));
                    if ($valida_fecha == '1969-12-31') {
                        $errores['formato_fecha'][] = array("linea" => $l, "cedtra" => $cedtra, "nota" => "El formato de la fecha no es valido, porfavor verfiquelo.");
                        continue;
                    }
                    $l++;
                    if ($cedtra == "" && $fecafi == "") continue;

                    $ps = Comman::Api();
                    $ps->runCli(
                        array(
                            "servicio" => "PoblacionAfiliada",
                            "metodo" => "datosTrabajadorTodos",
                            "params" =>  array("cedtra" => $cedtra)
                        )
                    );

                    $datos_trabajador = $ps->toArray();
                    if ($datos_trabajador['success'] == false) {
                        $errores['subsi15'][] = array("linea" => $l, "cedtra" => $cedtra, "nota" => $datos_trabajador['msj']);
                        continue;
                    }

                    $mercurio31 = new Mercurio31();
                    foreach ($datos_trabajador['data'] as $key => $value) {
                        $mercurio31->setLog(1);
                        if ($mercurio31->isAttribute(strval($key)))
                            $mercurio31->writeAttribute(strval($key), "$value");
                        if ($key == 'coddoc')
                            $mercurio31->writeAttribute("tipdoc", "$value");
                    }

                    $nombre = $mercurio31->getNombre();
                    if (Mercurio31::where('cedtra', $cedtra)
                        ->where('nit', parent::getActUser("documento"))
                        ->whereIn('estado', ['T', 'P'])
                        ->count() > 0
                    ) {
                        $errores['subsi15'][] = array("linea" => $l, "cedtra" => $cedtra, "nombre" => $nombre, "nota" => "El Trabajador ya tiene una solicitud creada1.");
                        continue;
                    }
                    if ($mercurio31->getCedtra() == '') {
                        $errores['subsi15'][] = array("linea" => $l, "cedtra" => $cedtra, "nombre" => $nombre, "nota" => "El Trabajador no existe en nuestro sistema.");
                        continue;
                    }
                    if ($mercurio31->getNit() == parent::getActUser("documento") && $mercurio31->getEstado() == 'A') {
                        $errores['subsi15'][] = array("linea" => $l, "cedtra" => $cedtra, "nombre" => $nombre, "nota" => "El Trabajador esta activo en la misma empresa.");
                        continue;
                    }

                    if ($mercurio31->getNit() != parent::getActUser("documento") &&  $mercurio31->getEstado() == 'A' && isset($datos_trabajador['data']['s168'])  && $datos_trabajador['data']['s168'][0] == 0) {
                        $errores['subsi15'][] = array("linea" => $l, "cedtra" => $cedtra, "nombre" => $nombre, "nota" => "El Trabajador esta activo en otra empresa.");
                        continue;
                    }

                    if ($mercurio31->getEstado() == 'M') {
                        $errores['subsi15'][] = array("linea" => $l, "cedtra" => $cedtra, "nombre" => $nombre, "nota" => "El Trabajador Fallecio.");
                        continue;
                    }
                    //Verifica Multiafiliacion
                    $ps = Comman::Api();
                    $ps->runCli(
                        array(
                            "servicio" => "PoblacionAfiliada",
                            "metodo" => "verifica_multiafiliacion",
                            "params" => array("nit" => parent::getActUser("documento"), "cedtra" => $cedtra)
                        )
                    );
                    $multiafiliacion = $ps->toArray();
                    if ($multiafiliacion['data']['s168'] > 0) {
                        $actmult['s168act'][] = array("linea" => $l, "cedtra" => $cedtra, "nombre" => $nombre, "nota" => "El trabajador se encuentra activo por multiafiliacion en esta empresa.");
                        continue;
                    }
                    if ($mercurio31->getNit() != parent::getActUser("documento") &&  $multiafiliacion['data']['subsi168'] == 0) {
                        $errores['subsi15'][] = array("linea" => $l, "cedtra" => $cedtra, "nombre" => $nombre, "nota" => "El trabajador no se encuentra afiliado a esta empresa.");
                        continue;
                    }
                    $generalService = new GeneralService();
                    $id_log = $generalService->registrarLog(true, "Afiliacion Trabajador", "");
                    $mercurio31->setTransaction($Transaccion);
                    $mercurio31->setId(0);
                    $mercurio31->setLog($id_log);
                    $mercurio31->setNit(parent::getActUser("documento"));
                    $mercurio31->setRazsoc(parent::getActUser("nombre"));
                    $mercurio31->setEstado("A");
                    $mercurio31->setAutoriza("S");
                    $mercurio31->setFecsol($today->getDate());
                    $mercurio31->setFecing($today->getDate());
                    $mercurio31->setTipafi("01");
                    $mercurio31->setCodest(NULL);
                    $asignarFuncionario = new AsignarFuncionario();
                    $usuario = $asignarFuncionario->asignar($tipopc, parent::getActUser("codciu"));

                    if ($usuario == "") {
                        $response = parent::errorFunc("No se puede realizar el registro,Comuniquese con la Atencion al cliente");
                        $error = false;
                        return $this->renderText(json_encode($response));
                    }
                    $mercurio31->setUsuario($usuario);
                    $mercurio31->setTipo(parent::getActUser("tipo"));
                    $mercurio31->setCoddoc(parent::getActUser("coddoc"));
                    $mercurio31->setDocumento(parent::getActUser("documento"));
                    $params = array_merge($mercurio31->getArray(), $_POST, array("fecafi" => $fecafi, "codsuc" => $datos_trabajador["data"]["codsuc"], "codlis" => $datos_trabajador["data"]["codlis"], "vendedor" => $datos_trabajador["data"]["vendedor"], "empleador" => $datos_trabajador["data"]["empleador"]));

                    $servicio =

                        $procesadorComando = Comman::Api();
                    $procesadorComando->runPortal(array("servicio" => "activacion_masiva", "params" => $params));
                    $result = $procesadorComando->toArray();

                    if ($result['flag'] == false) {
                        $response = parent::errorFunc($result['msg']);
                        return $this->renderText(json_encode($response));
                    }
                    if (!$mercurio31->save()) {
                        parent::setLogger($mercurio31->getMessages());
                        parent::ErrorTrans();
                    }
                    $mercurio10 = new Mercurio10();
                    $mercurio10->setTransaction($Transaccion);
                    $mercurio10->setTipopc(1);
                    $mercurio10->setNumero($mercurio31->getId());
                    $mercurio10->setItem(2);
                    $mercurio10->setEstado("A");
                    $mercurio10->setNota("Afiliación exitosa");
                    $mercurio10->setFecsis($today->format('Y-m-d'));
                    if (!$mercurio10->save()) {
                        parent::setLogger($mercurio10->getMessages());
                        parent::ErrorTrans();
                    }
                    $mercurio46 = new Mercurio46();
                    $mercurio46->setTransaction($Transaccion);
                    $mercurio46->setId(0);
                    $mercurio46->setLog($id_log);
                    $mercurio46->setNit(parent::getActUser("documento"));
                    $mercurio46->setFecsis($today->format('Y-m-d'));
                    $mercurio46->setArchivo($_FILES['archivo']['name']);
                    if (!$mercurio46->save()) {
                        parent::setLogger($mercurio46->getMessages());
                        parent::ErrorTrans();
                    }
                    $aprobados++;
                    $array[] = $cedtra;
                    if (isset($result['data']['subsi15']['cedtra']) != false) {
                        $apro['subsi15'][] = $result['data']['subsi15'];
                    }
                    if (isset($result['data']['subsi168']['cedtra']) != false) {
                        $apro['subsi168'][] = $result['data']['subsi168'];
                    }
                }
                $aprobados = count($apro['subsi15']) + count($apro['subsi168']);
                $html = "";
                $html .= "<h2>Aceptados</h2>";
                $html .= "<table class='table'>";
                $html .= "<thead>";
                $html .= "<tr>";
                $html .= "<th>Linea</th>";
                $html .= "<th>Afiliacion</th>";
                $html .= "<th>Cedula</th>";
                $html .= "</tr>";
                $html .= "</thead>";
                $html .= "<tbody>";
                $aux = 1;
                foreach ($apro['subsi15'] as $value) {
                    if (isset($value["cedtra"]) == false) continue;
                    $html .= "<tr>";
                    $html .= "<td>" . $aux++ . "</td>";
                    $html .= "<td>Afiliacion Principal</td>";
                    $html .= "<td>" . $value["cedtra"] . "</td>";
                    $html .= "</tr>";
                }
                foreach ($apro['subsi168'] as $value) {
                    if (isset($value["cedtra"]) == false) continue;
                    $html .= "<tr>";
                    $html .= "<td>" . $aux++ . "</td>";
                    $html .= "<td>Multifialiacion</td>";
                    $html .= "<td>" . $value["cedtra"] . "</td>";
                    $html .= "</tr>";
                }
                $html .= "<tr>";
                $html .= "<td>" . $aux++ . "</td>";
                $html .= "<td>Total - Afiliacion Principal</td>";
                $html .= "<td>" . count($apro['subsi15']) . "</td>";
                $html .= "</tr>";
                $html .= "<tr>";
                $html .= "<td>" . $aux++ . "</td>";
                $html .= "<td>Total - Multiafiliacion</td>";
                $html .= "<td>" . count($apro['subsi168']) . "</td>";
                $html .= "</tr>";
                $html .= "<tbody>";
                $html .= "</table>";
                $html .= "<h2>Error</h2>";
                $html .= "<table class='table'>";
                $html .= "<thead>";
                $html .= "<tr>";
                $html .= "<th>Linea</th>";
                $html .= "<th>Cedula</th>";
                $html .= "<th>Nombre</th>";
                $html .= "<th>Nota</th>";
                $html .= "</tr>";
                $html .= "</thead>";
                $html .= "<tbody>";
                $html .= $errores;
                $html .= "<tbody>";
                $html .= "</table>";

                $today = new Date();
                $mancho = 220; //A4 P
                $mdatos = 160; //suma campos
                $mlineas = 26; //lines por hoja
                $info = array();
                $author = array("Reporte Por Sistemas y Soluciones");
                $this->setResponse('view');
                $pdf = new FPDFLibrary("P", 'mm', 'Letter');
                $pdf->AddPage();
                $pdf->SetTextColor(0);
                $pdf->Image('public/img/Mercurio/comfaca.jpg', 15, 10, "50", "30");
                $pdf->Ln();
                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetY(25);
                $pdf->SetX(round(($mancho - $mdatos) / 2));
                $pdf->cell(150, 4, "REPORTE ACTIVACION MASIVA - NIT: " . parent::getActUser("documento") . " FECHA: " . $today, 0, 0, 'C', 0);
                $pdf->Ln();
                $pdf->Ln();
                $pdf->Ln();
                $pdf->SetX(round(($mancho - $mdatos) / 2));
                $pdf->Cell(150, 4, "ACEPTADOS", 0, 0, 'C', 0);
                $pdf->Ln();
                $pdf->Ln();
                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setX(40);
                $pdf->cell(45, 4, "Linea", 1, 0, 'C', 0);
                $pdf->cell(45, 4, "Afiliacion", 1, 0, 'C', 0);
                $pdf->cell(45, 4, "Cedula", 1, 1, 'C', 0);
                $aux2 = 1;
                $pdf->SetFont('Arial', '', 8);
                foreach ($apro['subsi15'] as $value) {
                    if (isset($value["cedtra"]) == false) continue;
                    $pdf->setX(40);
                    $pdf->cell(45, 4, $aux2++, 1, 0, 'C', 0);
                    $pdf->cell(45, 4, "Afiliacion Principal", 1, 0, 'C', 0);
                    $pdf->cell(45, 4, $value["cedtra"], 1, 1, 'C', 0);
                }
                foreach ($apro['subsi168'] as $value) {
                    if (isset($value["cedtra"]) == false) continue;
                    $pdf->setX(40);
                    $pdf->cell(45, 4, $aux2++, 1, 0, 'C', 0);
                    $pdf->cell(45, 4, "Multifialiacion", 1, 0, 'C', 0);
                    $pdf->cell(45, 4, $value["cedtra"], 1, 1, 'C', 0);
                }
                $pdf->Ln();
                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setX(80);
                $pdf->Cell(50, 4, "NOVEDADES", 0, 0, 'C', 0);
                $pdf->Ln();
                $pdf->Ln();
                $pdf->setX(18);
                $pdf->cell(30, 4, "Linea", 1, 0, 'C', 0);
                $pdf->cell(30, 4, "Cedula", 1, 0, 'C', 0);
                $pdf->cell(30, 4, "Nombre", 1, 0, 'C', 0);
                $pdf->cell(90, 4, "Nota", 1, 1, 'C', 0);
                $pdf->SetFont('Arial', '', 8);
                foreach ($errores['subsi15'] as $value) {
                    $pdf->setX(18);
                    $pdf->cell(30, 4, $aux2++, 1, 0, 'C', 0);
                    $pdf->cell(30, 4, $value["cedtra"], 1, 0, 'C', 0);
                    $pdf->cell(30, 4, $value["nombre"], 1, 0, 'C', 0);
                    $pdf->cell(90, 4, $value["nota"], 1, 1, 'C', 0);
                }
                foreach ($errores['formato_fecha'] as $value) {
                    $pdf->setX(18);
                    $pdf->cell(30, 4, $aux2++, 1, 0, 'C', 0);
                    $pdf->cell(30, 4, $value["cedtra"], 1, 0, 'C', 0);
                    $pdf->cell(30, 4, "", 1, 0, 'C', 0);
                    $pdf->cell(90, 4, $value["nota"], 1, 1, 'C', 0);
                }
                $pdf->Ln();
                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->setX(80);
                $pdf->Cell(50, 4, "ACTIVOS MULTIAFILIACION", 0, 0, 'C', 0);
                $pdf->Ln();
                $pdf->Ln();
                $pdf->setX(18);
                $pdf->cell(30, 4, "Linea", 1, 0, 'C', 0);
                $pdf->cell(30, 4, "Cedula", 1, 0, 'C', 0);
                $pdf->cell(30, 4, "Nombre", 1, 0, 'C', 0);
                $pdf->cell(90, 4, "Nota", 1, 1, 'C', 0);
                $pdf->SetFont('Arial', '', 8);
                foreach ($actmult['s168act'] as $value) {
                    $pdf->setX(18);
                    $pdf->cell(30, 4, $aux2++, 1, 0, 'C', 0);
                    $pdf->cell(30, 4, $value["cedtra"], 1, 0, 'C', 0);
                    $pdf->cell(30, 4, $value["nombre"], 1, 0, 'C', 0);
                    $pdf->cell(90, 4, $value["nota"], 1, 1, 'C', 0);
                }

                $file = "public/temp/" . "activacion_masiva" . ".pdf";
                $pdf->Output($file, "F");

                if ($error == false) {
                    parent::finishTrans();
                    $response = parent::successFunc("Se digitaron $aprobados trabajador con exito", $html);
                    $response["file"] = $file;
                    return $this->renderText(json_encode($response));
                } else {
                    $response = parent::errorFunc("No se pudo realizar la accion.", $html);
                    return $this->renderText(json_encode($response));
                }
            } catch (DbException $e) {
                parent::setLogger($e->getMessage());
                parent::ErrorTrans();
            }
        } catch (TransactionFailed $e) {
            $response = parent::errorFunc("No se pudo realizar la accion activa masiva");
            return $this->renderText(json_encode($response));
        }
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

        $this->setParamToView("_tipper", $_tipper);
        $this->setParamToView("_coddoc", $_coddoc);
        $this->setParamToView("_calemp", $_calemp);
        $this->setParamToView("_codciu", $_codciu);
        $this->setParamToView("_codzon", $_codzon);
        $this->setParamToView("_codact", $_codact);
        $this->setParamToView("_tipsoc", $_tipsoc);
        $this->setParamToView("_tipemp", $_tipemp);
        $this->setParamToView("_codcaj", $_codcaj);
        $this->setParamToView("_ciupri", $_ciupri);
        $this->setParamToView("tipo", parent::getActUser("tipo"));
    }

    function loadDisplaySubsidio($empresa)
    {
        /* Tag::displayTo("tipdoc", $empresa['coddoc']);
        Tag::displayTo("digver", $empresa['digver']);
        Tag::displayTo("nit", $empresa['nit']);
        Tag::displayTo("sigla", $empresa['sigla']);
        Tag::displayTo("calemp", $empresa['calemp']);
        Tag::displayTo("cedrep", $empresa['cedrep']);
        Tag::displayTo("repleg", $empresa['repleg']);
        Tag::displayTo("telefono", $empresa['telefono']);
        Tag::displayTo("fax", $empresa['fax']);
        Tag::displayTo("email", $empresa['email']);
        Tag::displayTo("tottra", $empresa['tottra']);
        Tag::displayTo("ciupri", $empresa['ciupri']);
        Tag::displayTo("prinom", $empresa['prinom']);
        Tag::displayTo("segnom", $empresa['segnom']);
        Tag::displayTo("priape", $empresa['priape']);
        Tag::displayTo("segape", $empresa['segape']);
        Tag::displayTo("priaperepleg", $empresa['priaperepleg']);
        Tag::displayTo("segnomrepleg", $empresa['segnomrepleg']);
        Tag::displayTo("prinomrepleg", $empresa['prinomrepleg']);
        Tag::displayTo("segaperepleg", $empresa['segaperepleg']);
        Tag::displayTo("razsoc", $empresa['razsoc']);
        Tag::displayTo("tipper", $empresa['tipper']);
        Tag::displayTo("matmer", $empresa['matmer']);
        Tag::displayTo("direccion", $empresa['direccion']);
        Tag::displayTo("tipsoc", $empresa['tipsoc']);
        Tag::displayTo("codact", $empresa['codact']);
        Tag::displayTo("tipemp", $empresa['tipemp']);
        Tag::displayTo("codcaj", $empresa['codcaj']); */
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

    function consulta_nucleoAction()
    {
        try {
            Core::importLibrary("ParamsTrabajador", "Collections");
            Core::importLibrary("ParamsConyuge", "Collections");
            Core::importLibrary("ParamsBeneficiario", "Collections");

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
