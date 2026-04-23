<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\FormularioDinamico;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio37;
use App\Models\Subsi54;
use App\Services\Entidades\TrabajadorService;
use App\Services\FormulariosAdjuntos\TrabajadorAdjuntoService;
use App\Services\Api\ApiSubsidio;
use App\Services\Entidades\EmpresaService;
use App\Services\Srequest;
use App\Services\Tag;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrabajadorController extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    protected $tipopc = '1';

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    /**
     * GET /mercurio/trabajador/index
     */
    public function index()
    {
        try {
            if ($this->tipo !== 'E') {
                return redirect()->route('mercurio.index');
            }
            $input_nits = null;

            $data = (new EmpresaService)->buscarEmpresaSubsidio($this->user['documento']);
            if ($data) {
                $mempresa = new Mercurio30();
                $mempresa->fill($data);
            } else {
                $mempresa = Mercurio30::where('documento', $this->user['documento'])
                    ->where('coddoc', $this->user['coddoc'])
                    ->first();
            }

            $razsoc = $mempresa->razsoc;
            $nits = [$mempresa->nit => $mempresa->nit];

            $input_nits = Tag::selectStatic(
                new Srequest([
                    'name' => 'nit',
                    'class' => 'form-control top',
                    'id' => 'nit',
                    'options' => $nits,
                    'dummyText' => 'Seleccione un nit',
                ])
            );

            $input_razsoc = Tag::textUpperField(
                'name:razsoc',
                'class:form-control',
                'id:razsoc',
                'readonly:1',
                "value:{$razsoc}"
            );

            return view('mercurio/trabajador/index', [
                'tipo' => $this->tipo,
                'documento' => $this->user['documento'],
                'coddoc' => $this->user['coddoc'],
                'title' => 'Afiliación de trabajadores',
                'input_nits' => $input_nits,
                'input_razsoc' => $input_razsoc,
            ]);
        } catch (\Throwable $e) {
            $response = $this->handleException($e, request());
            set_flashdata('error', [
                'msj' => $response['msj'],
                'code' => $response['code'],
            ]);
            return redirect()->route('principal/index');
        }
    }

    /**
     * POST /trabajador/valide_nit
     * Valida existencia y estado de empresa vía API Comman
     */
    public function valideNit(Request $request)
    {
        try {
            $nit = $request->input('nit');
            if (! $nit) {
                throw new DebugException('El nit es requerido', 422);
            }

            $ps = new ApiSubsidio();
            $ps->send([
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => ['nit' => $nit],
            ]);
            $datos = $ps->toArray();

            if (! isset($datos['data']) || count($datos['data']) == 0) {
                $response = ['success' => false, 'msj' => 'El nit no existe'];
            } elseif (($datos['data']['estado'] ?? null) === 'I') {
                $response = ['success' => false, 'msj' => 'La empresa esta inactiva no puede crear nuevos trabajadores'];
            } else {
                $response = ['success' => true, 'msj' => '', 'data' => $datos['data']['razsoc'] ?? null];
            }

            $salida = $response;
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    /**
     * POST /trabajador/borrar_archivo
     */
    public function borrarArchivo(Request $request)
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
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * POST /trabajador/guardar_archivo
     */
    public function guardarArchivo(Request $request)
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

            $response = [
                'success' => true,
                'msj' => 'Ok archivo procesado',
                'data' => method_exists($mercurio37, 'getArray') ? $mercurio37->getArray() : null,
            ];
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * POST /trabajador/traer_trabajador
     */
    public function traerTrabajador(Request $request)
    {
        try {

            $cedtra = $request->input('cedtra');
            $nit = $request->input('nit');

            $datos_trabajador = [];

            $ps = new ApiSubsidio();
            $ps->send([
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => [
                    'cedtra' => $cedtra,
                ],
            ]);

            $out = $ps->toArray();
            if (($out['success'] ?? false) && isset($out['data'])) {
                $datos_trabajador = $out['data'];
            }

            $mercurio31 = new Mercurio31($datos_trabajador);
            $mercurio31->setLog('0');

            $response = [];
            $response['multi'] = false;
            if ($mercurio31->getNit() != $nit) {
                $response['multi'] = true;
            }

            $response['flag'] = true;
            if ($mercurio31->getNit() == $nit && $mercurio31->getEstado() == 'A') {
                $response['flag'] = false;
                $response['msg'] = 'El afiliado ya se encuentra registrado o Activo con la misma empresa.';
            }

            if ($mercurio31->getCedtra() == '') {
                $mercurio31 = Mercurio31::where('cedtra', $cedtra)->first() ?: new Mercurio31;
            }

            $response['data'] = $mercurio31->toArray();
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    /**
     * POST /trabajador/enviar_caja
     */
    public function enviarCaja(Request $request)
    {
        try {
            $id = $request->input('id');
            $trabajadorService = new TrabajadorService;
            $asignarFuncionario = new AsignarFuncionario;
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);
            $trabajadorService->enviarCaja(new SenderValidationCaja, $id, $usuario); // TODO: importar/clase correcta si existe
            $salida = ['success' => true, 'msj' => 'El envio de la solicitud se ha completado con éxito'];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    public function seguimiento(Request $request)
    {
        try {
            $id = $request->input('id');
            $trabajadorService = new TrabajadorService;
            $out = $trabajadorService->consultaSeguimiento($id);
            $salida = [
                'success' => true,
                'data' => $out
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    public function params()
    {
        try {
            $nit = $this->user['documento'];
            $coddoc = Gener18::whereNotIn('coddoc', ['7', '5', '2'])->pluck('detdoc', 'coddoc');
            $coddocrepleg = tipo_document_repleg_detalle();
            unset($coddocrepleg['RC']);
            unset($coddocrepleg['TI']);

            $tipsoc = Subsi54::all()->pluck('detalle', 'tipsoc');
            $codzon = Gener09::where("codzon", '>=', 18000)
                ->where("codzon", "<=", 19000)
                ->pluck('detzon', 'codzon');

            $codciu = Gener09::all()->pluck('detzon', 'codzon');

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_trabajadores',
                ]
            );
            $paramsTrabajador = new ParamsTrabajador;
            $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => "informacion_empresa",
                    'params' => [
                        'nit' => $nit,
                    ]
                ]
            );
            $rqs = $procesadorComando->toArray();

            $codsuc = [];
            $empresa_sisu = $rqs['data'];
            if ($empresa_sisu) {
                foreach ($empresa_sisu['sucursales'] as $data) {
                    if ($data['estado'] == 'I') {
                        continue;
                    }
                    if (isset($codciu[$data['codzon']])) {
                        $codsuc["{$data['codsuc']}"] = $data['detalle'] . ' - DE ' . $codciu[$data['codzon']];
                    } else {
                        $codsuc["{$data['codsuc']}"] = $data['detalle'];
                    }
                }
            }

            $data = [
                'ciunac' => ParamsTrabajador::getCiudades(),
                'tipafi' => ParamsTrabajador::getTipoAfiliado(),
                'cargo' => ParamsTrabajador::getOcupaciones(),
                'resguardo_id' => ParamsTrabajador::getResguardos(),
                'pub_indigena_id' => ParamsTrabajador::getPueblosIndigenas(),
                'codban' => ParamsTrabajador::getBancos(),
                'tipo' => get_array_tipos(),
                'tipdoc' => $coddoc,
                'tipper' => tipper_array(),
                'tipsoc' => $tipsoc,
                'calemp' => calemp_array(),
                'codciu' => $codciu,
                'codzon' => $codzon,
                'coddocrepleg' => $coddocrepleg,
                'sexo' => sexos_array(),
                'estciv' => estados_civiles_array(),
                'tipdis' => tipo_discapacidad_array(),
                'nivedu' => nivel_educativo_array(),
                'tipcon' => tipo_contrato(),
                'vivienda' => vivienda_array(),
                'orisex' => orientacion_sexual_array(),
                'facvul' => vulnerabilidades_array(),
                'peretn' => pertenencia_etnica_array(),
                'tippag' => tipo_pago_array(),
                'tipsal' => tipsal_array(),
                'tipcue' => tipo_cuenta_array(),
                'tipjor' => tipo_jornada_array(),
                'codsuc' => $codsuc,

                'empleador' => condicionSN(),
                'giro' => condicionSN(),
                'vendedor' => condicionSN(),
                'labora_otra_empresa' => condicionSN(),
                'cabhog' => condicionSN(),
                'rural' => condicionSN(), /* rural laboral */
                'ruralt' => condicionSN(), /* rural residencia */
                'trasin' => condicionSN(),
                'autoriza' => condicionSN(),
                'comision' => condicionSN(),
                'captra' => captra_array(),
                'indipais' => indicativos_paises_array(),
                'indidepa' => indicativos_departamentos_array()
            ];

            $formulario = FormularioDinamico::where('name', 'mercurio31')->first();
            $componentes = $formulario->componentes()->get();
            $componentes = $componentes->map(function ($componente) use ($data) {
                $_componente = $componente->toArray();
                if (isset($data[$componente->name])) {
                    $_componente['data_source'] = $data[$componente->name];
                }
                $_componente['id'] = $componente->name;
                return $_componente;
            });

            $componentes['props'] = [
                'name' => null,
                'nit' => $nit,
                'razsoc' => $empresa_sisu['razsoc'],
            ];

            $salida = [
                'success' => true,
                'data' => $componentes,
                'msj' => 'OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }
        return response()->json($salida);
    }

    /**
     * renderTable function
     * @changed [2023-12-00]
     * @author elegroag <elegroag@ibero.edu.co>
     * @param  string  $estado
     * @return string
     */
    public function renderTable(Request $request, ?string $estado = null)
    {
        try {
            $trabajadorService = new TrabajadorService();
            $html = view(
                'mercurio/trabajador/tmp/solicitudes',
                [
                    'path' => base_path(),
                    'trabajadores' => $trabajadorService->findAllByEstado($estado),
                ]
            )->render();

            $this->setResponse('view');
            return $this->renderText($html);
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
            return $this->renderText($salida);
        }
    }

    public function searchRequest($id)
    {
        try {
            if (is_null($id)) {
                throw new DebugException('Error no hay solicitud a buscar', 301);
            }
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $solicitud = Mercurio31::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->first();

            if (!$solicitud) {
                throw new DebugException('Error la solicitud no está disponible para acceder.', 301);
            }

            $salida = [
                'success' => true,
                'data' => $solicitud->toArray(),
                'msj' => 'OK',
            ];
        } catch (Exception $e) {
            $salida = $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    /**
     * guardar function
     * @changed [2024-03-10]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return JsonResponse
     */
    public function guardar(Request $request): JsonResponse
    {
        $this->db->begin();
        try {
            $trabajadorService = new TrabajadorService();
            $clave_certificado = $request->input('clave');
            $id = $request->input('id');

            $params = $this->serializeData($request);
            $params['tipo'] = $this->tipo;
            $params['coddoc'] = $this->user['coddoc'];
            $params['documento'] = $this->user['documento'];

            if (is_null($id) || $id == '') {
                $solicitud = $trabajadorService->createByFormData($params);
            } else {
                $res = $trabajadorService->updateByFormData($id, $params);
                if ($res == false) {
                    throw new DebugException('Error no se actualizo los datos', 301);
                }
                $solicitud = $trabajadorService->findById($id);
            }

            $trabajadorService->paramsApi();

            TrabajadorAdjuntoService::generarAdjuntos(
                $solicitud,
                $this->tipopc,
                $clave_certificado
            );

            $salida = [
                'msj' => 'Proceso se ha completado con éxito',
                'success' => true,
                'data' => $solicitud->toArray(),
            ];

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            $salida = $this->handleException($e, $request);
        }
        return response()->json($salida);
    }

    public function serializeData(Request $request)
    {
        $asignarFuncionario = new AsignarFuncionario;
        $fecsol = Carbon::now();

        return [
            'fecsol' => $fecsol->format('Y-m-d'),
            'nit' => $request->input('nit'),
            'razsoc' => $request->input('razsoc'),
            'cedtra' => $request->input('cedtra'),
            'tipdoc' => $request->input('tipdoc'),
            'priape' => mb_strtoupper($request->input('priape'), 'UTF-8'),
            'segape' => mb_strtoupper($request->input('segape'), 'UTF-8'),
            'prinom' => mb_strtoupper($request->input('prinom'), 'UTF-8'),
            'segnom' => mb_strtoupper($request->input('segnom'), 'UTF-8'),
            'fecnac' => $request->input('fecnac', 'addslaches', 'extraspaces', 'striptags'),
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
            'fax' => $request->input('fax'),
            'email' => mb_strtoupper($request->input('email'), 'UTF-8'),
            'fecing' => $request->input('fecing'),
            'salario' => $request->input('salario'),
            'tipsal' => $request->input('tipsal'),
            'captra' => $request->input('captra'),
            'tipdis' => $request->input('tipdis'),
            'nivedu' => $request->input('nivedu'),
            'rural' => $request->input('rural'),
            'horas' => $request->input('horas'),
            'tipcon' => $request->input('tipcon'),
            'trasin' => $request->input('trasin'),
            'vivienda' => $request->input('vivienda'),
            'tipafi' => $request->input('tipafi'),
            'profesion' => $request->input('profesion'),
            'cargo' => $request->input('cargo'),
            'orisex' => $request->input('orisex'),
            'facvul' => $request->input('facvul'),
            'peretn' => $request->input('peretn'),
            'dirlab' => $request->input('dirlab'),
            'autoriza' => $request->input('autoriza'),
            'tipjor' => $request->input('tipjor'),
            'ruralt' => $request->input('ruralt'),
            'comision' => $request->input('comision'),
            'codsuc' => $request->input('codsuc'),
            'otra_empresa' => $request->input('otra_empresa'),
            'numcue' => $request->input('numcue') ?? '0',
            'codban' => $request->input('codban') ?? null,
            'tipcue' => $request->input('tipcue') ?? null,
            'tippag' => $request->input('tippag') ?? 'T',
            'log' => '0',
            'usuario' => $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']),
        ];
    }

    public function consultaDocumentos($id)
    {
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $traService = new TrabajadorService;
            $mtrabajador = Mercurio31::whereRaw(
                "id =? AND documento=? AND coddoc=? AND estado NOT IN('I','X')",
                [$id, $documento, $coddoc]
            )
                ->first();

            if ($mtrabajador == false) {
                throw new DebugException('Error no se puede identificar el propietario de la solicitud', 301);
            }

            $salida = [
                'success' => true,
                'data' => $traService->dataArchivosRequeridos($mtrabajador),
                'msj' => 'OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    public function borrar(Request $request)
    {
        try {
            if (!$request->get('id')) {
                throw new DebugException('Error no se puede identificar el propietario de la solicitud', 301);
            }
            $id = $request->get('id');
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $m31 = Mercurio31::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->first();
            if ($m31) {
                if ($m31->estado != 'T') {
                    Mercurio10::where('numero', $id)->delete();
                }
            }

            Mercurio37::where('numero', $id)->delete();
            Mercurio31::where('id', $id)->delete();

            $response = [
                'success' => true,
                'msj' => 'Registro eliminado con exito',
            ];
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    public function valida(Request $request)
    {
        try {
            $empresa = [];
            $nit = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $cedtra = $request->get('cedtra');

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $nit,
                    ],
                ]
            );

            $out = $procesadorComando->toArray();
            if ($out['success']) {
                $empresa = $out['data'];
            }

            $solicitud_previa = (new Mercurio31)->getCount(
                '*',
                "conditions: nit='{$nit}' and cedtra='{$cedtra}' and estado IN('T','P')"
            );

            $trabajador = false;
            $trabajadorService = new TrabajadorService;
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
                'success' => true,
                'solicitud_previa' => ($solicitud_previa > 0) ? true : false,
                'trabajador' => $trabajador,
            ];
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    public function buscarTrabajador(Request $request)
    {
        try {

            $cedtra = $request->input('cedtra');
            $ps = new ApiSubsidio();
            $ps->send([
                'servicio' => 'PoblacionAfiliada',
                'metodo' => 'datosTrabajador',
                'params' => ['cedtra' => $cedtra],
            ]);

            $out = $ps->toArray();
            if (! $out['success']) {
                $salida = [
                    'flag' => false,
                    'success' => false,
                    'msj' => $out['msj'],
                ];
            }

            $subsi15 = $out['data'];
            if (count($subsi15) == 0) {
                $salida = [
                    'flag' => false,
                    'success' => false,
                    'msj' => 'No Existe la cedula dada',
                ];
            }

            if ($subsi15['nit'] != $this->user['documento']) {
                $salida = [
                    'flag' => false,
                    'success' => false,
                    'msj' => 'el trabajador no esta registrado a su empresa',
                ];
            }

            $salida = [
                'flag' => true,
                'success' => true,
                'data' => $subsi15,
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
            $salida['flag'] = false;
        }

        return response()->json($salida);
    }
}
