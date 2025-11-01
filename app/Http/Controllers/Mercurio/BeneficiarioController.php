<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Collections\ParamsBeneficiario;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Mercurio10;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio37;
use App\Services\Entidades\BeneficiarioService;
use App\Services\Entidades\ConyugeService;
use App\Services\Entidades\TrabajadorService;
use App\Services\FormulariosAdjuntos\BeneficiarioAdjuntoService;
use App\Services\FormulariosAdjuntos\Formularios;
use App\Services\Api\ApiSubsidio;
use App\Services\Srequest;
use App\Services\Tag;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\GuardarArchivoService;
use App\Services\Utils\SenderValidationCaja;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeneficiarioController extends ApplicationController
{
    protected $tipopc = '4';

    protected $db;

    protected $user;

    protected $tipo;

    protected $codciu;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    public function index()
    {
        $empresa = null;
        $documento = $this->user['documento'];

        if (
            $this->tipo == 'E' ||
            $this->tipo == 'I' ||
            $this->tipo == 'O' ||
            $this->tipo == 'F'
        ) {
            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => ['nit' => $documento],
                ]
            );

            $empresa = $procesadorComando->toArray();
            if (! isset($empresa['data'])) {
                set_flashdata('error', [
                    'msj' => 'Error al acceder al servicio de consulta de empresa.',
                    'code' => 401,
                ]);

                return redirect('principal/index');
            }

            if ($empresa['data']['estado'] === 'I') {
                set_flashdata('error', [
                    'msj' => 'La empresa ya no está activa para realizar afiliación de beneficiarios.',
                    'code' => 401,
                ]);

                return redirect('principal/index');
            }
        }

        return view('mercurio/beneficiario/index', [
            'tipo' => $this->tipo,
            'documento' => $documento,
            'title' => 'Afiliación de beneficiarios',
            'empresa' => $empresa,
        ]);
    }

    public function traerConyuges(Request $request)
    {
        $cedtra = $request->input('cedtra');

        $cedcons = Mercurio32::where('cedtra', $cedtra)
            ->get(['cedcon', 'priape', 'segape', 'prinom'])
            ->pluck('cedcon', 'priape', 'segape', 'prinom')
            ->map(function ($conyuge) {
                return $conyuge->cedcon . '-' . $conyuge->priape . ' ' . $conyuge->segape . ' ' . $conyuge->prinom;
            })
            ->toArray();

        $ps = new ApiSubsidio();
        $ps->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'listar_conyuges_trabajador',
                'params' => [
                    'cedtra' => $cedtra,
                ],
            ]
        );

        $subsi20 = $ps->toArray();
        if ($subsi20['success'] == true) {
            $subsi20 = $subsi20['data'];
            if (count($subsi20) > 0) {
                foreach ($subsi20 as $msubsi20) {
                    $cedcons[$msubsi20['cedcon']] = $msubsi20['cedcon'] . '-' . $msubsi20['priape'] . ' ' . $msubsi20['prinom'];
                }
            }
        }

        $response = Tag::selectStatic(
            new Srequest([
                'cedcon' => $cedcons,
                'use_dummy' => true,
                'dummyValue' => '',
                'class' => 'form-control'
            ])
        );

        return response()->json($response);
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
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
                'errors' => $e->render($request)
            ];
        }

        return response()->json($response);
    }

    public function enviarCaja(Request $request)
    {
        try {
            $id = $request->input('id');
            $beneficiarioService = new BeneficiarioService;
            $asignarFuncionario = new AsignarFuncionario;
            $usuario = $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']);
            $beneficiarioService->enviarCaja(new SenderValidationCaja, $id, $usuario);
            $salida = [
                'success' => true,
                'msj' => 'El envio de la solicitud se ha completado con éxito',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
                'errors' => $e->render($request)
            ];
        }

        return response()->json($salida);
    }

    public function traerBeneficiario(Request $request)
    {
        $numdoc = $request->input('numdoc');

        $datos_beneficiario = [];

        $ps = new ApiSubsidio();
        $ps->send(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'beneficiario',
                'params' => [
                    'documento' => $numdoc,
                ],
            ]
        );
        $out = $ps->toArray();
        if ($out['success'] == true) {
            $datos_beneficiario = $out['data'];
        }
        $mercurio34 = new Mercurio34($datos_beneficiario);

        return response()->json($mercurio34->toArray());
    }

    public function borrar(Request $request)
    {
        try {
            $id = $request->input('id');
            Mercurio34::where('id', $id)->delete();
            $response = [
                'success' => true,
                'msj' => 'Borrado Con Exito',
            ];
            return response()->json($response);
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
                'errors' => $e->render($request)
            ];
        }

        return response()->json($response);
    }

    public function buscarBeneficiarios($estado)
    {
        $documento = $this->user['documento'];
        $tipo = $this->user['tipo'];
        $coddoc = $this->user['coddoc'];

        if (empty($estado)) {
            $mercurio34s = Mercurio34::where('tipo', $tipo)
                ->where('coddoc', $coddoc)
                ->where('documento', $documento)
                ->where('estado', 'IN', ['T', 'D', 'P', 'A', 'X'])
                ->orderBy('id', 'estado', 'desc')
                ->get();
        } else {
            $mercurio34s = Mercurio34::where('tipo', $tipo)
                ->where('coddoc', $coddoc)
                ->where('documento', $documento)
                ->where('estado', $estado)
                ->orderBy('id', 'desc')
                ->get();
        }

        $beneficiarios = [];
        foreach ($mercurio34s as $mercurio34) {
            $rqs = Mercurio10::select(DB::raw('count(mercurio10.numero) as cantidad'))
                ->join('mercurio34', 'mercurio34.id', '=', 'mercurio10.numero')
                ->where('mercurio10.tipopc', $this->tipopc)
                ->where('mercurio34.id', $mercurio34->id)
                ->first();

            $trayecto = Mercurio10::select(DB::raw('max(mercurio10.item), mercurio10.*'))
                ->join('mercurio34', 'mercurio34.id', '=', 'mercurio10.numero')
                ->where('mercurio10.tipopc', $this->tipopc)
                ->where('mercurio34.id', $mercurio34->id)
                ->first();

            $beneficiario = $mercurio34->toArray();
            $beneficiario['cantidad_eventos'] = $rqs->cantidad;
            $beneficiario['fecha_ultima_solicitud'] = $trayecto->fecsis;
            switch ($beneficiario['estado']) {
                case 'T':
                    $beneficiario['estado_detalle'] = 'TEMPORAL';
                    break;
                case 'D':
                    $beneficiario['estado_detalle'] = 'DEVUELTO';
                    break;
                case 'A':
                    $beneficiario['estado_detalle'] = 'APROBADO';
                    break;
                case 'X':
                    $beneficiario['estado_detalle'] = 'RECHAZADO';
                    break;
                case 'P':
                    $beneficiario['estado_detalle'] = 'Pendinete De Validación CAJA';
                    break;
                default:
                    $beneficiario['estado_detalle'] = 'T';
                    break;
            }
            $beneficiarios[] = $beneficiario;
        }

        return $beneficiarios;
    }

    public function cancelarSolicitud(Request $request)
    {
        try {
            $documento = $this->user['documento'];
            $id = $request->input('id');

            $m34 = Mercurio34::where('id', $id)->where('documento', $documento)->first();
            if ($m34) {
                if ($m34->getEstado() != 'T') {
                    Mercurio10::where('numero', $id)->where('tipopc', $this->tipopc)->delete();
                }
                Mercurio34::where('id', $id)->where('documento', $documento)->delete();
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

        return $this->renderObject($salida);
    }

    /**
     * buscarConyugesTrabajador function
     *
     * @return void
     */
    public function buscarConyugesTrabajador(Request $request)
    {
        try {
            $cedtra = $request->input('cedtra');
            $documento = $this->user['documento'];
            $tipo = $this->user['tipo'];
            $procesadorComando = new ApiSubsidio();
            $datos_captura = [];

            // solo conyuges activas a buscar
            if ($tipo == 'T') {
                $trabajador = Mercurio31::where('documento', $documento)->where('estado', 'A')->first();
                $documento = ($trabajador) ? $trabajador->getCedtra() : $documento;

                $procesadorComando->send(
                    [
                        'servicio' => 'ComfacaAfilia',
                        'metodo' => 'listar_conyuges_trabajador',
                        'params' => [
                            'cedtra' => $documento,
                        ],
                    ]
                );

                $out = $procesadorComando->toArray();
                if ($out['success'] == true) {
                    $datos_captura = $out['data'];
                }
            } else {
                $empresa = Mercurio30::where('documento', $documento)->where('estado', 'A')->first();
                $nit = ($empresa) ? $empresa->getNit() : $documento;
                $procesadorComando->send(
                    [
                        'servicio' => 'ComfacaAfilia',
                        'metodo' => 'listar_conyuges',
                        'params' => [
                            'nit' => $nit,
                        ],
                    ]
                );
                $out = $procesadorComando->toArray();
                if ($out['success'] == true) {
                    $datos_captura = $out['data'];
                }
            }

            $_cedcon = [];
            foreach ($datos_captura as $data) {
                if ($cedtra == '') {
                    $_cedcon[$data['cedcon']] = $data['cedcon'] . ' - ' . $data['nombre'];
                } else {
                    if ($cedtra == $data['cedtra']) {
                        $_cedcon[$data['cedcon']] = $data['cedcon'] . ' - ' . $data['nombre'];
                    }
                }
            }

            $conyuguesPendientes = Mercurio32::where('documento', $documento)->whereNotIn('estado', ['I', 'X'])->get();
            foreach ($conyuguesPendientes as $conCp) {
                if (! isset($_cedcon[$conCp->getCedcon()])) {
                    $_cedcon[$conCp->getCedcon()] = $conCp->getCedcon() . ' - ' . $conCp->getPrinom() . ' ' . $conCp->getSegnom() . ' ' . $conCp->getPriape() . ' ' . $conCp->getSegape();
                }
            }

            $html = Tag::selectStatic(
                new Srequest([
                    'cedcon' => $_cedcon,
                    'use_dummy' => true,
                    'dummyValue' => '',
                    'class' => 'form-control'
                ])
            );

            $salida = [
                'success' => true,
                'list' => $html,

            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'list' => '',
                'msj' => $e->getMessage(),
            ];
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

    public function downloadDocs($archivo = '')
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

    public function params(Request $request)
    {
        try {
            $nombre = $this->user['nombre'];
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $listAfiliados = false;
            $listConyuges = false;
            $conyuges = [];
            $cedtras = [];

            $trabajadorService = new TrabajadorService;
            if ($this->tipo == 'E') {
                $cedtras = $trabajadorService->findRequestByDocumentoCoddoc($documento, $coddoc);

                if ($list = $trabajadorService->findApiTrabajadoresByNit($documento)) {
                    $listAfiliados = [];
                    foreach ($list as $row) {
                        $listAfiliados[] = ['cedula' => $row['cedtra'], 'nombre_completo' => $row['nombre']];
                    }
                }
            } else {
                $cedtras[] = ['cedula' => $documento,  'nombre_completo' => $nombre];
            }

            $conyugeService = new ConyugeService;
            if ($this->tipo == 'E') {
                $conyuges[] = $conyugeService->findRequestByDocumentoCoddoc($documento, $coddoc);
                $list = $conyugeService->findApiConyugesByNit($documento);
                $listConyuges = [];
                foreach ($list as $row) {
                    $listConyuges[] = ['cedula' => $row['cedcon'], 'nombre_completo' => $row['nombre']];
                }
            } else {
                $conyuges = $conyugeService->findRequestByCedtra($documento);
            }

            $codzons = Gener09::where('codzon', '>=', 18000)
                ->where('codzon', '<=', 19000)
                ->pluck('detzon', 'codzon')
                ->toArray();

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_beneficiarios',
                    'params' => false
                ]
            );

            $biourbana = ['S' => 'SI', 'N' => 'NO'];
            $biodesco = ['S' => 'SI', 'N' => 'NO'];
            $paramsConyuge = new ParamsBeneficiario;
            $paramsConyuge->setDatosCaptura($procesadorComando->toArray());

            $salida = [
                'success' => true,
                'data' => [
                    'biotipdoc' => ParamsBeneficiario::getTiposDocumentos(),
                    'tipdoc' => ParamsBeneficiario::getTiposDocumentos(),
                    'sexo' => ParamsBeneficiario::getSexos(),
                    'estciv' => ParamsBeneficiario::getEstadoCivil(),
                    'ciunac' => ParamsBeneficiario::getCiudades(),
                    'captra' => ParamsBeneficiario::getCapacidadTrabajar(),
                    'parent' => ParamsBeneficiario::getParentesco(),
                    'huerfano' => ParamsBeneficiario::getHuerfano(),
                    'tiphij' => ParamsBeneficiario::getTipoHijo(),
                    'nivedu' => ParamsBeneficiario::getNivelEducativo(),
                    'tipdis' => ParamsBeneficiario::getTipoDiscapacidad(),
                    'calendario' => ParamsBeneficiario::getCalendario(),
                    'resguardo_id' => ParamsBeneficiario::getResguardos(),
                    'pub_indigena_id' => ParamsBeneficiario::getPueblosIndigenas(),
                    'biocodciu' => ParamsBeneficiario::getCiudades(),
                    'peretn' => ParamsBeneficiario::getPertenenciaEtnicas(),
                    'tippag' => ParamsBeneficiario::getTipoPago(),
                    'codban' => ParamsBeneficiario::getBancos(),
                    'tipcue' => ParamsBeneficiario::getTipoCuenta(),
                    'codzon' => $codzons,
                    'biourbana' => $biourbana,
                    'biodesco' => $biodesco,
                    'trabajadores' => $cedtras,
                    'conyuges' => $conyuges,
                    'list_conyuges' => $listConyuges,
                    'convive' => (new Mercurio34)->getConvive(),
                    'list_afiliados' => $listAfiliados,
                ],
                'msj' => 'OK',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage() . ' ' . $e->getLine() . ' ' . basename($e->getFile()),
                'error' => $e->render($request),
            ];
        }

        return response()->json($salida);
    }

    public function renderTable($estado = '')
    {
        $this->setResponse('view');
        $benService = new BeneficiarioService;
        $html = view(
            'mercurio/beneficiario/tmp/solicitudes',
            [
                'path' => base_path(),
                'beneficiarios' => $benService->findAllByEstado($estado),
            ]
        )->render();

        return $this->renderText($html);
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

            $solicitud = Mercurio34::where('id', $id)
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
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return response()->json($salida);
    }

    public function valida(Request $request)
    {
        $this->setResponse('ajax');
        try {
            $documento = parent::getActUser('documento');
            $coddoc = parent::getActUser('coddoc');

            $numdoc = $request->input('numdoc');
            $solicitud_previa = (new Mercurio34)->findFirst(" numdoc='{$numdoc}' and documento='{$documento}' and coddoc='{$coddoc}'");

            $beneficiario = false;
            if ($solicitud_previa) {
                $beneficiario = $solicitud_previa->getArray();
            }

            if (! $beneficiario) {
                $benefiService = new BeneficiarioService;
                $rqs = $benefiService->buscarBeneficiarioSubsidio($numdoc);
                if ($rqs) {
                    $beneficiario = (count($rqs['data']) > 0) ? $rqs['data'] : false;
                }
            }

            $response = [
                'success' => true,
                'solicitud_previa' => ($solicitud_previa > 0) ? true : false,
                'beneficiario' => $beneficiario,
            ];
        } catch (DebugException $e) {
            $response = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function serializeData(Request $request)
    {
        $fecsol = Carbon::now();
        $asignarFuncionario = new AsignarFuncionario;

        return [
            'usuario' => $asignarFuncionario->asignar($this->tipopc, $this->user['codciu']),
            'log' => '0',
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
        ];
    }

    public function guardar(Request $request)
    {
        $this->db->begin();
        try {
            $benefiService = new BeneficiarioService;
            $id = $request->input('id');
            $clave_certificado = $request->input('clave');
            $params = $this->serializeData($request);
            $params['tipo'] = $this->tipo;
            $params['coddoc'] = $this->user['coddoc'];
            $params['documento'] = $this->user['documento'];

            $solicitud = null;
            if (is_null($id) || $id == '') {
                $solicitud = $benefiService->createByFormData($params);
            } else {
                $res = $benefiService->updateByFormData($id, $params);
                if ($res == false) {
                    throw new DebugException('Error no se actualizo los datos', 301);
                }
                $solicitud = $benefiService->findById($id);
            }

            $beneficiarioAdjuntoService = new BeneficiarioAdjuntoService($solicitud);
            $beneficiarioAdjuntoService->setClaveCertificado($clave_certificado);

            $out = $beneficiarioAdjuntoService->formulario()->getResult();
            (new GuardarArchivoService(
                [
                    'tipopc' => $this->tipopc,
                    'coddoc' => 1,
                    'id' => $solicitud->getId(),
                ]
            ))->salvarDatos($out);

            $out = $beneficiarioAdjuntoService->declaraJurament()->getResult();
            (new GuardarArchivoService(
                [
                    'tipopc' => $this->tipopc,
                    'coddoc' => 4,
                    'id' => $solicitud->getId(),
                ]
            ))->salvarDatos($out);

            $salida = [
                'msj' => 'Proceso se ha completado con éxito',
                'success' => true,
                'data' => $solicitud->getArray(),
            ];

            $this->db->commit();
        } catch (DebugException $erro) {
            $salida = [
                'error' => $erro->getMessage(),
                'success' => false,
            ];
            $this->db->rollBack();
        }

        return response()->json($salida);
    }

    public function consultaDocumentos($id)
    {
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $benService = new BeneficiarioService;

            $sindepe = Mercurio34::where('id', $id)
                ->where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->whereNotIn('estado', ['I', 'X'])
                ->first();

            if ($sindepe == false) {
                throw new DebugException('Error no se puede identificar el propietario de la solicitud', 301);
            }
            $salida = [
                'success' => true,
                'data' => $benService->dataArchivosRequeridos($sindepe),
                'msj' => 'OK',
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return response()->json($salida);
    }

    public function formulario($id)
    {
        try {
            $paramsTrabajador = new ParamsTrabajador;
            $adicionPersonaCargo = true;
            $tipo = $this->user['tipo'];
            $documento = $this->user['documento'];

            $procesadorComando = new ApiSubsidio();
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'parametros_trabajadores',
                ]
            );

            $datos_captura = $procesadorComando->toArray();
            $paramsTrabajador->setDatosCaptura($datos_captura);

            $mercurio34 = Mercurio34::find($id);
            $cedtra = $mercurio34->getCedtra();

            $nit = ($mercurio34->getNit()) ? $mercurio34->getNit() : 0;
            if ($nit == 0 && $tipo == 'E') {
                $nit = $documento;
            }

            // traer primero de sisuweb
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaAfilia',
                    'metodo' => 'trabajador_empresa',
                    'params' => [
                        'cedtra' => $cedtra,
                        'nit' => $nit,
                        'estado' => 'A',
                    ],
                ]
            );

            $mercurio31 = false;
            if ($out = $procesadorComando->toArray()) {
                $datos_trabajador = ($out['success'] == true) ? $out['data'] : null;
                if ($datos_trabajador) {
                    if ($datos_trabajador['nit'] == $nit) {

                        $mercurio31 = new Mercurio31($datos_trabajador);
                        $mercurio31->fecing = $datos_trabajador['fecafi'];
                        $mercurio31->tipafi = $datos_trabajador['tipcot'];
                        $mercurio31->tipdoc = $datos_trabajador['coddoc'];
                    }
                }
            }

            if ($mercurio31 == false) {
                $mercurio31 = Mercurio31::where('documento', $documento)
                    ->where('cedtra', $cedtra)
                    ->where('nit', $nit)
                    ->orderBy('fecsol', 'desc')
                    ->first();
            }

            if ($mercurio31 == false) {
                throw new DebugException('El trabajador no esta correctamente afiliado.', 505);
            }

            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'informacion_empresa',
                    'params' => [
                        'nit' => $mercurio31->getNit(),
                    ],
                ]
            );

            $empresa = false;
            if ($out = $procesadorComando->toArray()) {
                $datos_empresa = ($out['success'] == true) ? $out['data'] : false;
                if ($datos_empresa) {
                    $empresa = new Mercurio30;
                    $datos_empresa['telefono'] = ($datos_empresa['telr'] == '') ? $datos_empresa['telefono'] : $datos_empresa['telr'];
                    $empresa->createAttributes($datos_empresa);
                }
            }

            if (! $empresa) {
                throw new DebugException('Error los datos de la empresa no estan disponibles', 505);
            }

            /* para beneficiarios hijos buscar conyuge */
            $mercurio32 = false;
            if ($mercurio34->getParent() == 1) {
                $mercurio32 = Mercurio32::where('cedtra', $cedtra)
                    ->where('estado', 'not in', ['I', 'X'])
                    ->where('cedcon', $mercurio34->getCedcon())
                    ->first();

                if (! $mercurio32) {

                    $procesadorComando->send(
                        [
                            'servicio' => 'ComfacaAfilia',
                            'metodo' => 'conyugue_trabajador_beneficiario',
                            'params' => [
                                'documento' => $mercurio34->getNumdoc(),
                                'cedtra' => $mercurio34->getCedtra(),
                            ],
                        ]
                    );

                    if ($out = $procesadorComando->toArray()) {

                        $data = ($out['success']) ? $out['data'] : [];
                        $has = 0;
                        foreach ($data as $datos_conyuge) {
                            if ($datos_conyuge['cedcon'] == $mercurio34->getCedcon()) {
                                $has++;
                                break;
                            }
                        }
                        if ($has > 0) {
                            $mercurio32 = new Mercurio32($datos_conyuge);
                            $mercurio32->tipdoc = $datos_conyuge['coddoc'];
                            $mercurio32->ciures = $datos_conyuge['codzon'];
                        }
                    }
                }

                if (! $mercurio32) {

                    $procesadorComando = new ApiSubsidio();
                    $procesadorComando->send(
                        [
                            'servicio' => 'ComfacaAfilia',
                            'metodo' => 'listar_conyuges_trabajador',
                            'params' => [
                                'cedtra' => $mercurio34->getCedtra(),
                            ],
                        ]
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
                                $mercurio32 = new Mercurio32($datos_conyuge);
                                $mercurio32->tipdoc = $datos_conyuge['coddoc'];
                                $mercurio32->ciures = $datos_conyuge['codzon'];
                            }
                        }
                    }
                }
            }

            // buscar mas beneficiarios al formulario
            $beneficiariosTodos = Mercurio34::where('cedtra', $cedtra)
                ->whereIn('estado', ['P', 'D', 'T'])
                ->where('documento', $documento)
                ->get();

            $file = "formulario_afiliacion_acargo{$cedtra}.pdf";
            $formularios = new Formularios;

            $formularios->trabajadorAfiliacion(
                [
                    'trabajador' => $mercurio31,
                    'empresa' => $empresa,
                    'adicionPersonaCargo' => $adicionPersonaCargo,
                    'conyuge' => $mercurio32,
                    'beneficiarios' => $beneficiariosTodos,
                ],
                $file
            )->outFile();
        } catch (DebugException $e) {

            $msj = $e->getMessage() . ' linea: ' . $e->getLine();
            set_flashdata('error', [
                'msj' => $msj,
            ]);

            return redirect('beneficiario.index');
        }
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
                'data' => $mercurio37->toArray(),
            ];
        } catch (DebugException $ert) {
            $response = [
                'success' => false,
                'msj' => $ert->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function seguimiento($id)
    {
        try {
            $beneficiarioService = new BeneficiarioService;
            $out = $beneficiarioService->consultaSeguimiento($id);
            $salida = [
                'success' => true,
                'data' => $out,
            ];
        } catch (DebugException $e) {
            $salida = ['success' => false, 'msj' => $e->getMessage()];
        }

        return response()->json($salida);
    }
}
