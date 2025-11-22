<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsConyuge;
use App\Models\Adapter\DbBase;
use App\Models\FormularioDinamico;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio10;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio37;
use App\Services\Entidades\ConyugeService;
use App\Services\Entidades\TrabajadorService;
use App\Services\FormulariosAdjuntos\ConyugeAdjuntoService;
use App\Services\FormulariosAdjuntos\Formularios;
use App\Services\PreparaFormularios\TrabajadorFormulario;
use App\Services\Api\ApiSubsidio;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ConyugeController extends ApplicationController
{
    protected $asignarFuncionario;

    protected $tipopc = '3';

    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    public function index()
    {
        try {
            $tipo = $this->tipo;
            $empresa = null;
            if (
                $tipo == 'E' ||
                $tipo == 'I' ||
                $tipo == 'O' ||
                $tipo == 'F'
            ) {
                $procesadorComando = new ApiSubsidio();
                $procesadorComando->send(
                    [
                        'servicio' => 'ComfacaEmpresas',
                        'metodo' => 'informacion_empresa',
                        'params' => ['nit' => $this->user['documento']],
                    ]
                );

                $empresa = $procesadorComando->toArray();
                if (! isset($empresa['data'])) {
                    set_flashdata('error', [
                        'msj' => 'Error al acceder al servicio de consulta de empresa.',
                        'code' => 401,
                    ]);

                    return redirect()->route('principal/index');
                    exit;
                }

                if ($empresa['data']['estado'] === 'I') {
                    set_flashdata('error', [
                        'msj' => 'La empresa ya no está activa para realizar afiliación de beneficiarios.',
                        'code' => 401,
                    ]);

                    return redirect()->route('principal/index');
                    exit;
                }
            }

            return view('mercurio.conyuge.index', [
                'documento' => $this->user['documento'],
                'tipo' => $this->tipo,
                'title' => 'Afiliación de cónyuges',
                'empresa' => $empresa,
            ]);
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
            set_flashdata('error', [
                'msj' => $salida['msj'],
                'code' => $salida['code'],
            ]);
            return redirect()->route('principal/index');
        }
    }

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
                'data' => $mercurio37->getArray(),
            ];
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    public function traerConyugue(Request $request)
    {
        try {
            $cedcon = $request->input('cedcon');
            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_conyuge',
                    'params' => [
                        'cedcon' => $cedcon,
                    ],
                ]
            );

            $mercurio32 = new Mercurio32;
            $out = $procesadorComando->toArray();
            if ($out['success']) {
                $datos_conyuge = $out['data'];
                $mercurio32 = new Mercurio32($datos_conyuge);
            }

            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_trabajador',
                    'params' => [
                        'cedtra' => $cedcon,
                    ],
                ]
            );

            $out = $procesadorComando->toArray();
            if ($out['success']) {
                $datos_trabajador = $out['data'];
                $mercurio32 = new Mercurio32($datos_trabajador);
                $mercurio32->setTipdoc($datos_trabajador['coddoc']);
            }

            $response = [
                'success' => true,
                'data' => $mercurio32->toArray(),
            ];
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    public function descargarDeclaracion()
    {
        $this->setResponse('view');
        $archivo = 'declaracion_juramentada_nueva.pdf';
        $fichero = 'public/docs/formulario_mercurio/' . $archivo;
        $ext = substr(strrchr($archivo, '.'), 1);
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

    public function downloadDocumentos($archivo = '')
    {
        $fichero = 'public/docs/formulario_mercurio/' . $archivo;
        $ext = substr(strrchr($archivo, '.'), 1);
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

    public function downloadReporte($archivo = '')
    {
        $fichero = 'public/temp/' . $archivo;
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

    public function buscarConyugues($db, $estado = '')
    {
        $documento = $this->user['documento'];
        $tipo = $this->user['tipo'];
        $coddoc = $this->user['coddoc'];

        if (empty($estado)) {
            $mercurio32 = Mercurio32::where('tipo', $tipo)
                ->where('coddoc', $coddoc)
                ->where('documento', $documento)
                ->whereIn('estado', ['T', 'D', 'P', 'A', 'X'])
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $mercurio32 = Mercurio32::where('tipo', $tipo)
                ->where('coddoc', $coddoc)
                ->where('documento', $documento)
                ->where('estado', $estado)
                ->orderBy('id', 'desc')
                ->get();
        }
        $conyuges = [];
        foreach ($mercurio32 as $mercurio32) {
            $rqs = Mercurio10::select(DB::raw('count(mercurio10.numero) as cantidad'))
                ->join('mercurio32', 'mercurio32.id', '=', 'mercurio10.numero')
                ->where('mercurio10.tipopc', $this->tipopc)
                ->where('mercurio32.id', $mercurio32->id)
                ->first();

            $trayecto = Mercurio10::select(DB::raw('max(mercurio10.item), mercurio10.*'))
                ->join('mercurio32', 'mercurio32.id', '=', 'mercurio10.numero')
                ->where('mercurio10.tipopc', $this->tipopc)
                ->where('mercurio32.id', $mercurio32->id)
                ->first();

            $conyuge = $mercurio32->toArray();
            $conyuge['cantidad_eventos'] = $rqs->cantidad;
            $conyuge['fecha_ultima_solicitud'] = $trayecto->fecsis;
            switch ($mercurio32->estado) {
                case 'T':
                    $conyuge['estado_detalle'] = 'TEMPORAL';
                    break;
                case 'D':
                    $conyuge['estado_detalle'] = 'DEVUELTO';
                    break;
                case 'A':
                    $conyuge['estado_detalle'] = 'APROBADO';
                    break;
                case 'X':
                    $conyuge['estado_detalle'] = 'RECHAZADO';
                    break;
                case 'P':
                    $conyuge['estado_detalle'] = 'Pendiente De Validación CAJA';
                    break;
                default:
                    $conyuge['estado_detalle'] = 'T';
                    break;
            }
            $conyuges[] = $conyuge;
        }

        return $conyuges;
    }

    public function serializeData(Request $request)
    {
        $asignarFuncionario = new AsignarFuncionario;
        $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);
        $fecsol = Carbon::now();

        return [
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
            'fecing' => (is_null($request->input('fecing')) || $request->input('fecing') == '') ? date('Y-m-d') : $request->input('fecing'),
            'salario' => ($request->input('salario')) ? $request->input('salario') : '0',
            'captra' => $request->input('captra'),
            'tipdis' => $request->input('tipdis'),
            'nivedu' => $request->input('nivedu'),
            'autoriza' => $request->input('autoriza'),
            'numcue' => ($request->input('numcue') == null || $request->input('numcue') == '') ? '0' : $request->input('numcue'),
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
            'usuario' => $usuario,
            'zoneurbana' => $request->input('zoneurbana') ?? 'N',
        ];
    }

    public function guardar(Request $request)
    {
        $this->db->begin();
        try {

            $conyugeService = new ConyugeService;
            $id = $request->input('id');
            $clave_certificado = $request->input('clave');
            $params = $this->serializeData($request);

            if (is_null($id) || $id == '') {
                $params['id'] = null;
                $params['estado'] = 'T';
                $solicitud = $conyugeService->createByFormData($params);
            } else {
                $res = $conyugeService->updateByFormData($id, $params);
                if ($res == false) {
                    throw new DebugException('Error no se actualizo los datos', 301);
                }
                $solicitud = $conyugeService->findById($id);
            }

            ConyugeAdjuntoService::generarAdjuntos(
                $solicitud,
                $this->tipopc,
                $clave_certificado
            );

            $salida = [
                'msj' => 'Proceso se ha completado con éxito',
                'success' => true,
                'data' => $solicitud->getArray(),
            ];

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $salida = $this->handleException($e, $request);
        }
        return response()->json($salida);
    }

    public function mapper()
    {
        return [
            'cedtra' => 'cedula',
            'tipdoc' => 'tipo documento',
            'priape' => 'primer apellido',
            'segape' => 'segundo apellido',
            'prinom' => 'primer nombre',
            'segnom' => 'segundo nombre',
            'fecnac' => 'fecha nacimiento',
            'ciunac' => 'codigo ciudad nacimiento',
            'estciv' => 'estado civil',
            'cabhog' => 'cabeza hogar',
            'codciu' => 'código ciudad residencia',
            'codzon' => 'código ciudad laboral',
            'fecing' => 'fecha ingreso',
            'tipsal' => 'tipo salario',
            'captra' => 'capacidad trabajar',
            'tipdis' => 'tipo discapacidad',
            'nivedu' => 'nivel educativo',
            'rural' => 'residencia rural',
            'horas' => 'horas trabajar',
            'tipcon' => 'tipo contrato',
            'trasin' => 'sindicalizado',
            'tipafi' => 'tipo afiliado',
            'orisex' => 'orientación sexual',
            'facvul' => 'factor vulnerabilidad',
            'peretn' => 'etnica',
            'dirlab' => 'direccion laboral',
            'autoriza' => 'tratamiento datos',
            'tipjor' => 'tipo jornada',
            'ruralt' => 'labor rural',
            'comision' => 'recibe comisión',
            'fecsol' => 'fecha solicitid',
        ];
    }

    public function enviarCaja(Request $request)
    {
        $this->db->begin();
        try {
            $id = $request->input('id');
            $conygueService = new ConyugeService;
            $asignarFuncionario = new AsignarFuncionario;
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);
            $conygueService->enviarCaja(new SenderValidationCaja, $id, $usuario);
            $salida = [
                'success' => true,
                'msj' => 'El envio de la solicitud se ha completado con éxito',
            ];
            $this->db->commit();
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
            $this->db->rollBack();
        }
        return response()->json($salida);
    }

    public function borrar(Request $request)
    {
        $this->db->begin();
        try {
            $documento = $this->user['documento'];
            $id = $request->input('id');

            $m32 = Mercurio32::where('id', $id)->where('documento', $documento)->first();
            if ($m32) {
                if ($m32->estado != 'T') {
                    Mercurio10::where('numero', $id)->where('tipopc', $this->tipopc)->delete();
                }
                Mercurio32::where('id', $id)->where('documento', $documento)->delete();
            }
            $salida = [
                'success' => true,
                'msj' => 'El registro se borro con éxito del sistema.',
            ];
            $this->db->commit();
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
            $this->db->rollBack();
        }
        return response()->json($salida);
    }

    public function params()
    {
        try {
            $tipo = $this->tipo;
            $trabajadorService = new TrabajadorService;

            $mercurio31 = $trabajadorService->findRequestByDocumentoCoddoc($this->user['documento'], $this->user['coddoc']);
            if ($tipo == 'E') {
                $trabajadoresSisu = $trabajadorService->findApiTrabajadoresByNit($this->user['documento']);

                $listAfiliados = collect($trabajadoresSisu)->map(function ($row) {
                    return ['cedula' => $row['cedtra'], 'nombre_completo' => $row['nombre']];
                });

                $cedtras = [];
                foreach ($listAfiliados as $value) {
                    $cedtras[$value['cedula']] = $value['cedula'];
                }
                foreach ($mercurio31 as $value) {
                    $cedtras[$value['cedula']] = $value['cedula'];
                }

                $listAfiliados = array_merge($listAfiliados->toArray(), $mercurio31);

                $procesadorComando = new ApiSubsidio();
                $procesadorComando->send(
                    [
                        'servicio' => 'ComfacaEmpresas',
                        'metodo' => "informacion_empresa",
                        'params' => [
                            'nit' => $this->user['documento'],
                        ]
                    ]
                );
                $rqs = $procesadorComando->toArray();
                $empresa_sisu = $rqs['data'];
                $nit = [$this->user['documento'] => $this->user['documento']];
                $numero_nit = $this->user['documento'];
            } else {
                $cedtras[$this->user['documento']] = $this->user['documento'];
                $empresa_sisu = false;

                $listAfiliados = collect($mercurio31)->map(function ($row) {
                    return ['cedula' => $row['cedula'], 'nombre_completo' => $row['nombre']];
                });
                $nit = collect($mercurio31)->pluck('nit', 'nit');
                $listAfiliados[] = ['cedula' => $this->user['documento'], 'nombre_completo' => $this->user['nombre']];

                $trabajador_sisu = $trabajadorService->buscarTrabajadorSubsidio($this->user['documento']);
                if ($trabajador_sisu) {
                    $numero_nit = $trabajador_sisu['nit'];
                } else {
                    $numero_nit = $mercurio31['nit'];
                }
            }

            $coddoc = Gener18::whereNotIn('coddoc', ['7', '5', '2'])->pluck('detdoc', 'coddoc');
            $coddocrepleg = tipo_document_repleg_detalle();
            unset($coddocrepleg['RC']);
            unset($coddocrepleg['TI']);

            $codzon = Gener09::where("codzon", '>=', 18000)
                ->where("codzon", "<=", 19000)
                ->pluck('detzon', 'codzon');

            $codciu = Gener09::all()->pluck('detzon', 'codzon');
            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_conyuges',
                ]
            );
            $paramsConyuge = new ParamsConyuge;
            $paramsConyuge->setDatosCaptura($procesadorComando->toArray());

            $data = [
                'tipdoc' => $coddoc,
                'coddoc' => $coddoc,
                'codzon' => $codzon,
                'codciu' => $codciu,
                'sexo' => sexos_array(),
                'estciv' => estados_civiles_array(),
                'captra' => capacidad_trabajar(),
                'nivedu' => nivel_educativo_array(),
                'tipviv' => vivienda_array(),
                'ciunac' => $codciu,
                'tippag' => tipo_pago_array(),
                'tipcue' => tipo_cuenta_array(),
                'autoriza' => autoriza_array(),
                'tipsal' => tipsal_array(),
                'ciures' => $codciu,
                'tipdis' => tipo_discapacidad_array(),
                'peretn' => pertenencia_etnica_array(),
                'resguardo_id' => ParamsConyuge::getResguardos(),
                'pub_indigena_id' => ParamsConyuge::getPueblosIndigenas(),
                'cargo' => ParamsConyuge::getOcupaciones(),
                'codban' => ParamsConyuge::getBancos(),
                'comper' => ParamsConyuge::getCompaneroPermanente(),
                'codocu' => ParamsConyuge::getOcupaciones(),
                'nit' => $nit,
                'cedtra' => $cedtras,
                'tipo' => $tipo,
            ];

            $formulario = FormularioDinamico::where('name', 'mercurio32')->first();
            $componentes = $formulario->componentes()->get();
            $componentes = $componentes->map(function ($componente) use ($data) {

                $_componente = $componente->toArray();
                $_componente['id'] = $componente->name;
                if ($data['tipo'] !== 'E' && ($componente->name == 'nit' || $componente->name == 'cedtra')) {
                    $_componente['form_type'] = 'input';
                    $_componente['is_readonly'] = true;
                }

                if ($data['tipo'] === 'E' && ($componente->name == 'nit' || $componente->name == 'cedtra')) {
                    $_componente['form_type'] = 'select';
                    $_componente['search_type'] = "collection";
                    $_componente['type'] = 'text';
                }

                if (isset($data[$componente->name])) {
                    $_componente['data_source'] = $data[$componente->name];
                }

                return $_componente;
            });

            $componentes['props'] = [
                'name' => null,
                'tipo' => $tipo,
                'list_afiliados' => $listAfiliados,
                'empresa_sisu' => $empresa_sisu,
                'nit' => $numero_nit
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

    public function renderTable(Request $request, Response $response, string $estado = '')
    {
        try {
            $conyugeService = new ConyugeService;
            $html = View(
                'mercurio/conyuge/tmp/solicitudes',
                [
                    'path' => base_path(),
                    'conyuges' => $conyugeService->findAllByEstado($estado),
                ]
            )->render();

            $this->setResponse('view');
            return $this->renderText($html);
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
            return response()->json($salida);
        }
    }

    public function valida(Request $request, Response $response)
    {
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $cedcon = $request->input('cedcon');

            $solicitud_previa = Mercurio32::where("cedcon", $cedcon)
                ->where("documento", $documento)
                ->where("coddoc", $coddoc)
                ->first();

            $conyuge = false;
            if ($solicitud_previa) {
                $conyuge = $solicitud_previa->toArray();
            }

            if (! $conyuge) {
                $procesadorComando = new ApiSubsidio();
                $procesadorComando->send(
                    [
                        'servicio' => 'ComfacaEmpresas',
                        'metodo' => 'informacion_conyuge',
                        'params' => [
                            'cedcon' => $cedcon,

                        ],
                    ]
                );
                $salida = $procesadorComando->toArray();
                if ($salida['success']) {
                    if ($salida['data']) {
                        $conyuge = $salida['data'];
                    }
                }
            }

            $response = [
                'success' => true,
                'solicitud_previa' => ($solicitud_previa > 0) ? true : false,
                'conyuge' => $conyuge,
            ];
        } catch (\Throwable $e) {
            $response = $this->handleException($e, $request);
        }

        return response()->json($response);
    }

    public function searchRequest(Request $request, Response $response, string $id)
    {
        $this->setResponse('ajax');
        try {
            if (is_null($id)) {
                throw new DebugException('Error no hay solicitud a buscar', 301);
            }
            $documento = parent::getActUser('documento');
            $coddoc = parent::getActUser('coddoc');

            $solicitud = Mercurio32::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->first();

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
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
    }

    public function consultaDocumentos($id)
    {
        $this->setResponse('ajax');
        try {
            $documento = parent::getActUser('documento');
            $coddoc = parent::getActUser('coddoc');
            $conService = new ConyugeService;

            $sindepe = Mercurio32::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->whereNotIn('estado', ['I', 'X'])
                ->first();

            if ($sindepe == false) {
                throw new DebugException('Error no se puede identificar el propietario de la solicitud', 301);
            }
            $salida = [
                'success' => true,
                'data' => $conService->dataArchivosRequeridos($sindepe),
                'msj' => 'OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
        }

        return response()->json($salida);
    }

    public function formulario($id)
    {
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $mercurio32 = Mercurio32::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->first();
            if (! $mercurio32) {
                throw new DebugException('Error no se puede generar el fomulario a la solicitud no es valida', 301);
            }

            $mercurio31 = Mercurio31::where('cedtra', $mercurio32->getCedtra())
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->first();

            if (! $mercurio31) {
                throw new DebugException('Error no se puede generar el fomulario a la solicitud no es valida', 301);
            }

            $trabajadorFormulario = new TrabajadorFormulario(
                [
                    'documento' => $documento,
                    'coddoc' => $coddoc,
                ]
            );

            $timer = strtotime('now');
            $file = "formulario_afiliacion_{$mercurio32->getCedtra()}_{$timer}.pdf";
            $formularios = new Formularios;

            $formularios->trabajadorAfiliacion(
                $trabajadorFormulario->main($mercurio31),
                $file
            );

            $response = [
                'success' => true,
                'name' => $file,
                'url' => 'conyuge/download_reporte/' . $file,
            ];
        } catch (\Throwable $e) {
            $response = $this->handleException($e, request());
        }

        return response()->json($response);
    }

    public function seguimiento(Request $request)
    {
        try {
            $id = $request->input('id');
            $conyugeService = new ConyugeService;
            $out = $conyugeService->consultaSeguimiento($id);
            $salida = [
                'success' => true,
                'data' => $out,
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, $request);
        }

        return response()->json($salida);
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
