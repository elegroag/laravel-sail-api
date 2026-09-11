<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Http\Controllers\Mercurio\Concerns\RendersSolicitudesGrid;
use App\Library\Collections\ParamsPensionado;
use App\Models\FormularioDinamico;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio07;
use App\Models\Mercurio37;
use App\Models\Mercurio38;
use App\Models\Subsi54;
use App\Services\Api\ApiSubsidio;
use App\Services\Entidades\PensionadoService;
use App\Services\Entidades\TrabajadorService;
use App\Services\FormulariosAdjuntos\PensionadoAdjuntoService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\ChangeCuentaService;
use App\Services\Utils\GeneralService;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PensionadoController extends ApplicationController
{
    use RendersSolicitudesGrid;

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


    protected ?array $user;

    protected ?string $tipo;

    public function __construct()
    {
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    /**
     * indexfunction
     *
     * @return View
     */
    public function index()
    {
        try {
            return view('mercurio.pensionado.index', [
                'title' => 'Afiliación Pensionados',
                'calemp' => 'P',
                'tipper' => 'N',
                'cedtra' => $this->user['documento'],
                'coddoc' => $this->user['coddoc'],
            ]);
        } catch (\Throwable $e) {
            $exception = $this->captureException($e, request());
            set_flashdata('error', [
                'msj' => $exception['msj'],
                'code' => $e->getCode(),
            ]);

            return redirect()->route('principal/index');
        }
    }

    /**
     * actualizar function
     */
    public function actualizar(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $params = $this->serializeData($request);
            $params['tipo'] = $this->tipo;
            $params['coddoc'] = $this->user['coddoc'];
            $params['documento'] = $this->user['documento'];
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
        } catch (Exception $e) {
            return $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * guardar function
     */
    public function guardar(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $pensionadoService = new PensionadoService;
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

            PensionadoAdjuntoService::generarAdjuntos(
                $pensionado,
                $this->tipopc,
                $clave_certificado
            );
            $salida = [
                'success' => true,
                'msj' => 'Registro completado con éxito',
                'data' => $pensionado->getArray(),
            ];
            DB::commit();
        } catch (Exception $e) {
            return $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    /**
     * serializeData function
     */
    protected function serializeData(Request $request): array
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
     */
    public function valida(Request $request): JsonResponse
    {
        try {
            $cedtra = $request->input('cedrep');
            $solicitud = Mercurio38::where('documento', $cedtra)->whereIn('estado', ['A', 'I'])->first();

            $solicitudPrevia = $solicitud ? $solicitud->getArray() : false;

            // Obtener información de la empresa
            $procesadorComando = new ApiSubsidio;
            $procesadorComando->send([
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => ['nit' => $cedtra],
            ]);

            $empresa = $procesadorComando->toArray();
            $empresa = ! empty($empresa['data']) ? $empresa['data'] : false;

            // Obtener información del trabajador
            $procesadorComando = new ApiSubsidio;
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
        } catch (Exception $e) {
            return $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * borrarArchivo function
     */
    public function borrarArchivo(Request $request): JsonResponse
    {
        try {
            $numero = $request->input('id');
            $coddoc = $request->input('coddoc');
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
        } catch (Exception $e) {
            return $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * guardarArchivo function
     */
    public function guardarArchivo(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $coddoc = $request->input('coddoc');

            $guardarArchivoService = new GuardarArchivoService([
                'tipopc' => $this->tipopc,
                'coddoc' => $coddoc,
                'id' => $id,
            ]);

            $mercurio37 = $guardarArchivoService->main();
            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)->where('numero', $id)->where('coddoc', $coddoc)->first();

            if (! $mercurio37) {
                throw new Exception('No se pudo encontrar el archivo guardado');
            }

            $response = [
                'success' => true,
                'msj' => 'Archivo procesado correctamente',
                'data' => $mercurio37->getArray(),
            ];
        } catch (Exception $e) {
            return $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * enviarCaja function
     */
    public function enviarCaja(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $id = $request->input('id');

            $asignarFuncionario = new AsignarFuncionario;
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);
            if (! $usuario) {
                throw new Exception('No se pudo obtener la información del usuario actual');
            }
            $pensionadoService = new PensionadoService;
            $pensionadoService->enviarCaja(new SenderValidationCaja, $id, $usuario);

            DB::commit();

            $response = [
                'success' => true,
                'msj' => 'El envío de la solicitud se ha completado con éxito',
                'comprobante_url' => url("/mercurio/pensionado/comprobante/{$id}"),
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * Obtiene el usuario actual
     *
     * @return mixed
     *
     * @throws Exception
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

    public function reloadArchivos(Request $request): JsonResponse
    {
        $pensionadoService = new PensionadoService;
        try {
            $cedtra = $request->input('cedtra');
            $id = $request->input('id');

            $mercurio38 = Mercurio38::where('cedtra', $cedtra)->where('id', $id)->first();

            if (! $mercurio38) {
                throw new DebugException('La solicitud no está disponible actualizar el documento adjunto', 501);
            } else {

                $salida = [
                    'documentos_adjuntos' => $pensionadoService->archivosRequeridos($mercurio38),
                    'success' => true,
                ];
            }
        } catch (Exception $e) {
            return $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    /**
     * cancelarSolicitud function
     *
     * @return void
     */
    public function cancelarSolicitud(Request $request): JsonResponse
    {
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $id = $request->input('id');

            $m41 = Mercurio38::where('id', $id)->where('documento', $documento)->where('coddoc', $coddoc)->first();
            if ($m41) {
                Mercurio38::where('id', $id)->delete();
            }
            $salida = [
                'success' => true,
                'msj' => 'El registro se borro con éxito del sistema.',
            ];
        } catch (Exception $e) {
            return $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    public function downloadFile($archivo = '')
    {
        $this->setResponse('view');
        $fichero = 'public/temp/' . $archivo;

        return $this->renderFile($fichero);
    }

    public function params(): JsonResponse
    {
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

            $coddoc = $tipoDocumentos;
            $data = [
                'tipdoc' => $coddoc,
                'tipper' => tipper_array(),
                'tipsoc' => $tipsoc,
                'calemp' => calemp_array(),
                'codciu' => $codciu,
                'coddocrepleg' => $coddocrepleg,
                'codzon' => ParamsPensionado::getZonas(),
                'codact' => ParamsPensionado::getActividades(),
                'tipemp' => ParamsPensionado::getTipoEmpresa(),
                'codcaj' => ParamsPensionado::getCodigoCajas(),
                'ciupri' => ParamsPensionado::getCiudades(),
                'sexo' => sexos_array(),
                'estciv' => estados_civiles_array(),
                'tipdis' => tipo_discapacidad_array(),
                'nivedu' => nivel_educativo_array(),
                'tipcon' => tipo_contrato(),
                'vivienda' => vivienda_array(),
                'tipafi' => ParamsPensionado::getTipoAfiliado(),
                'cargo' => ParamsPensionado::getOcupaciones(),
                'orisex' => orientacion_sexual_array(),
                'facvul' => vulnerabilidades_array(),
                'peretn' => pertenencia_etnica_array(),
                'ciunac' => ParamsPensionado::getCiudades(),
                'tippag' => tipo_pago_array(),
                'resguardo_id' => ParamsPensionado::getResguardos(),
                'pub_indigena_id' => ParamsPensionado::getPueblosIndigenas(),
                'codban' => ParamsPensionado::getBancos(),
                'tipsal' => tipsal_array(),
                'tipcue' => tipo_cuenta_array(),
                'empleador' => condicionSN(),
                'vendedor' => condicionSN(),
                'labora_otra_empresa' => condicionSN(),
                'cabhog' => condicionSN(),
                'rural' => condicionSN(), /* rural laboral */
                'ruralt' => condicionSN(), /* rural residencia */
                'trasin' => condicionSN(),
                'autoriza' => condicionSN(),
                'comision' => condicionSN(),
                'captra' => condicionSN(),
                'indipais' => indicativos_paises_array(),
                'indidepa' => indicativos_departamentos_array(),
            ];

            $formulario = FormularioDinamico::where('name', 'mercurio38')->first();
            $componentes = $formulario->componentes()->get();
            $componentes = $componentes->map(function ($componente) use ($data) {
                $_componente = $componente->toArray();
                if (isset($data[$componente->name])) {
                    $_componente['data_source'] = $data[$componente->name];
                }
                $_componente['id'] = $componente->name;

                return $_componente;
            });

            $solicitante = Mercurio07::where('documento', $this->user['documento'])
                ->where('coddoc', $this->user['coddoc'])
                ->where('tipo', $this->tipo)
                ->first();

            $hoy = Carbon::now();
            $componentes['props'] = [
                'name' => null,
                'cedtra' => $solicitante->documento,
                'coddoc' => $solicitante->coddoc,
                'tipdoc' => $solicitante->coddoc,
                'tipo' => $solicitante->tipo,
                'email' => $solicitante->email,
                'codciu' => $solicitante->codciu,
                'fecsol' => $hoy->format('Y-m-d'),
            ];
            $salida = [
                'success' => true,
                'data' => $componentes,
                'msj' => 'OK',
            ];
        } catch (Exception $e) {
            return $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    public function searchRequest(?string $id = null): JsonResponse
    {
        try {
            if (is_null($id)) {
                throw new DebugException('Error no hay solicitud a buscar', 301);
            }
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $solicitud = Mercurio38::where('id', $id)->where('documento', $documento)->where('coddoc', $coddoc)->first();
            if ($solicitud == false) {
                throw new DebugException('Error la solicitud no está disponible para acceder.', 301);
            } else {
                $data = $solicitud->toArray();
            }
            $salida = [
                'success' => true,
                'data' => $data,
                'msj' => 'OK',
            ];
        } catch (Exception $e) {
            return $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    public function consultaDocumentos(?string $id = null): JsonResponse
    {
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $pensionadoService = new PensionadoService;

            $sindepe = Mercurio38::whereRaw(
                "id =? AND documento=? AND coddoc=?",
                [$id, $documento, $coddoc]
            )
                ->first();

            if ($sindepe == false) {
                throw new DebugException('Error no se puede identificar el propietario de la solicitud', 301);
            }
            $salida = [
                'success' => true,
                'data' => $pensionadoService->dataArchivosRequeridos($sindepe),
                'msj' => 'OK',
            ];
        } catch (Exception $e) {
            return $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    public function borrar(Request $request): JsonResponse
    {
        $this->setResponse('ajax');
        $generales = new GeneralService;
        $generales->startTrans('mercurio41');
        try {

            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $id = $request->input('id');
            $solicitud = Mercurio38::where('id', $id)->where('documento', $documento)->where('coddoc', $coddoc)->first();
            if ($solicitud) {
            }
            Mercurio38::where('id', $id)->where('documento', $documento)->where('coddoc', $coddoc)->delete();
            $generales->finishTrans();
            $response = [
                'success' => true,
                'msj' => 'Ok',
            ];
        } catch (Exception $e) {
            return $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    public function renderTable(Request $request, ?string $estado = null)
    {
        return $this->renderSolicitudesGrid(
            $request,
            $estado,
            new PensionadoService,
            'mercurio/pensionado/tmp/solicitudes',
            'pensionados'
        );
    }

    public function seguimiento(Request $request): JsonResponse
    {
        try {
            $pensionadoService = new PensionadoService;
            $out = $pensionadoService->consultaSeguimiento($request->input('id'));
            $salida = [
                'success' => true,
                'data' => $out,
            ];
        } catch (Exception $e) {
            return $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    public function descargar_formulario($id) {}

    /**
     * Administra la cuenta de un pensionado
     *
     * @param  string  $id  ID de la solicitud
     * @return mixed
     *
     * @throws Exception
     */
    public function administrar_cuenta(?string $id = null)
    {
        try {
            if (is_null($id)) {
                throw new DebugException('El ID de la solicitud es requerido');
            }

            // Obtener la solicitud
            $solicitud = Mercurio38::where('id', $id)->where('estado', 'A')->first();

            if (! $solicitud) {
                throw new DebugException('No se encontró la solicitud solicitada');
            }

            // Preparar datos del usuario
            $userData = [
                'id' => $id,
                'cedtra' => $solicitud->cedtra,
                'tipopc' => $this->tipopc,
                'codciu' => $this->user['codciu'],
                'codusu' => $this->user['codusu'],
                'codpai' => $this->user['codpai'],
                'codemp' => $this->user['codemp'],
                'codofi' => $this->user['codofi'],
                'codrol' => $this->user['codrol'],
            ];

            $request = new Request($userData);
            $change = new ChangeCuentaService;

            if ($change->initializa($request)) {
                set_flashdata('success', [
                    'msj' => 'La administración de la cuenta se ha inicializado con éxito.',
                    'code' => 200,
                ]);

                return redirect('principal/index');
            }

            throw new DebugException('No se pudo inicializar la administración de la cuenta', 301);
        } catch (Exception $e) {
            $exception = $this->captureException($e, request());
            set_flashdata('error', [
                'msj' => $exception['msj'],
                'code' => $e->getCode(),
            ]);

            return redirect()->route('principal/index');
        }
    }
}
