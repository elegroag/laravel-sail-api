<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsBeneficiario;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio34;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Gener09;
use App\Services\Entidades\BeneficiarioService;
use App\Services\Entidades\TrabajadorService;
use App\Services\Entidades\ConyugeService;
use App\Services\FormulariosAdjuntos\BeneficiarioAdjuntoService;
use App\Services\FormulariosAdjuntos\Formularios;
use App\Services\Tag;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use App\Services\Utils\Date;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;


class BeneficiarioController extends ApplicationController
{

    protected $tipopc = "4";
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
        if (
            $this->tipo == 'E' ||
            $this->tipo == 'I' ||
            $this->tipo == 'O' ||
            $this->tipo == 'F'
        ) {
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array('nit' => parent::getActUser("documento"))
                )
            );

            $empresa = $procesadorComando->toArray();
            if (!isset($empresa['data'])) {
                set_flashdata("error", array(
                    "msj" => 'Error al acceder al servicio de consulta de empresa.',
                    "code" => 401
                ));
                redirect('principal/index');
                exit;
            }

            if ($empresa['data']['estado'] === 'I') {
                set_flashdata("error", array(
                    "msj" => 'La empresa ya no está activa para realizar afiliación de beneficiarios.',
                    "code" => 401
                ));
                redirect('principal/index');
                exit;
            }
        }
        return view('mercurio/beneficiario/index', [
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'title' => 'Afiliación de beneficiarios'
        ]);
    }

    public function traerConyugesAction(Request $request)
    {
        $this->setResponse("ajax");
        $cedtra = $request->input("cedtra");

        $cedcons = array();
        $mercurio32 = $this->Mercurio32->find(" cedtra='{$cedtra}'");
        foreach ($mercurio32 as $conyuge) {
            $cedcons[$conyuge->getCedcon()] = $conyuge->getCedcon() . "-" . $conyuge->getPriape() . " " . $conyuge->getSegape() . " " . $conyuge->getPrinom();
        }

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "listar_conyuges_trabajador",
                "params" => array(
                    "cedtra" => $cedtra
                )
            )
        );

        $subsi20 = $ps->toArray();
        if ($subsi20['success'] == true) {
            $subsi20 = $subsi20['data'];
            if (count($subsi20) > 0) {
                foreach ($subsi20 as $msubsi20) {
                    $cedcons[$msubsi20['cedcon']] = $msubsi20['cedcon'] . "-" . $msubsi20['priape'] . " " . $msubsi20['prinom'];
                }
            }
        }

        $response = Tag::selectStatic("cedcon", $cedcons, "use_dummy: true", "dummyValue: ", "class: form-control");
        return $this->renderObject($response, false);
    }

    public function borrarArchivoAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $numero = $this->cleanInput($request->input('id'));
            $coddoc = $this->cleanInput($request->input('coddoc'));

            $mercurio01 = $this->Mercurio01->findFirst();
            $mercurio37 = $this->Mercurio37->findFirst("tipopc='{$this->tipopc}' and numero='{$numero}' and coddoc='{$coddoc}'");

            $filepath = base_path() . '' . $mercurio01->getPath() . $mercurio37->getArchivo();
            if (file_exists($filepath)) {
                unlink(base_path() . '' . $mercurio01->getPath() . $mercurio37->getArchivo());
            }

            $this->Mercurio37->deleteAll("tipopc='{$this->tipopc}' and numero='{$numero}' and coddoc='{$coddoc}'");

            $response = array(
                "success" => true,
                "msj" => "El archivo se borro de forma correcta"
            );
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    public function enviarCajaAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $this->cleanInput($request->input('id'));
            $beneficiarioService = new BeneficiarioService();
            //$beneficiarioService->setTransa();

            $asignarFuncionario = new AsignarFuncionario();
            $usuario = $asignarFuncionario->asignar($this->tipopc, parent::getActUser("codciu"));

            $beneficiarioService->enviarCaja(new SenderValidationCaja(), $id, $usuario);
            //$beneficiarioService->endTransa();

            $salida = array(
                "success" => true,
                "msj" => "El envio de la solicitud se ha completado con éxito"
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    public function traerBeneficiarioAction(Request $request)
    {
        $this->setResponse("ajax");
        $numdoc = $request->input("numdoc");
        $mercurio34 = new Mercurio34();
        $datos_beneficiario = array();

        $ps = Comman::Api();
        $ps->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "beneficiario",
                "params" => array(
                    "documento" => $numdoc
                )
            )
        );
        $out = $ps->toArray();
        if ($out['success'] == true) {
            $datos_beneficiario = $out['data'];
        }

        foreach ($datos_beneficiario as $key => $value) {
            if (is_numeric($key)) continue;
            if ($mercurio34->isAttribute($key))
                $mercurio34->writeAttribute($key, "$value");
        }
        return $this->renderObject($mercurio34->getArray(), false);
    }

    public function borrarAction(Request $request)
    {
        try {
            $this->setResponse("ajax");
            $id = $request->input('id');
            $mercurio34 = $this->Mercurio34->findFirst("id = '$id'");
            $modelos = array("Mercurio34");
            //$Transaccion = parent::startTrans($modelos);
            //$response = parent::startFunc();
            $this->Mercurio34->deleteAll("id = '$id'");
            //parent::finishTrans();
            $response = "Borrado Con Exito";
            return $this->renderObject(json_encode($response));
        } catch (DebugException $e) {
            $response = "No se puede Borrar el Registro";
        }
        return $this->renderObject(json_encode($response));
    }

    public function buscarBeneficiarios($estado)
    {

        //usuario empresa, unica solicitud de afiliación
        $documento = parent::getActUser("documento");
        $tipo = parent::getActUser("tipo");
        $coddoc = parent::getActUser("coddoc");

        if (empty($estado)) {
            $beneficiarios = $this->db->fetchAll("SELECT * FROM mercurio34
			WHERE  tipo='{$tipo}' AND coddoc='{$coddoc}' AND documento='{$documento}' AND estado IN('T','D','P','A','X') ORDER BY id, estado DESC");
        } else {
            $beneficiarios = $this->db->fetchAll("SELECT * FROM mercurio34
			WHERE tipo='{$tipo}' AND coddoc='{$coddoc}' AND documento='{$documento}' AND estado='{$estado}' ORDER BY id DESC");
        }

        foreach ($beneficiarios as $ai => $row) {
            $rqs = $this->db->fetchOne("SELECT count(mercurio10.numero) as cantidad
                FROM mercurio10
                LEFT JOIN mercurio34 ON mercurio34.id=mercurio10.numero
                WHERE mercurio10.tipopc='{$this->tipopc}' AND
                mercurio34.id ='{$row['id']}'
            ");
            $trayecto = $this->db->fetchOne("SELECT max(mercurio10.item), mercurio10.*
                FROM mercurio10
                LEFT JOIN mercurio34 ON mercurio34.id=mercurio10.numero
                WHERE mercurio10.tipopc='{$this->tipopc}' AND
                mercurio34.id ='{$row['id']}' LIMIT 1
            ");

            $beneficiarios[$ai] = $row;
            $beneficiarios[$ai]["cantidad_eventos"] = $rqs['cantidad'];
            $beneficiarios[$ai]["fecha_ultima_solicitud"] = $trayecto['fecsis'];
            switch ($row['estado']) {
                case 'T':
                    $beneficiarios[$ai]["estado_detalle"] = "TEMPORAL";
                    break;
                case 'D':
                    $beneficiarios[$ai]["estado_detalle"] = "DEVUELTO";
                    break;
                case 'A':
                    $beneficiarios[$ai]["estado_detalle"] = "APROBADO";
                    break;
                case 'X':
                    $beneficiarios[$ai]["estado_detalle"] = "RECHAZADO";
                    break;
                case 'P':
                    $beneficiarios[$ai]["estado_detalle"] = "Pendinete De Validación CAJA";
                    break;
                default:
                    $beneficiarios[$ai]["estado_detalle"] = "T";
                    break;
            }
        }
        return $beneficiarios;
    }

    public function cancelar_solicitudAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $documento = parent::getActUser("documento");
            $id = $request->input('id');

            $m34 = $this->Mercurio34->findFirst("id='{$id}' AND documento='{$documento}' ");
            if ($m34) {
                if ($m34->getEstado() != 'T') {
                    $this->Mercurio10->deleteAll("numero='{$id}' AND tipopc='{$this->tipopc}'");
                }
                $this->Mercurio34->deleteAll("id='{$id}' AND documento='{$documento}' ");
            }
            $salida = array(
                "success" => true,
                "msj" => "El registro se borro con éxito del sistema."
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    /**
     * buscar_conyuges_trabajadorAction function
     * @return void
     */
    public function buscar_conyuges_trabajadorAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $cedtra = $request->input("cedtra");
            $documento =  parent::getActUser("documento");
            $tipo = parent::getActUser("tipo");
            $procesadorComando = Comman::Api();
            $datos_captura = array();

            //solo conyuges activas a buscar
            if ($tipo == 'T') {
                $trabajador = (new Mercurio31)->findFirst("documento='{$documento}' AND estado='A'");
                $documento = ($trabajador) ? $trabajador->getCedtra() : $documento;

                $procesadorComando->runCli(
                    array(
                        "servicio" => "ComfacaAfilia",
                        "metodo" => "listar_conyuges_trabajador",
                        "params" => array(
                            "cedtra" => $documento
                        )
                    )
                );

                $out = $procesadorComando->toArray();
                if ($out['success'] == true) {
                    $datos_captura = $out['data'];
                }
            } else {
                $empresa = $this->Mercurio30->findFirst("documento='{$documento}' AND estado='A'");
                $nit = ($empresa) ? $empresa->getNit() : $documento;
                $procesadorComando->runCli(
                    array(
                        "servicio" => "ComfacaAfilia",
                        "metodo" => "listar_conyuges",
                        "params" => array(
                            "nit" => $nit
                        )
                    )
                );
                $out = $procesadorComando->toArray();
                if ($out['success'] == true) {
                    $datos_captura = $out['data'];
                }
            }

            $_cedcon = array();
            foreach ($datos_captura as $data) {
                if ($cedtra == '') {
                    $_cedcon[$data['cedcon']] = $data['cedcon'] . ' - ' . $data['nombre'];
                } else {
                    if ($cedtra == $data['cedtra']) {
                        $_cedcon[$data['cedcon']] = $data['cedcon'] . ' - ' . $data['nombre'];
                    }
                }
            }

            $conyuguesPendientes = (new Mercurio32)->getFind("documento='{$documento}' AND estado NOT IN('I','X')");
            foreach ($conyuguesPendientes as $conCp) {
                if (!isset($_cedcon[$conCp->getCedcon()])) {
                    $_cedcon[$conCp->getCedcon()] = $conCp->getCedcon() . ' - ' . $conCp->getPrinom() . ' ' . $conCp->getSegnom() . ' ' . $conCp->getPriape() . ' ' . $conCp->getSegape();
                }
            }

            $html = Tag::selectStatic("cedcon", $_cedcon, "use_dummy: true", "dummyValue: ", "class: form-control");
            $salida = array(
                "success"  => true,
                "list" => $html,

            );
        } catch (DebugException $e) {
            $salida = array(
                "success"  => false,
                "list" => "",
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    function mapper()
    {
        return array(
            "cedtra" => "cedula",
            "tipdoc" => "tipo documento",
            "priape" => "primer apellido",
            "segape" => "segundo apellido",
            "prinom" => "primer nombre",
            "segnom" => "segundo nombre",
            "fecnac" => "fecha nacimiento",
            "ciunac" => "codigo ciudad nacimiento",
            "estciv" => "estado civil",
            "cabhog" => "cabeza hogar",
            "codciu" => "código ciudad residencia",
            "codzon" => "código ciudad laboral",
            "fecing" => "fecha ingreso",
            "tipsal" => "tipo salario",
            "captra" => "capacidad trabajar",
            "tipdis" => "tipo discapacidad",
            "nivedu" => "nivel educativo",
            "rural" => "residencia rural",
            "horas" => "horas trabajar",
            "tipcon" => "tipo contrato",
            "trasin" => "sindicalizado",
            "tipafi" => "tipo afiliado",
            "orisex" => "orientación sexual",
            "facvul" => "factor vulnerabilidad",
            "peretn" => "etnica",
            "dirlab" => "direccion laboral",
            "autoriza" => "tratamiento datos",
            "tipjor" => "tipo jornada",
            "ruralt" => "labor rural",
            "comision" => "recibe comisión",
            "fecsol" => "fecha solicitid"
        );
    }

    public function download_docsAction($archivo = "")
    {
        $fichero = "public/docs/formulario_mercurio/" . $archivo;
        $ext = substr(strrchr($archivo, "."), 1);
        if (file_exists($fichero)) {
            header('Content-Description: File Transfer');
            header("Content-Type: application/{$ext}");
            header("Content-Disposition: attachment; filename={$archivo}");
            header('Cache-Control: must-revalidate');
            header('Expires: 0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fichero));
            ob_clean();
            readfile($fichero);
            exit;
        } else {
            redirect('generador/reportes');
            exit();
        }
    }

    public function download_reporteAction($archivo = "")
    {
        $fichero = "public/temp/" . $archivo;
        if (file_exists($fichero)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header("Content-Disposition: attachment; filename={$archivo}");
            header('Cache-Control: must-revalidate');
            header('Expires: 0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fichero));
            ob_clean();
            readfile($fichero);
            exit;
        } else {
            redirect('trabajador/index');
            exit();
        }
    }

    public function descargar_declaracionAction()
    {
        $this->setResponse("view");
        $archivo = "declaracion_juramentada_nueva.pdf";
        $fichero = "public/docs/formulario_mercurio/" . $archivo;
        $ext = substr(strrchr($archivo, "."), 1);
        header('Content-Description: File Transfer');
        header("Content-Type: application/{$ext}");
        header("Content-Disposition: attachment; filename={$archivo}");
        header('Cache-Control: must-revalidate');
        header('Expires: 0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fichero));
        ob_clean();
        readfile($fichero);
        exit;
    }

    public function paramsAction()
    {
        $this->setResponse("ajax");
        try {
            $nombre = parent::getActUser("nombre");
            $documento = parent::getActUser("documento");
            $tipo = parent::getActUser("tipo");
            $coddoc = parent::getActUser("coddoc");

            $listAfiliados = false;
            $listConyuges = false;
            $conyuges = array();
            $cedtras = array();

            $trabajadorService = new TrabajadorService();
            if ($tipo == 'E') {
                $cedtras = $trabajadorService->findRequestByDocumentoCoddoc($documento, $coddoc);

                if ($list = $trabajadorService->findApiTrabajadoresByNit($documento)) {
                    $listAfiliados = array();
                    foreach ($list as $row) {
                        $listAfiliados[] = array('cedula' => $row['cedtra'], 'nombre_completo' => $row['nombre']);
                    }
                }
            } else {
                $cedtras[] = array('cedula' => $documento,  'nombre_completo' => $nombre);
            }

            $conyugeService = new ConyugeService();
            if ($tipo == 'E') {
                $conyuges[] = $conyugeService->findRequestByDocumentoCoddoc($documento, $coddoc);
                $list = $conyugeService->findApiConyugesByNit($documento);
                $listConyuges = array();
                foreach ($list as $row) {
                    $listConyuges[] = array('cedula' => $row['cedcon'], 'nombre_completo' => $row['nombre']);
                }
            } else {
                $conyuges = $conyugeService->findRequestByCedtra($documento);
            }

            $codciu = array();
            $mgener09 = new Gener09();
            foreach ($mgener09->find("*", "conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_beneficiarios"
                ),
                false
            );

            $biourbana = array('S' => 'SI', 'N' => 'NO');
            $biodesco = array('S' => 'SI', 'N' => 'NO');
            $paramsConyuge = new ParamsBeneficiario();
            $paramsConyuge->setDatosCaptura($procesadorComando->toArray());

            $salida = array(
                "success" => true,
                "data" => array(
                    "biotipdoc" => ParamsBeneficiario::getTiposDocumentos(),
                    "tipdoc" => ParamsBeneficiario::getTiposDocumentos(),
                    "sexo" => ParamsBeneficiario::getSexos(),
                    "estciv" => ParamsBeneficiario::getEstadoCivil(),
                    "ciunac" => ParamsBeneficiario::getCiudades(),
                    "captra" => ParamsBeneficiario::getCapacidadTrabajar(),
                    "parent" => ParamsBeneficiario::getParentesco(),
                    "huerfano" => ParamsBeneficiario::getHuerfano(),
                    "tiphij" => ParamsBeneficiario::getTipoHijo(),
                    "nivedu" => ParamsBeneficiario::getNivelEducativo(),
                    "tipdis" => ParamsBeneficiario::getTipoDiscapacidad(),
                    "calendario" => ParamsBeneficiario::getCalendario(),
                    'resguardo_id' => ParamsBeneficiario::getResguardos(),
                    'pub_indigena_id' => ParamsBeneficiario::getPueblosIndigenas(),
                    "biocodciu" => ParamsBeneficiario::getCiudades(),
                    'peretn' => ParamsBeneficiario::getPertenenciaEtnicas(),
                    'tippag' => ParamsBeneficiario::getTipoPago(),
                    'codban' => ParamsBeneficiario::getBancos(),
                    'tipcue' => ParamsBeneficiario::getTipoCuenta(),
                    'biourbana' => $biourbana,
                    'biodesco' => $biodesco,
                    "trabajadores" => $cedtras,
                    "conyuges" => $conyuges,
                    'list_conyuges' => $listConyuges,
                    "convive" => $this->Mercurio34->getConvive(),
                    'list_afiliados' => $listAfiliados
                ),
                "msj" => 'OK'
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage() . ' ' . $e->getLine() . ' ' . basename($e->getFile())
            );
        }
        return $this->renderObject($salida);
    }

    public function renderTableAction($estado = '')
    {
        $this->setResponse("view");
        $benService = new BeneficiarioService();
        $html = View::render(
            "beneficiario/tmp/solicitudes",
            array(
                "path" => base_path(),
                "beneficiarios" => $benService->findAllByEstado($estado)
            )
        );
        return $this->renderObject($html);
    }

    public function searchRequestAction($id)
    {
        $this->setResponse("ajax");
        try {
            if (is_null($id)) {
                throw new DebugException("Error no hay solicitud a buscar", 301);
            }
            $documento = parent::getActUser("documento");
            $coddoc = parent::getActUser("coddoc");

            $solicitud = $this->Mercurio34->findFirst(" id='{$id}' AND documento='{$documento}' AND coddoc='{$coddoc}'");
            if ($solicitud == False) {
                throw new DebugException("Error la solicitud no está disponible para acceder.", 301);
            } else {
                $data = $solicitud->getArray();
            }
            $salida = array(
                "success" => true,
                "data" => $data,
                "msj" => 'OK'
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function validaAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $documento = parent::getActUser("documento");
            $coddoc = parent::getActUser("coddoc");

            $numdoc = $request->input('numdoc');
            $solicitud_previa = (new Mercurio34)->findFirst(" numdoc='{$numdoc}' and documento='{$documento}' and coddoc='{$coddoc}'");

            $beneficiario = false;
            if ($solicitud_previa) {
                $beneficiario = $solicitud_previa->getArray();
            }

            if (!$beneficiario) {
                $benefiService = new BeneficiarioService();
                $rqs = $benefiService->buscarBeneficiarioSubsidio($numdoc);
                if ($rqs) {
                    $beneficiario = (count($rqs['data']) > 0) ? $rqs['data'] : false;
                }
            }

            $response = array(
                "success" => true,
                "solicitud_previa" => ($solicitud_previa > 0) ? true : false,
                "beneficiario" => $beneficiario
            );
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }

        return $this->renderObject($response);
    }

    function serializeData()
    {
        $fecsol = Carbon::now();
        $asignarFuncionario = new AsignarFuncionario();
        $request = request();

        return array(
            'id' => $this->clp($request, 'id'),
            'usuario' => $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']),
            'log' => $this->user['documento'],
            'fecsol' => $fecsol->format('Y-m-d'),
            'nit' => $this->clp($request, 'nit'),
            'cedtra' => $this->clp($request, 'cedtra'),
            'cedcon' => $this->clp($request, 'cedcon'),
            'tipdoc' => $this->clp($request, 'tipdoc'),
            'numdoc' => $this->clp($request, 'numdoc'),
            'priape' => $this->clp($request, 'priape'),
            'segape' => $this->clp($request, 'segape'),
            'prinom' => $this->clp($request, 'prinom'),
            'segnom' => $this->clp($request, 'segnom'),
            'fecnac' => $this->clp($request, 'fecnac'),
            'ciunac' => $this->clp($request, 'ciunac'),
            'sexo' => $this->clp($request, 'sexo'),
            'parent' => $this->clp($request, 'parent'),
            'huerfano' => $this->clp($request, 'huerfano'),
            'tiphij' => $this->clp($request, 'tiphij'),
            'nivedu' => $this->clp($request, 'nivedu'),
            'captra' => $this->clp($request, 'captra'),
            'tipdis' => $this->clp($request, 'tipdis'),
            'calendario' => $this->clp($request, 'calendario'),
            'cedacu' => $this->clp($request, 'cedacu'),
            'biocedu' => $this->clp($request, 'biocedu'),
            'biotipdoc' => $this->clp($request, 'biotipdoc'),
            'biocodciu' => $this->clp($request, 'biocodciu'),
            'biodesco' => $this->clp($request, 'biodesco'),
            'biodire' => $this->clp($request, 'biodire'),
            'bioemail' => $this->clp($request, 'bioemail'),
            'biophone' => $this->clp($request, 'biophone'),
            'biopriape' => $this->clp($request, 'biopriape'),
            'bioprinom' => $this->clp($request, 'bioprinom'),
            'biosegape' => $this->clp($request, 'biosegape'),
            'biosegnom' => $this->clp($request, 'biosegnom'),
            'biourbana' => $this->clp($request, 'biourbana'),
            'peretn' => $this->clp($request, 'peretn'),
            'resguardo_id' => $this->clp($request, 'resguardo_id'),
            'pub_indigena_id' => $this->clp($request, 'pub_indigena_id'),
            'tippag' => $this->clp($request, 'tippag'),
            'tipcue' => $this->clp($request, 'tipcue'),
            'numcue' => $this->clp($request, 'numcue'),
            'codban' => $this->clp($request, 'codban'),
            'tipo' => $this->tipo,
            'coddoc' => $this->user['coddoc'],
            'documento' => $this->user['documento']
        );
    }

    public function guardarAction(Request $request)
    {
        $this->setResponse("ajax");
        $benefiService = new BeneficiarioService();
        //$benefiService->setTransa();
        try {
            $id = $this->cleanInput($request->input('id'));
            $params = $this->serializeData();

            $solicitud = null;
            if (is_null($id) || $id == '') {
                $params['id'] = null;
                $params['usuario'] = 2;
                $params['estado'] = 'T';
                $solicitud = $benefiService->createByFormData($params);
                $soli = $solicitud->getArray();
                $id = $soli['id'];
            } else {
                $res = $benefiService->updateByFormData($id, $params);
                if ($res == false) {
                    throw new DebugException("Error no se actualizo los datos", 301);
                }
            }

            //$benefiService->endTransa();
            $solicitud = $benefiService->findById($id);

            $beneficiarioAdjuntoService = new BeneficiarioAdjuntoService($solicitud);
            $out = $beneficiarioAdjuntoService->formulario()->getResult();
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => 1,
                    'id' => $solicitud->getId()
                )
            ))->salvarDatos($out);

            $out = $beneficiarioAdjuntoService->declaraJurament()->getResult();
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => 4,
                    'id' => $solicitud->getId()
                )
            ))->salvarDatos($out);

            $salida = array(
                "msj" => "Proceso se ha completado con éxito",
                "success" => true,
                "data" => $solicitud->getArray()
            );
        } catch (DebugException $erro) {
            //$benefiService->closeTransa($erro->getMessage());
            $salida = [
                "error" => $erro->getMessage(),
                "success" => false,
            ];
        }

        return $this->renderObject($salida);
    }

    public function consultaDocumentosAction($id)
    {
        $this->setResponse("ajax");
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $benService = new BeneficiarioService();

            $sindepe = $this->Mercurio34->findFirst("id='{$id}' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado NOT IN('I','X')");
            if ($sindepe == false) {
                throw new DebugException("Error no se puede identificar el propietario de la solicitud", 301);
            }
            $salida = array(
                'success' => true,
                'data' => $benService->dataArchivosRequeridos($sindepe),
                'msj' => 'OK'
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    public function formularioAction($id)
    {
        try {
            $paramsTrabajador = new ParamsTrabajador();
            $adicionPersonaCargo =  true;
            $tipo = $this->user['tipo'];
            $documento = $this->user['documento'];

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_trabajadores"
                ),
                false
            );

            $datos_captura = $procesadorComando->toArray();
            $paramsTrabajador->setDatosCaptura($datos_captura);

            $mercurio34 = $this->Mercurio34->findBySql("SELECT * FROM mercurio34 WHERE id='{$id}'");
            $cedtra = $mercurio34->getCedtra();

            $nit = ($mercurio34->getNit()) ? $mercurio34->getNit() : 0;
            if ($nit == 0 && $tipo == 'E') $nit = $documento;

            //traer primero de sisuweb
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "trabajador_empresa",
                    "params" => array(
                        "cedtra" => $cedtra,
                        "nit" => $nit,
                        "estado" => "A"
                    )
                )
            );

            $mercurio31 = false;
            if ($out = $procesadorComando->toArray()) {
                $datos_trabajador = ($out['success'] == true) ? $out['data'] : null;
                if ($datos_trabajador) {
                    if ($datos_trabajador['nit'] == $nit) {
                        $mercurio31 = new Mercurio31();
                        foreach ($datos_trabajador as $key => $value) {
                            if ($mercurio31->isAttribute($key)) $mercurio31->writeAttribute($key, "$value");
                            if ($key == 'fecafi') $mercurio31->writeAttribute('fecing', "$value");
                            if ($key == 'tipcot') $mercurio31->writeAttribute('tipafi', "$value");
                            if ($key == 'coddoc') $mercurio31->writeAttribute('tipdoc', "$value");
                        }
                    }
                }
            }

            if ($mercurio31 == false) {
                $mercurio31 = $this->Mercurio31->findBySql("SELECT * FROM mercurio31 WHERE documento='{$documento}' and cedtra='{$cedtra}' and nit='{$nit}' ORDER BY fecsol DESC;");
            }

            if ($mercurio31 == false) {
                throw new DebugException("El trabajador no esta correctamente afiliado.", 505);
            }


            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        "nit" => $mercurio31->getNit()
                    )
                )
            );

            $empresa = false;
            if ($out = $procesadorComando->toArray()) {
                $datos_empresa = ($out['success'] == true) ? $out['data'] : false;
                if ($datos_empresa) {
                    $empresa = new Mercurio30();
                    $datos_empresa['telefono'] = ($datos_empresa['telr'] == '') ? $datos_empresa['telefono'] : $datos_empresa['telr'];
                    $empresa->createAttributes($datos_empresa);
                }
            }

            if (!$empresa) {
                throw new DebugException("Error los datos de la empresa no estan disponibles", 505);
            }

            /*para beneficiarios hijos buscar conyuge*/
            $mercurio32 = false;
            if ($mercurio34->getParent() == 1) {
                $mercurio32 = $this->Mercurio32->findFirst("cedtra='{$cedtra}' AND estado NOT IN('I','X') AND cedcon='{$mercurio34->getCedcon()}'");
                if (!$mercurio32) {

                    $procesadorComando->runCli(
                        array(
                            "servicio" => "ComfacaAfilia",
                            "metodo" => "conyugue_trabajador_beneficiario",
                            "params" => array(
                                'documento' => $mercurio34->getNumdoc(),
                                'cedtra' => $mercurio34->getCedtra()
                            )
                        )
                    );

                    if ($out = $procesadorComando->toArray()) {

                        $data = ($out['success']) ? $out['data'] : array();
                        $has = 0;
                        foreach ($data as $datos_conyuge) {
                            if ($datos_conyuge['cedcon'] == $mercurio34->getCedcon()) {
                                $has++;
                                break;
                            }
                        }
                        if ($has > 0) {
                            $mercurio32 = new Mercurio32();
                            foreach ($datos_conyuge as $key => $value) {
                                if ($mercurio32->isAttribute($key)) $mercurio32->writeAttribute($key, "$value");
                                if ($key == 'coddoc') $mercurio32->writeAttribute('tipdoc', "$value");
                                if ($key == 'codzon') $mercurio32->writeAttribute('ciures', "$value");
                            }
                        }
                    }
                }

                if (!$mercurio32) {

                    $procesadorComando = Comman::Api();
                    $procesadorComando->runCli(
                        array(
                            "servicio" => "ComfacaAfilia",
                            "metodo" => "listar_conyuges_trabajador",
                            "params" => array(
                                "cedtra" => $mercurio34->getCedtra()
                            )
                        )
                    );

                    if ($out = $procesadorComando->toArray()) {
                        if ($out['success'] == true) {
                            $out = $out['data'];
                            $has = 0;
                            foreach ($out as $datos_conyuge) {
                                if ($datos_conyuge['cedcon'] == $mercurio34->getCedcon()) {
                                    $has++;
                                    break;
                                }
                            }
                            if ($has > 0) {
                                $mercurio32 = new Mercurio32();
                                foreach ($datos_conyuge as $key => $value) {
                                    if ($mercurio32->isAttribute($key)) $mercurio32->writeAttribute($key, "$value");
                                    if ($key == 'coddoc') $mercurio32->writeAttribute('tipdoc', "$value");
                                    if ($key == 'codzon') $mercurio32->writeAttribute('ciures', "$value");
                                }
                            }
                        }
                    }
                }
            }

            //buscar mas beneficiarios al formulario
            $beneficiariosTodos = $this->Mercurio34->find(
                " mercurio34.* ",
                "conditions: mercurio34.cedtra='{$cedtra}' AND mercurio34.estado IN('P','D','T') AND mercurio34.documento='{$documento}'"
            );

            $file = "formulario_afiliacion_acargo{$cedtra}.pdf";
            $formularios = new Formularios();

            $formularios->trabajadorAfiliacion(
                array(
                    'trabajador' => $mercurio31,
                    'empresa' => $empresa,
                    'adicionPersonaCargo' => $adicionPersonaCargo,
                    'conyuge' => $mercurio32,
                    'beneficiarios' => $beneficiariosTodos
                ),
                $file
            )->outFile();
        } catch (DebugException $e) {

            $msj = $e->getMessage() . ' linea: ' . $e->getLine();
            set_flashdata("error", array(
                "msj" => $msj
            ));

            return redirect('beneficiario.index');
        }
    }

    public function guardarArchivoAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $coddoc = $this->cleanInput($request->input('coddoc'));

            $guardarArchivoService = new GuardarArchivoService(array(
                'tipopc' => $this->tipopc,
                'coddoc' => $coddoc,
            ));
            $mercurio37 = $guardarArchivoService->main();
            $response = array(
                'success' => true,
                'msj' => 'Ok archivo procesado',
                'data' => $mercurio37->getArray()
            );
        } catch (DebugException $ert) {
            $response = array(
                'success' => false,
                'msj' => $ert->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    public function seguimientoAction($id)
    {
        $this->setResponse("ajax");
        try {
            $beneficiarioService = new BeneficiarioService();
            $out = $beneficiarioService->consultaSeguimiento($id);
            $salida = array(
                "success" => true,
                "data" => $out
            );
        } catch (DebugException $e) {
            $salida = array('success' => false, 'msj' => $e->getMessage());
        }
        return $this->renderObject($salida, false);
    }
}
