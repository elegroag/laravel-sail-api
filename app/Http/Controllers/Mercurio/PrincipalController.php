<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Auth\SessionCookies;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio07;
use App\Models\Mercurio16;
use App\Models\Mercurio30;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;
use App\Services\Autentications\AutenticaEmpresa;
use App\Services\Autentications\AutenticaIndependiente;
use App\Services\Autentications\AutenticaParticular;
use App\Services\Autentications\AutenticaPensionado;
use App\Services\Autentications\AutenticaTrabajador;
use App\Services\Entidades\EmpresaService;
use App\Services\Entidades\IndependienteService;
use App\Services\Entidades\ParticularService;
use App\Services\Entidades\TrabajadorService;
use App\Services\PreparaFormularios\GestionFirmaNoImage;
use App\Services\Srequest;
use App\Services\Utils\Comman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrincipalController extends ApplicationController
{
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
        if ($this->user == null) {
            return redirect()->route('login');
        }

        return view('mercurio/principal/index', [
            'tipo' => $this->tipo,
            'documento' => $this->user['documento'],
            'nombre' => $this->user['nombre']
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
            if (!$mfirma) {
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

    public function requireChangeClave()
    {
        $documento = $this->user['documento'] ?? null;
        $coddoc = $this->user['coddoc'] ?? null;

        $requireChangeClave = false;
        $m07 = Mercurio07::where("documento", $documento)
            ->where("coddoc", $coddoc)
            ->where(
                "tipo",
                $this->tipo
            );
        if ($m07->clave === 'x0x') {
            $requireChangeClave = true;
        }

        return response()->json([
            'success' => true,
            'requireChangeClave' => $requireChangeClave
        ]);
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

    public function traerAportesEmpresa()
    {
        try {
            $response['labels'] = [
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

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'AportesEmpresas',
                    'metodo' => 'aportes_empresa_mensual',
                    'params' => [
                        'nit' => $this->user['documento'],
                        'vigencia' => date('Y'),
                    ],
                ]
            );

            $subsi11 = $ps->toArray();
            foreach ($subsi11['data'] as $msubsi11) {
                $data[] = $msubsi11['valcon'];
            }

            $response['data'] = $data;
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function traerCategoriasEmpresa()
    {
        try {
            $data = [];
            $labels = [];

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'PoblacionAfiliada',
                    'metodo' => 'categoria_trabajador_empresa',
                    'params' => [
                        'nit' => $this->user['documento'],
                    ],
                ]
            );
            $subsi11 = $ps->toArray();
            if (! $subsi11['success']) {
                return $this->renderObject([
                    'success' => false,
                    'msj' => 'No se pudo traer las categorias',
                ]);
            }

            foreach ($subsi11['data'] as $msubsi11) {
                $data[] = $msubsi11['cantidad'];
                $labels[] = $msubsi11['codcat'];
            }

            $response = [
                'success' => true,
                'data' => $data,
                'labels' => $labels,
            ];
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function traerGiroEmpresa()
    {
        try {
            $this->setResponse('ajax');
            $data = [];
            $response['labels'] = [
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

            $ps = Comman::Api();
            $ps->runCli(
                [
                    'servicio' => 'CuotaMonetaria',
                    'metodo' => 'giro_trabajador_empresa',
                    'params' => [
                        'nit' => $this->user['documento'],
                    ],
                ]
            );
            $subsi09 = $ps->toArray();
            if (! $subsi09['success']) {
                return $this->renderObject([
                    'success' => false,
                    'msj' => 'No se pudo traer el giro',
                ]);
            }

            foreach ($subsi09['data'] as $msubsi09) {
                $data[] = $msubsi09['valor'];
            }
            $response['data'] = $data;
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }

        return $this->renderObject($response, false);
    }

    public function fileExisteGlobal(Request $request, Response $response, string $filepath)
    {
        $archivo = base64_decode($filepath);
        if (preg_match('/(storage)(\/)(temp)/i', $archivo) == false) {
            $fichero = storage_path('temp/' . $archivo);
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
            $this->setResponse('ajax');

            if (get_flashdata_item('Syncron') == true) {
                return $this->renderObject([
                    'success' => true,
                    'msj' => 'Y se realizo la actualización de las solicitudes',
                ], false);
            }
            $tipo = $this->tipo;

            $coddoc = $this->user['coddoc'];
            $documento = $this->user['documento'];

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'actualiza_empresa_enlinea',
                    'params' => $documento,
                ]
            );
            $out = $procesadorComando->toArray();
            $salida_empresas = $out;

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'actualiza_trabajador_enlinea',
                    'params' => $documento,
                ]
            );
            $out = $procesadorComando->toArray();
            $salida_trabajadores = $out;

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
                [
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => 'actualiza_conyuge_enlinea',
                    'params' => $documento,
                ]
            );
            $out = $procesadorComando->toArray();
            $salida_conyuges = $out;

            $procesadorComando = Comman::Api();
            $procesadorComando->runCli(
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
        } catch (DebugException $tf) {
            $salida = [
                'success' => false,
                'msj' => $tf->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    public function up()
    {
        $this->setResponse('view');
        get_flashdata_item('Syncron', true);
    }

    public function listaAdress()
    {
        try {
            $this->setResponse('ajax');
            $adress = $this->db->inQueryAssoc('SELECT * FROM mercurio15 WHERE 1=1');
            $salida = [
                'success' => true,
                'data' => $adress,
                'msj' => 'El proceso de consulta completo con éxito',
            ];
        } catch (DebugException $tf) {
            $salida = [
                'success' => false,
                'msj' => $tf->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }

    public function servicios()
    {
        $this->setResponse('ajax');
        try {
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

            $servicios = $mservice->resumenServicios();
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
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    public function validaSyncro()
    {
        $this->setResponse('ajax');

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
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida, false);
    }

    public function establecerClaveFirma(Request $request)
    {
        try {
            $documento = $this->user['documento'];
            $coddoc = $this->user['coddoc'];

            $clave = $request->input('clave');
            if (!preg_match('/^\d{6}$/', (string)$clave)) {
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
                if (!$inc && !$dec) {
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
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return response()->json($salida);
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
            ];
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return response()->json($salida);
    }

    /**
     * ingresoDirigido function
     * aplica para los particulares que hacen su primer registro al sistema
     *
     * @param  string  $id
     * @param  string  $documento
     * @param  string  $coddoc
     * @param  string  $calemp
     * @return void
     */
    public function ingresoDirigido(Request $request)
    {
        $this->setResponse('view');
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
                'mercurio',
                new Srequest(
                    [
                        'tipo' => $token->tipo,
                        'coddoc' => $token->coddoc,
                        'documento' => $token->documento,
                        'estado' => 'A',
                        'estado_afiliado' => 'I',
                    ]
                )
            )) {
                throw new DebugException('Error en la autenticación del usuario', 501);
            }

            set_flashdata(
                'success',
                [
                    'type' => 'html',
                    'msj' => "<p style='font-size:1rem' class='text-left'>El usuario ha realizado el pre-registro de forma correcta</p>" .
                        "<p style='font-size:1rem' class='text-left'>El registro realizado es de tipo \"Particular\", ahora puedes realizar las afiliaciones de modo seguro.<br/>" .
                        'Las credenciales de acceso le seran enviadas a la respectiva dirección de correo registrado.<br/></p>',
                ]
            );

            return redirect()->to($url);
        } catch (DebugException $e) {
            set_flashdata('error', ['msj' => $e->getMessage()]);

            return redirect()->to('mercurio/login');
        }
    }

    public function estado_actual()
    {
        $this->setResponse('ajax');

        try {
            $tipo = $this->user['tipo'];
            $documento = $this->user['documento'];

            switch ($tipo) {
                case 'T':
                    $procesadorComando = Comman::Api();
                    $procesadorComando->runCli(
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
                    $procesadorComando = Comman::Api();
                    $procesadorComando->runCli(
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
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }

        return $this->renderObject($salida);
    }
}
