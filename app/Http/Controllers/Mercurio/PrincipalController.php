<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Auth\SessionCookies;
use App\Library\Auth\SessionMercurio;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio16;
use App\Models\Mercurio26;
use App\Models\Mercurio30;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Services\Api\ApiSubsidio;
use App\Services\Entidades\EmpresaService;
use App\Services\Entidades\IndependienteService;
use App\Services\Entidades\ParticularService;
use App\Services\Entidades\TrabajadorService;
use App\Services\PreparaFormularios\GestionFirmaNoImage;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrincipalController extends ApplicationController
{
    protected ?DbBase $db;

    protected ?array $user;

    protected ?string $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    public function index()
    {
        if ($this->user == null) {
            return redirect()->to('mercurio/login');
        }

        return view('mercurio/principal/index', [
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'nombre' => $this->user['nombre'],
        ]);
    }

    public function requireFirma()
    {
        $documento = $this->user['documento'] ?? null;
        $coddoc = $this->user['coddoc'] ?? null;

        $requireFirma = false;
        if ($documento && $coddoc) {
            $mfirma = Mercurio16::whereRaw("documento='{$documento}' AND coddoc='{$coddoc}'")->first();
            // Se considera que tiene firma cuando existe registro y algún recurso asociado (imagen de firma o clave pública)
            if (! $mfirma) {
                $requireFirma = true;
            } else {
                if (empty($mfirma->firma) && empty($mfirma->keypublic)) {
                    $requireFirma = true;
                }
            }
        }

        return response()->json([
            'success' => true,
            'requireFirma' => $requireFirma,
        ]);
    }

    public function requireChangeClave(Request $request)
    {
        try {
            $documento = $this->user['documento'] ?? null;
            $coddoc = $this->user['coddoc'] ?? null;

            $requireChangeClave = false;
            $m07 = Mercurio07::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->where(
                    'tipo',
                    $this->tipo
                );
            if ($m07->clave === 'x0x') {
                $requireChangeClave = true;
            }

            $salida = [
                'success' => true,
                'requireChangeClave' => $requireChangeClave,
            ];

            return response()->json($salida);
        } catch (\Throwable $th) {
            return $this->handleException($th);
        }
    }

    public function dashboardEmpresa()
    {
        return view('mercurio/principal/dashboard_empresa', [
            'title' => 'Dashboard Empresas',
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'nombre' => $this->user['nombre'],
        ]);
    }

    public function dashboardTrabajador()
    {
        return view('principal.dashboard_trabajador', [
            'help' => false,
            'title' => 'Dashboard Trabajadores',
            'hide_header' => true,
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'nombre' => $this->user['nombre'],
        ]);
    }

    public function traerAportesEmpresa(Request $request)
    {
        try {
            $labels = [
                'Enero',
                'Febrero',
                'Marzo',
                'Abril',
                'Mayo',
                'Junio',
                'Julio',
                'Agosto',
                'Septiembre',
                'Octubre',
                'Noviembre',
                'Diciembre',
            ];
            $data = [];

            $ps = new ApiSubsidio;
            $ps->send(
                [
                    'servicio' => 'AportesEmpresas',
                    'metodo' => 'aportes_empresa_mensual',
                    'params' => [
                        'nit' => $this->user['documento'],
                        'vigencia' => date('Y'),
                    ],
                ]
            );

            $out = $ps->toArray();
            $isSuccess = $out['success'] ?? false;

            if (! $isSuccess) {
                return response()->json([
                    'success' => false,
                    'msj' => 'No se pudo traer los aportes',
                    'message' => 'No se pudo traer los aportes',
                    'flag' => false,
                ]);
            }

            foreach ($out['data'] ?? [] as $item) {
                $data[] = $item['valcon'] ?? 0;
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'labels' => $labels,
            ]);
        } catch (DebugException $e) {
            return $e->render($request);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function traerCategoriasEmpresa()
    {
        try {
            $data = [];
            $labels = [];

            $ps = new ApiSubsidio;
            $ps->send(
                [
                    'servicio' => 'PoblacionAfiliada',
                    'metodo' => 'categoria_trabajador_empresa',
                    'params' => [
                        'nit' => $this->user['documento'],
                    ],
                ]
            );
            $out = $ps->toArray();
            $isSuccess = $out['success'] ?? false;

            if (! $isSuccess) {
                return response()->json([
                    'success' => false,
                    'msj' => 'No se pudo traer las categorias',
                    'message' => 'No se pudo traer el giro',
                    'flag' => false,
                ]);
            }

            foreach ($out['data'] as $item) {
                $data[] = $item['cantidad'];
                $labels[] = $item['codcat'];
            }

            $response = [
                'success' => true,
                'data' => $data,
                'labels' => $labels,
            ];

            return response()->json($response);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function traerGiroEmpresa(Request $request)
    {
        try {
            $ps = new ApiSubsidio;
            $ps->send(
                [
                    'servicio' => 'CuotaMonetaria',
                    'metodo' => 'giro_trabajador_empresa',
                    'params' => [
                        'nit' => $this->user['documento'],
                    ],
                ]
            );
            $out = $ps->toArray();
            $isSuccess = $out['success'] ?? false;

            if (! $isSuccess) {
                return response()->json([
                    'success' => false,
                    'msj' => 'No se pudo traer la cuota monetaria',
                    'message' => 'No se pudo traer la cuota monetaria',
                    'flag' => false,
                ]);
            }

            $data = [];
            $labels = [];

            foreach ($out['data'] ?? [] as $item) {
                $periodo = (string) ($item['periodo'] ?? '');
                $monthIndex = strlen($periodo) === 6 ? (int) substr($periodo, 4, 2) : null;
                $labels[] = $monthIndex !== null
                    ? get_mes_name($monthIndex + 1)
                    : $periodo;
                $data[] = $item['valor'] ?? 0;
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'labels' => $labels,
            ]);
        } catch (DebugException $e) {
            return $e->render($request);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function fileExisteGlobal(Request $request, Response $response, string $filepath)
    {
        $archivo = base64_decode($filepath);
        if (preg_match('/(storage)(\/)(temp)/i', $archivo) == false) {
            $fichero = storage_path('temp/'.$archivo);
        } else {
            $fichero = storage_path($archivo);
        }
        if (file_exists($fichero)) {
            return $this->renderObject(['success' => true]);
        } else {
            return $this->renderObject(['success' => false]);
        }
    }

    public function actualizaEstadoSolicitudes()
    {
        try {
            if (get_flashdata_item('Syncron') == true) {
                return response()->json([
                    'success' => true,
                    'msj' => 'Y se realizo la actualización de las solicitudes',
                ]);
            }
            $tipo = $this->tipo;

            $coddoc = $this->user['coddoc'];
            $documento = $this->user['documento'];

            $procesadorComando = new ApiSubsidio;
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'actualiza_empresa_enlinea',
                    'params' => $documento,
                ]
            );
            $out = $procesadorComando->toArray();
            $salida_empresas = $out;

            $procesadorComando = new ApiSubsidio;
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'actualiza_trabajador_enlinea',
                    'params' => $documento,
                ]
            );
            $out = $procesadorComando->toArray();
            $salida_trabajadores = $out;

            $procesadorComando = new ApiSubsidio;
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'actualiza_conyuge_enlinea',
                    'params' => $documento,
                ]
            );
            $out = $procesadorComando->toArray();
            $salida_conyuges = $out;

            $procesadorComando = new ApiSubsidio;
            $procesadorComando->send(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'actualiza_beneficiario_enlinea',
                    'params' => $documento,
                ]
            );
            $out = $procesadorComando->toArray();
            $salida_beneficiarios = $out;

            $hoy = Carbon::now()->format('Y-m-d');
            Mercurio07::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->where('tipo', $tipo)
                ->update(['fecha_syncron' => $hoy]);

            $salida = [
                'success' => true,
                'msj' => 'El proceso de actualización se ha completado con éxito',
                'empresas' => $salida_empresas,
                'trabajadores' => $salida_trabajadores,
                'conyuges' => $salida_conyuges,
                'beneficiarios' => $salida_beneficiarios,
            ];

            set_flashdata('Syncron', true, true);

            return response()->json($salida);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function up()
    {
        $this->setResponse('view');
        get_flashdata_item('Syncron', true);
    }

    public function listaAdress()
    {
        try {
            $adress = $this->db->inQueryAssoc('SELECT * FROM mercurio15 WHERE 1=1');
            $salida = [
                'success' => true,
                'data' => $adress,
                'msj' => 'El proceso de consulta completo con éxito',
            ];
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        return response()->json($salida);
    }

    public function servicios()
    {
        try {
            $mservice = null;
            $tipo = session('tipo');
            switch ($tipo) {
                case 'E':
                    $mservice = new EmpresaService;
                    break;
                case 'P':
                    $mservice = new ParticularService;
                    break;
                case 'I':
                case 'F':
                case 'O':
                    $mservice = new IndependienteService;
                    break;
                case 'T':
                    $mservice = new TrabajadorService;
                    break;
                default:
                    break;
            }

            if (session('estado_afiliado') == 'I') {
                $mservice = new ParticularService;
            }

            if ($mservice) {
                $servicios = $mservice->resumenServicios();
            } else {
                throw new DebugException('El servicio no está disponible.', 501);
            }
            // Totales por estados de afiliación
            $totales = [
                'pendientes' => 0,
                'aprobados' => 0,
                'rechazados' => 0,
                'devueltos' => 0,
                'temporales' => 0,
            ];

            // Sumar sobre la sección 'afiliacion' si existe
            if (isset($servicios['afiliacion']) && is_array($servicios['afiliacion'])) {
                foreach ($servicios['afiliacion'] as $item) {
                    if (isset($item['cantidad']) && is_array($item['cantidad'])) {
                        foreach ($totales as $estado => $valor) {
                            if (isset($item['cantidad'][$estado])) {
                                $totales[$estado] += (int) $item['cantidad'][$estado];
                            }
                        }
                    }
                }
            }

            $salida = [
                'success' => true,
                'msj' => 'Proceso completado con éxito',
                'data' => $servicios,
                'totales' => $totales,
            ];
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        return response()->json($salida);
    }

    public function galeria()
    {
        try {
            $mercurio01 = Mercurio01::first();
            if (! $mercurio01) {
                throw new DebugException('Configuración básica no encontrada.', 404);
            }

            $path = $mercurio01->publicUrl('galeria');
            $galeria = Mercurio26::activas()
                ->soloImagenes()
                ->orderBy('orden')
                ->get();

            $data = $galeria->map(function ($item) use ($path) {
                return [
                    'numero' => $item->numero,
                    'archivo' => $path.'/'.$item->archivo,
                    'tipo' => $item->tipo,
                    'nota' => $item->nota,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'msj' => 'Consulta exitosa',
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function validaSyncro()
    {
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];
            $tipo = $this->tipo;

            $hoy = date('Y-m-d');
            $solicitante = Mercurio07::whereRaw("documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'")->first();
            if ($solicitante->fecha_syncron == '' || is_null($solicitante->fecha_syncron)) {
                $solicitante->fecha_syncron = $hoy;
                $solicitante->save();
            }

            $hoy = Carbon::now();
            $dif = $hoy->diff(Carbon::parse($solicitante->fecha_syncron));
            $interval = $dif->days;
            $salida = [
                'success' => true,
                'msj' => 'Consulta realizada con éxito',
                'data' => [
                    'ultimo_syncron' => Carbon::parse($solicitante->fecha_syncron)->format('d - M - Y'),
                    'syncron' => ($interval >= 10) ? true : false,
                ],
            ];
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        return response()->json($salida);
    }

    public function establecerClaveFirma(Request $request)
    {
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $clave = $request->input('clave');
            if (! preg_match('/^\d{6}$/', (string) $clave)) {
                throw new DebugException('La clave debe ser un número de 6 dígitos.', 422);
            }
            // Validación: no permitir secuencias consecutivas (ascendente o descendente)
            $digits = str_split((string) $clave);
            $inc = true;
            $dec = true;
            for ($i = 1; $i < count($digits); $i++) {
                $prev = intval($digits[$i - 1]);
                $curr = intval($digits[$i]);
                if ($curr - $prev !== 1) {
                    $inc = false;
                }
                if ($curr - $prev !== -1) {
                    $dec = false;
                }
                if (! $inc && ! $dec) {
                    break;
                }
            }
            if ($inc || $dec) {
                throw new DebugException('La clave no puede ser una secuencia consecutiva (ej: 123456 o 654321).', 422);
            }

            $gestionFirmas = new GestionFirmaNoImage(
                [
                    'documento' => $documento,
                    'coddoc' => $coddoc,
                    'password' => $clave,
                ]
            );
            if ($gestionFirmas->hasFirma() == false) {
                $gestionFirmas->guardarFirma();
                $gestionFirmas->generarClaves();
            } else {
                $firma = $gestionFirmas->getFirma();
                if (is_null($firma->getKeypublic()) || is_null($firma->getKeyprivate())) {
                    $gestionFirmas->guardarFirma();
                    $gestionFirmas->generarClaves();
                }
            }

            $salida = [
                'success' => true,
                'msj' => 'La clave fue registrada correctamente.',
                'redirect_url' => $this->resolveTemporalSolicitudRedirect(),
            ];
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        return response()->json($salida);
    }

    private function resolveTemporalSolicitudRedirect(): ?string
    {
        $documento = $this->user['documento'] ?? null;
        $coddoc = $this->user['coddoc'] ?? null;

        if (! $documento || ! $coddoc) {
            return null;
        }

        $solicitud = match ($this->tipo) {
            'E' => Mercurio30::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->where('estado', 'T')
                ->orderByDesc('id')
                ->first(),
            'I' => Mercurio41::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->where('estado', 'T')
                ->orderByDesc('id')
                ->first(),
            'O' => Mercurio38::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->where('estado', 'T')
                ->orderByDesc('id')
                ->first(),
            default => null,
        };

        if (! $solicitud) {
            return null;
        }

        return match ($this->tipo) {
            'E' => url("mercurio/empresa/index#proceso/{$solicitud->id}"),
            'I' => url("mercurio/independiente/index#proceso/{$solicitud->id}"),
            'O' => url("mercurio/pensionado/index#proceso/{$solicitud->id}"),
            default => null,
        };
    }

    public function changeClave(Request $request)
    {
        try {
            $data = $request->validate([
                'clave' => 'required|string|min:10|max:20',
                'documento' => 'required|string',
                'coddoc' => 'required|string',
            ]);
            $documento = $data['documento'];
            $coddoc = $data['coddoc'];
            $clave = $data['clave'];
            $salida = [
                'success' => true,
                'msj' => 'La clave fue registrada correctamente.',
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        return response()->json($salida);
    }

    /**
     * ingresoDirigido function
     * aplica para los particulares que hacen su primer registro al sistema
     *
     * @return void
     */
    public function ingresoDirigido(Request $request)
    {
        try {
            $dataVerify = $request->input('dataVerify');
            $tk = explode('|', base64_decode($dataVerify));

            if (count($tk) !== 2) {
                throw new DebugException('El identificador de la empresa no es correcto', 404);
            }

            $data = Kdecrypt($tk[0], $tk[1]);
            if ($data == false) {
                throw new DebugException('El identificador de la empresa no es correcto', 404);
            }

            $token = json_decode($data);
            if ($token == false || is_null($token) || is_object($token) == false) {
                throw new DebugException('El identificador de la empresa no es correcto', 404);
            }

            if (
                isset($token->documento) == false ||
                isset($token->tipo) == false ||
                isset($token->coddoc) == false ||
                isset($token->tipafi) == false
            ) {
                throw new DebugException('El identificador de la empresa no es correcto', 404);
            }

            $solicitud = false;
            switch ($token->tipafi) {
                case 'E':
                    if ($token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException('El identificador de la empresa no es correcto', 404);
                    }
                    if ($token->id == '' || is_null($token->id)) {
                        $solicitud = (new Mercurio07)->findFirst(" documento='{$token->documento}' and coddoc='{$token->coddoc}' and tipo='{$token->tipo}'");
                        $url = 'mercurio/empresa/index';
                    } else {
                        $solicitud = (new Mercurio30)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                        $url = "mercurio/empresa/index#proceso/{$token->id}";
                    }
                    break;
                case 'I':
                    if ($token->id == '' || $token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException('El identificador de la empresa no es correcto', 404);
                    }
                    $solicitud = (new Mercurio41)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                    $url = "mercurio/independiente/index#proceso/{$token->id}";
                    break;
                case 'O':
                    if ($token->id == '' || $token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException('El identificador de la empresa no es correcto', 404);
                    }
                    $solicitud = (new Mercurio38)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                    $url = "mercurio/pensionado/index#proceso/{$token->id}";
                    break;
                case 'F':
                    if ($token->id == '' || $token->documento == '' || $token->tipo == '' || $token->coddoc == '') {
                        throw new DebugException('El identificador de la empresa no es correcto', 404);
                    }
                    $solicitud = (new Mercurio36)->findFirst(" id='{$token->id}' and documento='{$token->documento}' and coddoc='{$token->coddoc}'");
                    $url = "mercurio/facultativo/index#proceso/{$token->id}";
                    break;
                default:
                    // Ingreso usuario particular
                    $solicitud = (new Mercurio07)->findFirst(" documento='{$token->documento}' and coddoc='{$token->coddoc}' and tipo='{$token->tipo}'");
                    $url = 'mercurio/principal/index';
                    break;
            }

            if ($solicitud == false) {
                throw new DebugException('La identificación de la solicitud no es correcto', 404);
            }

            if (! SessionCookies::authenticate(
                new SessionMercurio,
                [
                    'tipo' => $token->tipo,
                    'coddoc' => $token->coddoc,
                    'documento' => $token->documento,
                    'estado' => 'A',
                    'estado_afiliado' => 'I',
                ]
            )) {
                throw new DebugException('Error en la autenticación del usuario', 501);
            }

            set_flashdata(
                'success',
                [
                    'type' => 'html',
                    'msj' => "<p style='font-size:1rem' class='text-left'>El usuario ha realizado el pre-registro de forma correcta</p>".
                        "<p style='font-size:1rem' class='text-left'>El registro realizado es de tipo \"Particular\", ahora puedes realizar las afiliaciones de modo seguro.<br/>".
                        'Las credenciales de acceso le seran enviadas a la respectiva dirección de correo registrado.<br/></p>',
                ]
            );

            return redirect()->to($url);
        } catch (\Throwable $e) {
            $salida = $this->captureException($e, $request);
            set_flashdata('error', [
                'msj' => $salida['msj'],
                'code' => $e->getCode(),
            ]);

            return redirect()->to('mercurio/login');
        }
    }

    public function estado_actual(Request $request)
    {
        try {
            $tipo = $this->user['tipo'];
            $documento = $this->user['documento'];

            switch ($tipo) {
                case 'T':
                    $procesadorComando = new ApiSubsidio;
                    $procesadorComando->send(
                        [
                            'servicio' => 'ComfacaEmpresas',
                            'metodo' => 'informacion_trabajador',
                            'params' => [
                                'cedtra' => $documento,
                            ],
                        ]
                    );
                    $out = $procesadorComando->toArray();
                    break;
                case 'E':
                case 'I':
                case 'F':
                case 'O':
                    $procesadorComando = new ApiSubsidio;
                    $procesadorComando->send(
                        [
                            'servicio' => 'ComfacaEmpresas',
                            'metodo' => 'informacion_empresa',
                            'params' => [
                                'nit' => $documento,
                            ],
                        ]
                    );
                    $out = $procesadorComando->toArray();
                    break;
                default:
                    $out = false;
                    break;
            }

            $salida = [
                'success' => true,
                'msj' => 'Proceso completado con éxito',
                'data' => $out,
            ];
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        return response()->json($salida);
    }

    public function cambioClave(Request $request)
    {
        $this->db->begin();
        try {
            $clave = $request->input('clave');
            $clacon = $request->input('clacon');
            $tipo = $this->tipo;
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            if ($clave != $clacon) {
                throw new DebugException('Las contraseñas no coinciden', 501);
            }

            $msubsi07 = Mercurio07::where('documento', $documento)
                ->where('tipo', $tipo)
                ->where('coddoc', $coddoc)
                ->first();

            if (strlen($clave) > 5 && strlen($clave) < 80) {
                $hash = clave_hash($clave);
                $msubsi07->clave = $hash;
            }

            $msubsi07->save();
            $salida = [
                'msj' => 'Proceso se ha completado con éxito',
                'success' => true,
            ];
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();

            return $this->handleException($e);
        }

        return response()->json($salida);
    }
}
