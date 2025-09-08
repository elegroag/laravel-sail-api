<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio37;
use App\Models\Subsi54;
use App\Services\Entidades\TrabajadorService;
use App\Services\FormulariosAdjuntos\TrabajadorAdjuntoService;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\SenderValidationCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrabajadorController extends ApplicationController
{

    protected $db;
    protected $user;
    protected $tipo;
    protected $tipopc = '1';

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    /**
     * GET /trabajador/index (Opcional, placeholder)
     */
    public function indexAction()
    {

        return view('mercurio/trabajador/index', [
            'tipo' => $this->tipo,
            'documento' => $this->documento,
            'title' => 'Afiliación de trabajadores',
            'nit' => '',
            'razsoc' => '',
        ]);
    }

    /**
     * POST /trabajador/valide_nit
     * Valida existencia y estado de empresa vía API Comman
     */
    public function valideNitAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $nit = $this->clp($request, 'nit');
            if (!$nit) throw new DebugException('El nit es requerido', 422);

            $ps = Comman::Api();
            $ps->runCli([
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => ['nit' => $nit]
            ]);
            $datos = $ps->toArray();

            if (!isset($datos['data']) || count($datos['data']) == 0) {
                $response = ['success' => false, 'msj' => 'El nit no existe'];
            } elseif (($datos['data']['estado'] ?? null) === 'I') {
                $response = ['success' => false, 'msj' => 'La empresa esta inactiva no puede crear nuevos trabajadores'];
            } else {
                $response = ['success' => true, 'msj' => '', 'data' => $datos['data']['razsoc'] ?? null];
            }
            return $this->renderObject($response);
        } catch (DebugException $e) {
            return $this->renderObject([
                'success' => false,
                'msj' => $e->getMessage()
            ]);
        }
    }

    /**
     * POST /trabajador/borrar_archivo
     */
    public function borrarArchivoAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $numero = $this->clp($request, 'id');
            $coddoc = $this->clp($request, 'coddoc');

            $mercurio01 = (new Mercurio01())->findFirst();
            $mercurio37 = (new Mercurio37())->findFirst("tipopc='{$this->tipopc}' and numero='{$numero}' and coddoc='{$coddoc}'");

            if ($mercurio01 && $mercurio37) {
                $filepath = base_path('') . '/' . ltrim($mercurio01->getPath() . $mercurio37->getArchivo(), '/');
                if (file_exists($filepath)) {
                    @unlink($filepath);
                }
                (new Mercurio37())->deleteAll("tipopc='{$this->tipopc}' and numero='{$numero}' and coddoc='{$coddoc}'");
            }

            $response = ['success' => true, 'msj' => 'El archivo se borro de forma correcta'];
        } catch (\Exception $e) {
            $response = ['success' => false, 'msj' => $e->getMessage()];
        }
        return $this->renderObject($response);
    }

    /**
     * POST /trabajador/guardar_archivo
     */
    public function guardarArchivoAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = $this->clp($request, 'id');
            $coddoc = $this->clp($request, 'coddoc');

            $guardarArchivoService = new GuardarArchivoService([
                'tipopc' => $this->tipopc,
                'coddoc' => $coddoc,
                'id' => $id,
            ]);
            $mercurio37 = $guardarArchivoService->main();

            $response = [
                'success' => true,
                'msj' => 'Ok archivo procesado',
                'data' => method_exists($mercurio37, 'getArray') ? $mercurio37->getArray() : null,
            ];
        } catch (\Exception $e) {
            $response = ['success' => false, 'msj' => $e->getMessage()];
        }
        return $this->renderObject($response);
    }

    /**
     * POST /trabajador/traer_trabajador
     */
    public function traerTrabajadorAction(Request $request)
    {
        $this->setResponse('ajax');
        $cedtra = $this->clp($request, 'cedtra');
        $nit = $this->clp($request, 'nit');

        $datos_trabajador = [];

        $ps = Comman::Api();
        $ps->runCli([
            'servicio' => 'ComfacaEmpresas',
            'metodo' => 'informacion_trabajador',
            'params' => [
                'cedtra' => $cedtra
            ]
        ]);

        $out = $ps->toArray();
        if (($out['success'] ?? false) && isset($out['data'])) {
            $datos_trabajador = $out['data'];
        }

        $mercurio31 = new Mercurio31($datos_trabajador);
        $mercurio31->setLog('0');

        $response = [];
        $response['multi'] = false;
        if ($mercurio31->getNit() != $nit) $response['multi'] = true;

        $response['flag'] = true;
        if ($mercurio31->getNit() == $nit && $mercurio31->getEstado() == 'A') {
            $response['flag'] = false;
            $response['msg'] = 'El afiliado ya se encuentra registrado o Activo con la misma empresa.';
        }

        if ($mercurio31->getCedtra() == '') {
            $mercurio31 = Mercurio31::where('cedtra', $cedtra)->first() ?: new Mercurio31();
        }

        $response['data'] = $mercurio31->toArray();
        return $this->renderObject($response);
    }

    /**
     * POST /trabajador/enviar_caja
     */
    public function enviarCajaAction(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = $this->clp($request, 'id');

            $trabajadorService = new TrabajadorService();


            $asignarFuncionario = new AsignarFuncionario();
            // Nota: getActUser reemplazado por datos del request o autent. Ajustar si hay SessionCookies
            $codciu = $this->clp($request, 'codciu');
            $usuario = $asignarFuncionario->asignar($this->tipopc, $codciu);

            $trabajadorService->enviarCaja(new SenderValidationCaja(), $id, $usuario); // TODO: importar/clase correcta si existe


            $salida = ['success' => true, 'msj' => 'El envio de la solicitud se ha completado con éxito'];
        } catch (\Exception $e) {
            $salida = ['success' => false, 'msj' => $e->getMessage()];
        }
        return $this->renderObject($salida);
    }

    /**
     * GET /trabajador/seguimiento/{id}
     */
    public function seguimientoAction($id)
    {
        $this->setResponse('ajax');
        try {
            $trabajadorService = new TrabajadorService();
            $out = $trabajadorService->consultaSeguimiento($id);
            $salida = ['success' => true, 'data' => $out];
        } catch (\Exception $e) {
            $salida = ['success' => false, 'msj' => $e->getMessage()];
        }
        return $this->renderObject($salida);
    }

    public function paramsAction()
    {
        $this->setResponse("ajax");

        try {
            $nit = $this->user['documento'];

            $mtipoDocumentos = new Gener18();
            $tipoDocumentos = array();

            foreach ($mtipoDocumentos->all() as $mtipo) {
                if ($mtipo->getCoddoc() == '7' || $mtipo->getCoddoc() == '2') continue;
                $tipoDocumentos["{$mtipo->getCoddoc()}"] = $mtipo->getDetdoc();
            }

            $msubsi54 = new Subsi54();
            $tipsoc = array();
            foreach ($msubsi54->all() as $entity) {
                $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
            }

            $coddoc = array();
            foreach ($mtipoDocumentos->all() as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') continue;
                $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $coddocrepleg = array();
            foreach ($mtipoDocumentos->all() as $entity) {
                if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') continue;
                $coddocrepleg["{$entity->getCodrua()}"] = $entity->getDetdoc();
            }

            $codciu = array();
            $mzonas = Gener09::where("codzon",  '>=', 18000)->where("codzon", '<=', 19000)->get();
            foreach ($mzonas as $entity) {
                $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaAfilia",
                    "metodo" => "parametros_trabajadores"
                )
            );
            $paramsTrabajador = new ParamsTrabajador();
            $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "buscar_sucursales_en_empresa/{$nit}",
                    "params" => [
                        'nit' => $nit
                    ]
                )
            );
            $rqs = $procesadorComando->toArray();

            $codsuc = array();
            $sucursales = $rqs['data'];
            if ($sucursales) {
                foreach ($sucursales as $data) {
                    if ($data['estado'] == 'I') continue;
                    if (isset($codciu[$data['codzon']])) {
                        $codsuc["{$data['codsuc']}"] = $data['detalle'] . ' - DE ' . $codciu[$data['codzon']];
                    } else {
                        $codsuc["{$data['codsuc']}"] = $data['detalle'];
                    }
                }
            }

            $tipafi = (new Mercurio07())->getArrayTipos();
            $coddoc = $tipoDocumentos;

            $salida = array(
                "success" => true,
                "msj" => 'OK',
                "data" => array(
                    'tipo'   => $tipafi,
                    'tipdoc' => $coddoc,
                    'tipper' => (new Mercurio30)->getTipperArray(),
                    'tipsoc' => $tipsoc,
                    'calemp' => (new Mercurio30)->getCalempArray(),
                    'codciu' => $codciu,
                    'codzon' => $codciu,
                    'coddocrepleg' => $coddocrepleg,
                    'sexo' => ParamsTrabajador::getSexos(),
                    'estciv' => ParamsTrabajador::getEstadoCivil(),
                    'cabhog' => ParamsTrabajador::getCabezaHogar(),
                    'captra' => ParamsTrabajador::getCapacidadTrabajar(),
                    'tipdis' => ParamsTrabajador::getTipoDiscapacidad(),
                    'nivedu' => ParamsTrabajador::getNivelEducativo(),
                    'rural' => ParamsTrabajador::getRural(),
                    'tipcon' => ParamsTrabajador::getTipoContrato(),
                    'trasin' => ParamsTrabajador::getSindicalizado(),
                    'vivienda' => ParamsTrabajador::getVivienda(),
                    'tipafi' => ParamsTrabajador::getTipoAfiliado(),
                    'cargo' => ParamsTrabajador::getOcupaciones(),
                    'orisex' => ParamsTrabajador::getOrientacionSexual(),
                    'facvul' => ParamsTrabajador::getVulnerabilidades(),
                    'peretn' => ParamsTrabajador::getPertenenciaEtnicas(),
                    'ciunac' => ParamsTrabajador::getCiudades(),
                    'tippag' => ParamsTrabajador::getTipoPago(),
                    'resguardo_id' => ParamsTrabajador::getResguardos(),
                    'pub_indigena_id' => ParamsTrabajador::getPueblosIndigenas(),
                    'codban' => ParamsTrabajador::getBancos(),
                    'tipsal' => (new Mercurio31)->getTipsalArray(),
                    'tipcue' => ParamsTrabajador::getTipoCuenta(),
                    'ruralt' => ParamsTrabajador::getRural(),
                    'tipjor' => array("C" => "COMPLETA", "M" => "MEDIA", "P" => "PARCIAL"),
                    "autoriza" => array("S" => "SI", "N" => "NO"),
                    "comision" => array("S" => "SI", "N" => "NO"),
                    'labora_otra_empresa' => array("S" => "SI", "N" => "NO"),
                    'codsuc' => $codsuc
                )
            );
        } catch (DebugException $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    /**
     * renderTableAction function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @param string $estado
     * @return string
     */
    public function renderTableAction($estado = '')
    {
        $this->setResponse("view");
        $trabajadorService = new TrabajadorService();
        $html = view(
            "mercurio/trabajador/tmp/solicitudes",
            array(
                "path" => base_path(),
                "trabajadores" => $trabajadorService->findAllByEstado($estado)
            )
        )->render();
        return $this->renderText($html);
    }

    public function searchRequestAction($id)
    {
        $this->setResponse("ajax");
        try {
            if (is_null($id)) {
                throw new DebugException("Error no hay solicitud a buscar", 301);
            }
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $solicitud = Mercurio31::where("id", $id)
                ->where("documento", $documento)
                ->where("coddoc", $coddoc)
                ->first();

            if ($solicitud == False) {
                throw new DebugException("Error la solicitud no está disponible para acceder.", 301);
            } else {
                $data = $solicitud->toArray();
            }

            $salida = [
                "success" => true,
                "data" => $data,
                "msj" => 'OK'
            ];
        } catch (DebugException $err) {
            $salida = [
                "success" => false,
                "msj" => $err->getMessage()
            ];
        }
        return $this->renderObject($salida);
    }

    /**
     * guardarAction function
     * @changed [2024-03-10]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function guardarAction(Request $request)
    {
        $this->setResponse("ajax");
        $trabajadorService = new TrabajadorService();

        try {
            $id = $request->get('id');
            $params = $this->serializeData($request);
            $params['tipo'] = $this->tipo;
            $params['coddoc'] = $this->user['coddoc'];
            $params['documento'] = $this->user['documento'];

            if (is_null($id) || $id == "") {
                $solicitud = $trabajadorService->createByFormData($params);
            } else {
                $res = $trabajadorService->updateByFormData($id, $params);
                if ($res == false) throw new DebugException("Error no se actualizo los datos", 301);
                $solicitud = $trabajadorService->findById($id);
            }

            $trabajadorService->paramsApi();
            $trabajadorAdjuntoService = new TrabajadorAdjuntoService($solicitud);

            $out = $trabajadorAdjuntoService->formulario()->getResult();
            $coddoc_adjunto = 1;
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => $coddoc_adjunto,
                    'id' => $solicitud->getId()
                )
            ))->salvarDatos($out);

            $out = $trabajadorAdjuntoService->tratamientoDatos()->getResult();
            $coddoc_adjunto = 25;
            (new GuardarArchivoService(
                array(
                    'tipopc' => $this->tipopc,
                    'coddoc' => $coddoc_adjunto,
                    'id' => $solicitud->getId()
                )
            ))->salvarDatos($out);

            ob_end_clean();

            $response = [
                "msj" => "Proceso se ha completado con éxito",
                "success" => true,
                "data" => $solicitud->toArray()
            ];
        } catch (DebugException $err) {
            $response = [
                'success' => false,
                'msj' => $err->getMessage()
            ];
        }
        return $this->renderObject($response);
    }

    function serializeData(Request $request)
    {
        $asignarFuncionario = new AsignarFuncionario();
        $fecsol = Carbon::now();

        return array(
            'fecsol' => $fecsol->format('Y-m-d'),
            'nit' => $request->get('nit'),
            'razsoc' => $request->get('razsoc'),
            'cedtra' => $request->get('cedtra'),
            'tipdoc' => $request->get('tipdoc'),
            'priape' => $request->get('priape'),
            'segape' => $request->get('segape'),
            'prinom' => $request->get('prinom'),
            'segnom' => $request->get('segnom'),
            'fecnac' => $request->get('fecnac', "addslaches", "extraspaces", "striptags"),
            'ciunac' => $request->get('ciunac'),
            'sexo' => $request->get('sexo'),
            'estciv' => $request->get('estciv'),
            'cabhog' => $request->get('cabhog'),
            'codciu' => $request->get('codciu'),
            'codzon' => $request->get('codzon'),
            'direccion' => $request->get('direccion'),
            'barrio' => $request->get('barrio'),
            'telefono' => $request->get('telefono'),
            'celular' => $request->get('celular'),
            'fax' => $request->get('fax'),
            'email' => $request->get('email'),
            'fecing' => $request->get('fecing'),
            'salario' => $request->get('salario'),
            'tipsal' => $request->get('tipsal'),
            'captra' => $request->get('captra'),
            'tipdis' => $request->get('tipdis'),
            'nivedu' => $request->get('nivedu'),
            'rural' => $request->get('rural'),
            'horas' => $request->get('horas'),
            'tipcon' => $request->get('tipcon'),
            'trasin' => $request->get('trasin'),
            'vivienda' => $request->get('vivienda'),
            'tipafi' => $request->get('tipafi'),
            'profesion' => $request->get('profesion'),
            'cargo' => $request->get('cargo'),
            'orisex' => $request->get('orisex'),
            'facvul' => $request->get('facvul'),
            'peretn' => $request->get('peretn'),
            'dirlab' => $request->get('dirlab'),
            'autoriza' => $request->get('autoriza'),
            'tipjor' => $request->get('tipjor'),
            'ruralt' => $request->get('ruralt'),
            'comision' => $request->get('comision'),
            'codsuc' => $request->get('codsuc'),
            'otra_empresa' => $request->get('otra_empresa'),
            'numcue' => $request->get('numcue'),
            'codban' => $request->get('codban'),
            'tipcue' => $request->get('tipcue'),
            'tippag' => $request->get('tippag'),
            'log' =>  '0',
            'usuario' => $asignarFuncionario->asignar($this->tipopc, $request->get('codzon'))
        );
    }

    public function consultaDocumentosAction($id)
    {
        $this->setResponse("ajax");
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $traService = new TrabajadorService();
            $mtrabajador = Mercurio31::where("id", $id)
                ->where("documento", $documento)
                ->where("coddoc", $coddoc)
                ->whereNotIn("estado", ['I', 'X'])->first();

            if ($mtrabajador == false) {
                throw new DebugException("Error no se puede identificar el propietario de la solicitud", 301);
            }

            $salida = [
                'success' => true,
                'data' => $traService->dataArchivosRequeridos($mtrabajador),
                'msj' => 'OK'
            ];
        } catch (DebugException $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }

        return $this->renderObject($salida);
    }

    public function borrarAction(Request $request, Response $response, $id)
    {
        $this->setResponse("ajax");
        $generales = new GeneralService();
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $m31 = Mercurio31::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->first();
            if ($m31) {
                if ($m31->estado != 'T') Mercurio10::where('numero', $id)->where('tipopc', $this->tipopc)->delete();
            }

            Mercurio31::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->delete();

            $response = [
                'success' => true,
                'msj' => 'Ok'
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage()
            ];
        }
        return $this->renderObject($response);
    }

    public function validaAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $empresa = array();
            $nit = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $cedtra = $request->get('cedtra');

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                array(
                    "servicio" => "ComfacaEmpresas",
                    "metodo" => "informacion_empresa",
                    "params" => array(
                        'nit' => $nit
                    )
                )
            );

            $out = $procesadorComando->toArray();
            if ($out['success']) {
                $empresa = $out['data'];
            }

            $solicitud_previa = (new Mercurio31)->getCount(
                "*",
                "conditions: nit='{$nit}' and cedtra='{$cedtra}' and estado IN('T','P')"
            );

            $trabajador = false;
            $trabajadorService = new TrabajadorService();
            $out = $trabajadorService->buscarTrabajadorSubsidio($cedtra);
            if ($out) {
                if (count($out) > 0) {
                    $out['nit'] = $nit;
                    $out['tipdoc'] = $out['coddoc'];
                    $out['razsoc'] = $empresa['razsoc'];
                    $out['tipafi'] = $out['tipcot'];
                    $trabajador = $out;
                }
            }

            $response = [
                "success" => true,
                "solicitud_previa" => ($solicitud_previa > 0) ? true : false,
                "trabajador" => $trabajador
            ];
        } catch (DebugException $err) {
            $response = [
                "success" => false,
                "msj" => $err->getMessage()
            ];
        }
        return $this->renderObject($response);
    }
}
