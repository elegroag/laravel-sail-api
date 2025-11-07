<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\AuthException;
use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsPensionado;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio37;
use App\Models\Mercurio38;
use App\Models\Subsi54;
use App\Services\Entidades\PensionadoService;
use App\Services\Entidades\TrabajadorService;
use App\Services\FormulariosAdjuntos\PensionadoAdjuntoService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\ChangeCuentaService;
use App\Services\Utils\Comman;
use App\Services\Utils\GeneralService;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Api\ApiSubsidio;

class PensionadoController extends ApplicationController
{
    /**
     * pensionadoService variable
     *
     * @var PensionadoService
     */
    protected $pensionadoService;

    /**
     * trabajadorService variable
     *
     * @var TrabajadorService
     */
    protected $trabajadorService;

    /**
     * asignarFuncionario variable
     *
     * @var AsignarFuncionario
     */
    protected $asignarFuncionario;

    protected $tipopc = '9';

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
     * indexfunction
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('mercurio.pensionado.index', [
            'title' => 'Afiliación Pensionados',
            'calemp' => 'P',
            'tipper' => 'N',
            'cedtra' => parent::getActUser('documento'),
            'coddoc' => parent::getActUser('coddoc'),
        ]);
    }

    /**
     * actualizar function
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizar(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $id = $request->input('id');
            $params = $this->serializeData($request);
            $params['tipo'] = parent::getActUser('tipo');
            $params['coddoc'] = parent::getActUser('coddoc');
            $params['documento'] = parent::getActUser('documento');
            $params['estado'] = 'T';

            $this->pensionadoService = new PensionadoService;
            $this->asignarFuncionario = new AsignarFuncionario;
            $params['usuario'] = $this->asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            $this->pensionadoService->updateByFormData($id, $params);
            $pensionado = $this->pensionadoService->findById($id);
            $data = $pensionado->getArray();

            $response = [
                'success' => true,
                'msj' => 'Registro actualizado con éxito',
                'data' => $data,
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    /**
     * guardar function
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardar(Request $request)
    {
        // $this->setResponse("ajax");
        $pensionadoService = new PensionadoService;
        $this->db->begin();
        try {
            $asignarFuncionario = new AsignarFuncionario;
            $id = $request->input('id');
            $clave_certificado = $request->input('clave');
            $params = $this->serializeData($request);
            $params['tipo'] = $this->tipo;
            $params['coddoc'] = $this->user['coddoc'];
            $params['documento'] = $this->user['documento'];

            $params['usuario'] = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);

            if (is_null($id) || $id == '') {
                $pensionado = $pensionadoService->createByFormData($params);
            } else {
                $res = $pensionadoService->updateByFormData($id, $params);
                if ($res == false) {
                    throw new DebugException('Error no se actualizaron los datos', 301);
                }
                $pensionado = $pensionadoService->findById($id);
            }

            // Buscar los parámetros por API
            $pensionadoService->paramsApi();

            $pensionadoAdjuntoService = new PensionadoAdjuntoService($pensionado);
            $pensionadoAdjuntoService->setClaveCertificado($clave_certificado);

            // Procesar formulario
            $out = $pensionadoAdjuntoService->formulario()->getResult();
            (new GuardarArchivoService([
                'tipopc' => $this->tipopc,
                'coddoc' => 1,
                'id' => $pensionado->getId(),
            ]))->salvarDatos($out);

            // Procesar tratamiento de datos
            $out = $pensionadoAdjuntoService->tratamientoDatos()->getResult();
            (new GuardarArchivoService([
                'tipopc' => $this->tipopc,
                'coddoc' => 25,
                'id' => $pensionado->getId(),
            ]))->salvarDatos($out);

            // Procesar carta de solicitud
            $out = $pensionadoAdjuntoService->cartaSolicitud()->getResult();
            (new GuardarArchivoService([
                'tipopc' => $this->tipopc,
                'coddoc' => 24,
                'id' => $pensionado->getId(),
            ]))->salvarDatos($out);

            $response = [
                'success' => true,
                'msj' => 'Registro completado con éxito',
                'data' => $pensionado->getArray(),
            ];
            $this->db->commit();
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
            $this->db->rollBack();
        }

        return $this->renderObject($response);
    }

    /**
     * serializeData function
     *
     * @return array
     */
    protected function serializeData(Request $request)
    {
        $fecsol = Carbon::now();

        return [
            'fecsol' => $fecsol->format('Y-m-d'),
            'cedtra' => $request->input('cedtra', ''),
            'tipdoc' => $request->input('tipdoc', ''),
            'priape' => $request->input('priape', ''),
            'segape' => $request->input('segape', ''),
            'prinom' => $request->input('prinom', ''),
            'segnom' => $request->input('segnom', ''),
            'fecnac' => $request->input('fecnac', ''),
            'ciunac' => $request->input('ciunac', ''),
            'sexo' => $request->input('sexo', ''),
            'estciv' => $request->input('estciv', ''),
            'cabhog' => $request->input('cabhog', ''),
            'codciu' => $request->input('codciu', ''),
            'codzon' => $request->input('codzon', ''),
            'direccion' => $request->input('direccion', ''),
            'barrio' => $request->input('barrio', ''),
            'telefono' => $request->input('telefono', ''),
            'celular' => $request->input('celular', ''),
            'email' => $request->input('email', ''),
            'fecini' => $request->input('fecini', ''),
            'salario' => $request->input('salario', ''),
            'captra' => $request->input('captra', ''),
            'tipdis' => $request->input('tipdis', ''),
            'nivedu' => $request->input('nivedu', ''),
            'rural' => $request->input('rural', ''),
            'vivienda' => $request->input('vivienda', ''),
            'tipafi' => $request->input('tipafi', ''),
            'autoriza' => $request->input('autoriza', ''),
            'calemp' => 'P',
            'codact' => $request->input('codact', ''),
            'tippag' => $request->input('tippag', ''),
            'cargo' => $request->input('cargo', ''),
            'tipcue' => $request->input('tipcue', ''),
            'numcue' => $request->input('numcue', ''),
            'resguardo_id' => $request->input('resguardo_id', ''),
            'peretn' => $request->input('peretn', ''),
            'pub_indigena_id' => $request->input('pub_indigena_id', ''),
            'codban' => $request->input('codban', ''),
            'codcaj' => $request->input('codcaj', ''),
            'coddocrepleg' => $request->input('coddocrepleg', ''),
            'facvul' => $request->input('facvul', ''),
            'orisex' => $request->input('orisex', ''),
        ];
    }

    /**
     * valida function
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function valida(Request $request)
    {
        $this->setResponse('ajax');

        try {
            $cedtra = $request->input('cedrep');
            $solicitud = (new Mercurio38)->findFirst("documento='{$cedtra}' AND estado IN('A','I')");

            $solicitudPrevia = $solicitud ? $solicitud->getArray() : false;

            // Obtener información de la empresa
            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send([
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => ['nit' => $cedtra],
            ]);

            $empresa = $procesadorComando->toArray();
            $empresa = ! empty($empresa['data']) ? $empresa['data'] : false;

            // Obtener información del trabajador
            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send([
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => ['cedtra' => $cedtra],
            ]);

            $trabajador = $procesadorComando->toArray();
            $trabajador = ! empty($trabajador['data']) ? $trabajador['data'] : false;

            $response = [
                'success' => true,
                'solicitud_previa' => $solicitudPrevia,
                'empresa' => $empresa,
                'trabajador' => $trabajador,
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => 'No se pudo validar la información: ' . $e->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    /**
     * borrarArchivo function
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrarArchivo(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $numero = $this->clp($request, 'id');
            $coddoc = $this->clp($request, 'coddoc');
            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)->where('numero', $numero)->where('coddoc', $coddoc)->first();

            $filepath = storage_path('temp/' . $mercurio37->getArchivo());
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $numero)
                ->where('coddoc', $coddoc)
                ->delete();

            $response = [
                'success' => true,
                'msj' => 'El archivo se borro de forma correcta',
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    /**
     * guardarArchivo function
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarArchivo(Request $request)
    {
        $this->setResponse('ajax');

        try {
            $id = $request->input('id');
            $coddoc = $request->input('coddoc');

            $guardarArchivoService = new GuardarArchivoService([
                'tipopc' => $this->tipopc,
                'coddoc' => $coddoc,
                'id' => $id,
            ]);

            $mercurio37 = $guardarArchivoService->main();
            $mercurio37 = (new Mercurio37)->findFirst("tipopc='{$this->tipopc}' AND numero='{$id}' AND coddoc='{$coddoc}'");

            if (! $mercurio37) {
                throw new \Exception('No se pudo encontrar el archivo guardado');
            }

            $response = [
                'success' => true,
                'msj' => 'Archivo procesado correctamente',
                'data' => $mercurio37->getArray(),
            ];
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'msj' => 'Error al procesar el archivo: ' . $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    /**
     * enviarCaja function
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enviarCaja(Request $request)
    {
        $this->db->begin();
        try {
            $id = $request->input('id');

            $asignarFuncionario = new AsignarFuncionario;
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);
            if (! $usuario) {
                throw new \Exception('No se pudo obtener la información del usuario actual');
            }
            $pensionadoService = new PensionadoService;
            $pensionadoService->enviarCaja(new SenderValidationCaja, $id, $usuario);

            $this->db->commit();

            $response = [
                'success' => true,
                'msj' => 'El envío de la solicitud se ha completado con éxito',
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            $response = [
                'success' => false,
                'msj' => 'Error al enviar a caja: ' . $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    /**
     * Obtiene el usuario actual
     *
     * @return mixed
     *
     * @throws \Exception
     */
    /**
     * Obtiene el usuario actual
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getCurrentUser()
    {
        if (! class_exists('AsignarFuncionario')) {
            throw new \RuntimeException('La clase AsignarFuncionario no está disponible');
        }

        $asignarFuncionario = new AsignarFuncionario;

        return $asignarFuncionario->asignar(
            $this->tipopc ?? 'WEB',
            $this->getActUser('codciu') ?? ''
        );
    }

    public function reloadArchivos(Request $request)
    {
        $this->setResponse('ajax');
        $this->pensionadoService = new PensionadoService;
        try {
            $cedtra = $request->input('cedtra');
            $id = $request->input('id');

            $mercurio38 = (new Mercurio38)->findFirst("cedtra='{$cedtra}' and id='{$id}'");

            if (! $mercurio38) {
                throw new DebugException('La solicitud no está disponible actualizar el documento adjunto', 501);
            } else {

                $salida = [
                    'documentos_adjuntos' => $this->pensionadoService->archivosRequeridos($mercurio38),
                    'success' => true,
                ];
            }
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    /**
     * cancelarSolicitud function
     *
     * @return void
     */
    public function cancelarSolicitud(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $user = $this->user;
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $id = $request->input('id');

            $m41 = (new Mercurio38)->findFirst("id='{$id}' AND documento='{$documento}' and coddoc='{$coddoc}'");
            if ($m41) {
                if ($m41->getEstado() != 'T') {
                    (new Mercurio10)->deleteAll("numero='{$id}' AND tipopc='{$this->tipopc}'");
                }
                (new Mercurio38)->deleteAll("id='{$id}'");
            }
            $salida = [
                'success' => true,
                'msj' => 'El registro se borro con éxito del sistema.',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }
        $this->renderObject(json_encode($salida, JSON_NUMERIC_CHECK));
    }

    public function downloadFile($archivo = '')
    {
        $this->setResponse('view');
        $fichero = 'public/temp/' . $archivo;

        return $this->renderFile($fichero);
    }

    public function params()
    {
        $this->setResponse('ajax');

        try {
            $mtipoDocumentos = new Gener18;
            $tipoDocumentos = [];

            foreach ($mtipoDocumentos->all() as $mtipo) {
                if ($mtipo->getCoddoc() == '7' || $mtipo->getCoddoc() == '2' || $mtipo->getCoddoc() == '3') {
                    continue;
                }
                $tipoDocumentos["{$mtipo->getCoddoc()}"] = $mtipo->getDetdoc();
            }

            $msubsi54 = new Subsi54;
            $tipsoc = [];
            foreach ($msubsi54->all() as $entity) {
                $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
            }

            $coddoc = [];
            foreach ($mtipoDocumentos->all() as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') {
                    continue;
                }
                $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $coddocrepleg = [];
            foreach ($mtipoDocumentos->all() as $entity) {
                if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') {
                    continue;
                }
                $coddocrepleg["{$entity->getCodrua()}"] = $entity->getDetdoc();
            }

            $codciu = [];
            $mgener09 = new Gener09;
            foreach ($mgener09->getFind("conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $pensionadoService = new PensionadoService;
            $pensionadoService->paramsApi();

            $mtipafi = ParamsTrabajador::getTipoAfiliado();
            $tipo_afiliados = [];
            foreach ($mtipafi as $key => $tipo) {
                if ($key == '10' || $key == '64' || $key == '66' || $key == '67') {
                    $tipo_afiliados[$key] = $tipo;
                }
            }

            $coddoc = $tipoDocumentos;
            $data = [
                'tipdoc' => $coddoc,
                'tipper' => (new Mercurio30)->getTipperArray(),
                'tipsoc' => $tipsoc,
                'calemp' => (new Mercurio30)->getCalempArray(),
                'codciu' => $codciu,
                'coddocrepleg' => $coddocrepleg,
                'codzon' => ParamsPensionado::getZonas(),
                'codact' => ParamsPensionado::getActividades(),
                'tipemp' => ParamsPensionado::getTipoEmpresa(),
                'codcaj' => ParamsPensionado::getCodigoCajas(),
                'ciupri' => ParamsPensionado::getCiudades(),
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
                'tipafi' => $tipo_afiliados,
                'cargo' => ParamsTrabajador::getOcupaciones(),
                'orisex' => ParamsTrabajador::getOrientacionSexual(),
                'facvul' => ParamsTrabajador::getVulnerabilidades(),
                'peretn' => ParamsTrabajador::getPertenenciaEtnicas(),
                'ciunac' => ParamsPensionado::getCiudades(),
                'labora_otra_empresa' => ParamsTrabajador::getLaboraOtraEmpresa(),
                'tippag' => ParamsTrabajador::getTipoPago(),
                'resguardo_id' => ParamsTrabajador::getResguardos(),
                'pub_indigena_id' => ParamsTrabajador::getPueblosIndigenas(),
                'codban' => ParamsTrabajador::getBancos(),
                'tipsal' => (new Mercurio31)->getTipsalArray(),
                'tipcue' => ParamsTrabajador::getTipoCuenta(),
                'ruralt' => ParamsTrabajador::getRural(),
                'autoriza' => ['S' => 'SI', 'N' => 'NO'],
            ];

            $salida = [
                'success' => true,
                'data' => $data,
                'msj' => 'OK',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    public function searchRequest($id)
    {
        $this->setResponse('ajax');
        try {
            if (is_null($id)) {
                throw new DebugException('Error no hay solicitud a buscar', 301);
            }
            $documento = parent::getActUser('documento');
            $coddoc = parent::getActUser('coddoc');

            $solicitud = (new Mercurio38)->findFirst(" id='{$id}' AND documento='{$documento}' AND coddoc='{$coddoc}'");
            if ($solicitud == false) {
                throw new DebugException('Error la solicitud no está disponible para acceder.', 301);
            } else {
                $data = $solicitud->getArray();
            }
            $salida = [
                'success' => true,
                'data' => $data,
                'msj' => 'OK',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    public function consultaDocumentos($id)
    {
        $this->setResponse('ajax');
        try {
            $documento = parent::getActUser('documento');
            $coddoc = parent::getActUser('coddoc');
            $pensionadoService = new PensionadoService;

            $sindepe = (new Mercurio38)->findFirst("id='{$id}' AND documento='{$documento}' AND coddoc='{$coddoc}' AND estado NOT IN('I','X')");
            if ($sindepe == false) {
                throw new DebugException('Error no se puede identificar el propietario de la solicitud', 301);
            }
            $salida = [
                'success' => true,
                'data' => $pensionadoService->dataArchivosRequeridos($sindepe),
                'msj' => 'OK',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    public function borrar(Request $request)
    {
        $this->setResponse('ajax');
        $generales = new GeneralService;
        $generales->startTrans('mercurio41');
        try {

            $documento = parent::getActUser('documento');
            $coddoc = parent::getActUser('coddoc');

            $id = $request->input('id');
            $solicitud = (new Mercurio38)->findFirst("id='{$id}' and documento='{$documento}' and coddoc='{$coddoc}'");
            if ($solicitud) {
                if ($solicitud->getEstado() != 'T') {
                    (new Mercurio10)->deleteAll("numero='{$id}' AND tipopc='{$this->tipopc}'");
                }
            }
            (new Mercurio38)->deleteAll("id='{$id}' and documento='{$documento}' and coddoc='{$coddoc}'");
            $generales->finishTrans();
            $response = [
                'success' => true,
                'msj' => 'Ok',
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function renderTable(Request $request, Response $response, string $estado = '')
    {
        $this->setResponse('view');
        $pensionadoService = new PensionadoService;
        $html = view(
            'mercurio/pensionado/tmp/solicitudes',
            [
                'path' => base_path(),
                'pensionados' => $pensionadoService->findAllByEstado($estado),
            ]
        )->render();

        return $this->renderText($html);
    }

    public function seguimiento(Request $request)
    {
        try {
            $pensionadoService = new PensionadoService;
            $out = $pensionadoService->consultaSeguimiento($request->input('id'));
            $salida = [
                'success' => true,
                'data' => $out,
            ];
        } catch (DebugException $e) {
            $salida = ['success' => false, 'msj' => $e->getMessage()];
        }
        return response()->json($salida);
    }

    public function descargar_formulario($id)
    {
        $this->setResponse('ajax');

        try {
            if (! is_numeric($id)) {
                throw new \InvalidArgumentException('ID de solicitud no válido');
            }

            $pensionadoService = new PensionadoService;
            // $formulario = $pensionadoService->descargarFormulario($id);

            if (empty($formulario)) {
                throw new \RuntimeException('No se pudo generar el formulario');
            }

            $response = [
                'success' => true,
                'formulario' => $formulario,
            ];
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'msj' => 'Error al descargar el formulario: ' . $e->getMessage(),
            ];
        }

        return $this->renderObject($response);
    }

    /**
     * Administra la cuenta de un pensionado
     *
     * @param  string  $id  ID de la solicitud
     * @return mixed
     *
     * @throws \Exception
     */
    public function administrar_cuenta($id = '')
    {
        $this->setResponse('view');

        try {
            if (empty($id)) {
                throw new \InvalidArgumentException('El ID de la solicitud es requerido');
            }

            // Obtener la solicitud
            $solicitud = (new Mercurio38)->findFirst("id='{$id}' and estado='A'");

            if (! $solicitud) {
                throw new \RuntimeException('No se encontró la solicitud solicitada');
            }

            // Preparar datos del usuario
            $userData = [
                'id' => $id,
                'cedtra' => $solicitud->cedtra,
                'tipopc' => $this->tipopc,
                'codciu' => $this->getActUser('codciu'),
                'codusu' => $this->getActUser('codusu'),
                'codpai' => $this->getActUser('codpai'),
                'codemp' => $this->getActUser('codemp'),
                'codofi' => $this->getActUser('codofi'),
                'codrol' => $this->getActUser('codrol'),
            ];

            $request = new Request($userData);
            $change = new ChangeCuentaService;

            if ($change->initializa($request)) {
                set_flashdata('success', [
                    'msj' => 'La administración de la cuenta se ha inicializado con éxito.',
                    'code' => 200,
                ]);
                redirect('principal/index');

                return;
            }

            throw new \RuntimeException('No se pudo inicializar la administración de la cuenta');
        } catch (AuthException $e) {
            set_flashdata('error', [
                'msj' => $e->getMessage(),
                'code' => $e->getCode() ?: 505,
            ]);
            redirect('empresa/index');
        } catch (\Exception $e) {
            set_flashdata('error', [
                'msj' => 'Error al procesar la solicitud: ' . $e->getMessage(),
                'code' => 500,
            ]);
            redirect('principal/index');
        }
    }
}
