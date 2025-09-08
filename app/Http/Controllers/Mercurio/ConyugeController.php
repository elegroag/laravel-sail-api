<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsConyuge;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio10;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio37;
use App\Services\Entidades\ConyugeService;
use App\Services\Entidades\TrabajadorService;
use App\Services\FormulariosAdjuntos\ConyugeAdjuntoService;
use App\Services\FormulariosAdjuntos\Formularios;
use App\Services\PreparaFormularios\TrabajadorFormulario;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConyugeController extends ApplicationController
{

    protected $asignarFuncionario;
    protected $tipopc = "3";
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
        $tipo = $this->tipo;
        $empresa = null;
        if (
            $tipo == 'E' ||
            $tipo == 'I' ||
            $tipo == 'O' ||
            $tipo == 'F'
        ) {
            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array('nit' => $this->user['documento'])
                )
            );

            $empresa = $procesadorComando->toArray();
            if (!isset($empresa['data'])) {
                set_flashdata("error", array(
                    "msj" => 'Error al acceder al servicio de consulta de empresa.',
                    "code" => 401
                ));
                return redirect()->route('principal/index');
                exit;
            }

            if ($empresa['data']['estado'] === 'I') {
                set_flashdata("error", array(
                    "msj" => 'La empresa ya no está activa para realizar afiliación de beneficiarios.',
                    "code" => 401
                ));
                return redirect()->route('principal/index');
                exit;
            }
        }
        return view('mercurio.conyuge.index', [
            'documento' => $this->user['documento'],
            'tipo' => $this->tipo,
            'title' => "Afiliación de cónyuges",
            'empresa' => $empresa
        ]);
    }

    public function borrarArchivoAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $numero = $request->input('id');
            $coddoc = $request->input('coddoc');

            $mercurio01 = (new Mercurio01)->findFirst();
            $mercurio37 = (new Mercurio37)->findFirst("tipopc='{$this->tipopc}' and numero='{$numero}' and coddoc='{$coddoc}'");

            $filepath = storage_path() . '' . $mercurio01->getPath() . $mercurio37->getArchivo();
            if (file_exists($filepath)) {
                unlink(storage_path() . '' . $mercurio01->getPath() . $mercurio37->getArchivo());
            }
            Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $numero)
                ->where('coddoc', $coddoc)
                ->delete();

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

    public function guardarArchivoAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id');
            $coddoc = $request->input('coddoc');

            $guardarArchivoService = new GuardarArchivoService(array(
                'tipopc' => $this->tipopc,
                'coddoc' => $coddoc,
                'id' => $id
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

    public function traerConyugueAction(Request $request)
    {
        $this->setResponse("ajax");
        $cedcon = $request->input("cedcon");


        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_conyuge",
                "params" => array(
                    "cedcon" =>  $cedcon
                )
            )
        );

        $mercurio32 = new Mercurio32();
        $out = $procesadorComando->toArray();
        if ($out['success']) {
            $datos_conyuge = $out['data'];
            $mercurio32 = new Mercurio32($datos_conyuge);
        }

        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_trabajador",
                "params" =>  array(
                    'cedtra' => $cedcon
                )
            )
        );

        $out = $procesadorComando->toArray();
        if ($out['success']) {
            $datos_trabajador = $out['data'];
            $mercurio32 = new Mercurio32($datos_trabajador);
            $mercurio32->setTipdoc($datos_trabajador['coddoc']);
        }

        $this->renderObject($mercurio32->toArray());
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

    function buscarConyugues($db, $estado = '')
    {
        //usuario empresa, unica solicitud de afiliación
        $documento = parent::getActUser("documento");
        $tipo = parent::getActUser("tipo");
        $coddoc = parent::getActUser("coddoc");

        if (empty($estado)) {
            $mercurio32 = $db->inQueryAssoc("SELECT * FROM mercurio32 WHERE  tipo='{$tipo}' AND coddoc='{$coddoc}' AND documento='{$documento}' AND estado IN('T','D','P','A','X') ORDER BY id, estado DESC");
        } else {
            $mercurio32 = $db->inQueryAssoc("SELECT * FROM mercurio32 WHERE tipo='{$tipo}' AND coddoc='{$coddoc}' AND documento='{$documento}' AND estado='{$estado}' ORDER BY id DESC");
        }

        foreach ($mercurio32 as $ai => $row) {
            $rqs = $db->fetchOne("SELECT count(mercurio10.numero) as cantidad
                FROM mercurio10
                LEFT JOIN mercurio32 ON mercurio32.id=mercurio10.numero
                WHERE mercurio10.tipopc='{$this->tipopc}' AND
                mercurio32.id ='{$row['id']}'
            ");
            $trayecto = $db->fetchOne("SELECT max(mercurio10.item), mercurio10.*
                FROM mercurio10
                LEFT JOIN mercurio32 ON mercurio32.id=mercurio10.numero
                WHERE mercurio10.tipopc='{$this->tipopc}' AND
                mercurio32.id ='{$row['id']}' LIMIT 1
            ");

            $mercurio32[$ai] = $row;
            $mercurio32[$ai]["cantidad_eventos"] = $rqs['cantidad'];
            $mercurio32[$ai]["fecha_ultima_solicitud"] = $trayecto['fecsis'];
            switch ($row['estado']) {
                case 'T':
                    $mercurio32[$ai]["estado_detalle"] = "TEMPORAL";
                    break;
                case 'D':
                    $mercurio32[$ai]["estado_detalle"] = "DEVUELTO";
                    break;
                case 'A':
                    $mercurio32[$ai]["estado_detalle"] = "APROBADO";
                    break;
                case 'X':
                    $mercurio32[$ai]["estado_detalle"] = "RECHAZADO";
                    break;
                case 'P':
                    $mercurio32[$ai]["estado_detalle"] = "Pendiente De Validación CAJA";
                    break;
                default:
                    $mercurio32[$ai]["estado_detalle"] = "T";
                    break;
            }
        }
        return $mercurio32;
    }

    function serializeData(Request $request)
    {
        $asignarFuncionario = new AsignarFuncionario();
        $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);
        $fecsol = Carbon::now();
        return array(
            'fecsol' => $fecsol->format('Y-m-d'),
            'cedtra' => $request->input('cedtra'),
            'cedcon' => $request->input('cedcon'),
            'tipdoc' => $request->input('tipdoc'),
            'priape' => $request->input('priape'),
            'segape' => $request->input('segape'),
            'prinom' => $request->input('prinom'),
            'segnom' => $request->input('segnom'),
            'fecnac' => $request->input('fecnac'),
            'ciunac' => $request->input('ciunac'),
            'sexo' => $request->input('sexo'),
            'estciv' => $request->input('estciv'),
            'cabhog' => $request->input('cabhog'),
            'codciu' => $request->input('codciu'),
            'codzon' => $request->input('codzon'),
            'direccion' => $request->input('direccion'),
            'barrio' => $request->input('barrio'),
            'telefono' => $request->input('telefono'),
            'celular' => $request->input('celular'),
            'email' => $request->input('email'),
            'fecing' => (is_null($request->input('fecing')) || $request->input('fecing') == 'NULL') ? date('Y-m-d') : $request->input('fecing'),
            'salario' => ($request->input('salario')) ? $request->input('salario') : '0',
            'captra' => $request->input('captra'),
            'tipdis' => $request->input('tipdis'),
            'nivedu' => $request->input('nivedu'),
            'autoriza' => $request->input('autoriza'),
            'numcue' => $request->input('numcue'),
            'tippag' => $request->input('tippag'),
            'log' => $this->user['documento'],
            'comper' => $request->input('comper'),
            'tiecon' => $request->input('tiecon'),
            'ciures' => $request->input('ciures'),
            'tipviv' => $request->input('tipviv'),
            'codocu' => $request->input('codocu'),
            'codban' => $request->input('codban'),
            'empresalab' => $request->input('empresalab'),
            'peretn' => $request->input('peretn'),
            'resguardo_id' => $request->input('resguardo_id'),
            'pub_indigena_id' => $request->input('pub_indigena_id'),
            'tipo' => $this->tipo,
            'coddoc' => $this->user['coddoc'],
            'documento' => $this->user['documento'],
            'usuario' => $usuario
        );
    }

    public function guardarAction(Request $request)
    {
        $this->setResponse("ajax");
        $conyugeService = new ConyugeService();

        try {
            $id = $request->input('id');
            $params = $this->serializeData($request);

            if (is_null($id) || $id == "") {
                $params['id'] = null;
                $params['estado'] = 'T';
                $solicitud = $conyugeService->createByFormData($params);
                $id = $solicitud->getId();
            } else {
                $res = $conyugeService->updateByFormData($id, $params);
                if ($res == false) {
                    throw new DebugException("Error no se actualizo los datos", 301);
                }
            }
            //$conyugeService->endTransa();

            $solicitud = $conyugeService->findById($id);

            $conyugeAdjuntoService = new ConyugeAdjuntoService($solicitud);
            $out = $conyugeAdjuntoService->formulario()->getResult();
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => 1,
                    'id' => $solicitud->getId()
                )
            ))->salvarDatos($out);

            $out = $conyugeAdjuntoService->declaraJurament()->getResult();
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
            //$conyugeService->closeTransa($erro->getMessage());
            $salida = [
                'success' => false,
                'error' => $erro->getMessage()
            ];
        }
        return $this->renderObject($salida, false);
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

    public function enviarCajaAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $id = $request->input('id');
            $conygueService = new ConyugeService();
            //$conygueService->setTransa();

            $asignarFuncionario = new AsignarFuncionario();
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            $conygueService->enviarCaja(new SenderValidationCaja(), $id, $usuario);
            //$conygueService->endTransa();

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

    public function borrarAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $documento =  parent::getActUser("documento");
            $id = $request->input('id');

            $m32 = Mercurio32::where('id', $id)->where('documento', $documento)->first();
            if ($m32) {
                if ($m32->getEstado() != 'T') {
                    Mercurio10::where('numero', $id)->where('tipopc', $this->tipopc)->delete();
                }
                Mercurio32::where('id', $id)->where('documento', $documento)->delete();
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

    public function paramsAction()
    {
        $this->setResponse("ajax");
        try {
            $nombre = parent::getActUser("nombre");
            $documento = parent::getActUser("documento");
            $tipo = parent::getActUser("tipo");
            $coddoc = parent::getActUser("coddoc");
            $listAfiliados = false;
            $cedtras = array();

            $trabajadorService = new TrabajadorService();
            if ($tipo == 'E') {
                $cedtras[] = $trabajadorService->findRequestByDocumentoCoddoc($documento, $coddoc);
                $list = $trabajadorService->findApiTrabajadoresByNit($documento);

                $listAfiliados = array();
                foreach ($list as $row) {
                    $listAfiliados[] = array('cedula' => $row['cedtra'], 'nombre_completo' => $row['nombre']);
                }
            } else {
                $cedtras[] = array('cedula' => $documento,  'nombre_completo' => $nombre);
            }

            $mtipoDocumentos = new Gener18();
            $tipoDocumentos = array();

            foreach ($mtipoDocumentos->all() as $mtipo) {
                if ($mtipo->getCoddoc() == '7' || $mtipo->getCoddoc() == '2') continue;
                $tipoDocumentos["{$mtipo->getCoddoc()}"] = $mtipo->getDetdoc();
            }

            $coddoc = array();
            foreach ($mtipoDocumentos->all() as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') continue;
                $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $codciu = array();
            $mgener09 = new Gener09();
            foreach ($mgener09->getFind("conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_conyuges"
                )
            );

            $tipsal = (new Mercurio32)->getTipsalArray();
            $tipsal["@"] = "NINGUNO";

            $paramsConyuge = new ParamsConyuge();
            $paramsConyuge->setDatosCaptura($procesadorComando->toArray());

            $coddoc = $tipoDocumentos;
            $data = array(
                'tipdoc' => $coddoc,
                'coddoc' => $coddoc,
                'codzon' => $codciu,
                'codciu' => ParamsConyuge::getCiudades(),
                'sexo'   => ParamsConyuge::getSexos(),
                'estciv' => ParamsConyuge::getEstadoCivil(),
                'captra' => ParamsConyuge::getCapacidadTrabajar(),
                'nivedu' => ParamsConyuge::getNivelEducativo(),
                'tipviv' => ParamsConyuge::getVivienda(),
                'cargo'  => ParamsConyuge::getOcupaciones(),
                'ciunac' => ParamsConyuge::getCiudades(),
                'tippag' => ParamsConyuge::getTipoPago(),
                'codban' => ParamsConyuge::getBancos(),
                'tipcue' => ParamsConyuge::getTipoCuenta(),
                "autoriza" => array("S" => "SI", "N" => "NO"),
                "comper" => ParamsConyuge::getCompaneroPermanente(),
                "codocu" => ParamsConyuge::getOcupaciones(),
                "tipsal" => $tipsal,
                "ciures" => $codciu,
                "trabajadores" => $cedtras,
                'list_afiliados' => $listAfiliados,
                "tipdis" => ParamsConyuge::getTipoDiscapacidad(),
                'peretn' => ParamsConyuge::getPertenenciaEtnicas(),
                'resguardo_id' => ParamsConyuge::getResguardos(),
                'pub_indigena_id' => ParamsConyuge::getPueblosIndigenas()
            );

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

    public function renderTableAction(Request $request, Response $response, string $estado = '')
    {
        $this->setResponse("view");
        $conyugeService = new ConyugeService();
        $html = View(
            "mercurio/conyuge/tmp/solicitudes",
            array(
                "path" => base_path(),
                "conyuges" => $conyugeService->findAllByEstado($estado)
            )
        )->render();
        return $this->renderText($html);
    }

    public function validaAction(Request $request, Response $response)
    {
        $this->setResponse("ajax");
        try {
            $documento = parent::getActUser("documento");
            $coddoc = parent::getActUser("coddoc");
            $cedcon = $request->input('cedcon');

            $solicitud_previa = (new Mercurio32)->findFirst("cedcon='{$cedcon}' and documento='{$documento}' and coddoc='{$coddoc}'");
            $conyuge = false;

            if ($solicitud_previa) {
                $conyuge = $solicitud_previa->getArray();
            }

            if (!$conyuge) {
                $procesadorComando = Comman::Api();
                $procesadorComando->runCli(
                    array(
                        "servicio" => "ComfacaEmpresas",
                        "metodo" => "informacion_conyuge",
                        "params" => array(
                            "cedcon" => $cedcon

                        )
                    )
                );
                $salida = $procesadorComando->toArray();
                if ($salida['success']) {
                    if ($salida['data']) $conyuge = $salida['data'];
                }
            }

            $response = array(
                "success" => true,
                "solicitud_previa" => ($solicitud_previa > 0) ? true : false,
                "conyuge" => $conyuge
            );
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($response);
    }

    public function searchRequestAction(Request $request, Response $response, string $id)
    {
        $this->setResponse("ajax");
        try {
            if (is_null($id)) {
                throw new DebugException("Error no hay solicitud a buscar", 301);
            }
            $documento = parent::getActUser("documento");
            $coddoc = parent::getActUser("coddoc");

            $solicitud = Mercurio32::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->first();

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

    public function consultaDocumentosAction($id)
    {
        $this->setResponse("ajax");
        try {
            $documento = parent::getActUser("documento");
            $coddoc = parent::getActUser("coddoc");
            $conService = new ConyugeService();

            $sindepe = Mercurio32::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->whereNotIn('estado', ['I', 'X'])
                ->first();

            if ($sindepe == false) {
                throw new DebugException("Error no se puede identificar el propietario de la solicitud", 301);
            }
            $salida = array(
                'success' => true,
                'data' => $conService->dataArchivosRequeridos($sindepe),
                'msj' => 'OK'
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function formularioAction($id)
    {
        $this->setResponse("ajax");
        try {
            $documento = parent::getActUser('documento');
            $coddoc = parent::getActUser('coddoc');

            $mercurio32 = Mercurio32::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->first();
            if (!$mercurio32) {
                throw new DebugException("Error no se puede generar el fomulario a la solicitud no es valida", 301);
            }

            $mercurio31 = Mercurio31::where('cedtra', $mercurio32->getCedtra())
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->first();

            if (!$mercurio31) {
                throw new DebugException("Error no se puede generar el fomulario a la solicitud no es valida", 301);
            }

            $trabajadorFormulario = new TrabajadorFormulario(
                array(
                    'documento' => $documento,
                    'coddoc' => $coddoc
                )
            );

            $timer = strtotime('now');
            $file = "formulario_afiliacion_{$mercurio32->getCedtra()}_{$timer}.pdf";
            $formularios = new Formularios();

            $formularios->trabajadorAfiliacion(
                $trabajadorFormulario->main($mercurio31),
                $file
            );

            $response = array(
                "success" => true,
                "name" => $file,
                "url" => 'conyuge/download_reporte/' . $file
            );
        } catch (DebugException $e) {
            $response = array(
                'success' => false,
                'msj' => $e->getMessage()
            );
        }
        return $this->renderObject($response, false);
    }

    public function seguimientoAction($id)
    {
        $this->setResponse("ajax");
        try {
            $conyugeService = new ConyugeService();
            $out = $conyugeService->consultaSeguimiento($id);
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
