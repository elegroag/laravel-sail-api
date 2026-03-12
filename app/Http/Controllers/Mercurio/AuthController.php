<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\AuthException;
use App\Exceptions\DebugException;
use App\Http\Controllers\Controller;
use App\Library\Auth\AuthJwt;
use App\Library\Auth\SessionCookies;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Models\Subsi54;
use App\Services\Autentications\AutenticaService;
use App\Services\Autentications\VerifyAuthService;
use App\Services\Srequest;
use App\Services\Utils\SenderEmail;
use App\Services\Autentications\AutenticaGeneral;
use App\Services\CajaServices\NotificacionService;
use App\Services\Utils\AsignarFuncionario;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    private AuthJwt $authJwt;

    public function __construct()
    {
        $this->authJwt = new AuthJwt(700);
    }

    public function index()
    {
        return Inertia::render('Auth/Login');
    }

    public function register()
    {
        return Inertia::render('Auth/Register', (new AutenticaGeneral)->paramsAuthentication());
    }

    public function registerCompany()
    {
        return Inertia::render('Auth/RegisterCompany', (new AutenticaGeneral)->paramsAuthentication());
    }

    public function registerWorker()
    {
        return Inertia::render('Auth/RegisterWorker', (new AutenticaGeneral)->paramsAuthentication());
    }

    public function resetPassword()
    {
        $coddoc = [];
        foreach (Gener18::all() as $entity) {
            if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') {
                continue;
            }
            $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
        }
        return Inertia::render('Auth/ResetPassword', [
            'Coddoc' => $coddoc,
        ]);
    }

    /**
     * Autenticación vía WEB: valida credenciales, crea sesión Laravel y redirige
     */
    public function authenticate(Request $request)
    {
        try {
            try {
                $request->validate([
                    'documentType' => 'required|string|min:1',
                    'identification' => 'required|integer|digits_between:6,18',
                    'password' => 'required|string|min:8',
                    'tipo' => 'required|string|min:1',
                ]);

                $service = new AutenticaService;
                [$access, $message] = $service->execute(
                    new Srequest([
                        'coddoc' => $request->input('documentType'),
                        'documento' => $request->input('identification'),
                        'clave' => $request->input('password'),
                        'tipo' => $request->input('tipo'),
                    ])
                );

                if (! $access) {
                    throw new DebugException($message);
                }

                // Redirección según tipo
                switch ($request->input('tipo')) {
                    case 'T':
                        $url = 'mercurio/principal/index';
                        break;
                    case 'E':
                        $url = 'mercurio/empresa/index';
                        break;
                    case 'I':
                        $url = 'mercurio/independiente/index';
                        break;
                    case 'O':
                        $url = 'mercurio/pensionado/index';
                        break;
                    case 'F':
                        $url = 'mercurio/facultativo/index';
                        break;
                    default:
                        $url = 'mercurio/principal/index';
                        break;
                }

                return Inertia::location(url($url));
            } catch (ValidationException $e) {
                throw new DebugException($e->getMessage());
            }
        } catch (DebugException $e) {
            return Inertia::render('Auth/Login', [
                'success' => false,
                'errors' => [
                    'message' => $e->getMessage()
                ],
                'tracer' => $e->render($request)
            ]);
        }
    }

    public function paramsLogin()
    {
        $tipsoc = Subsi54::pluck('detalle', 'tipsoc')->toArray();

        $coddoc = Gener18::whereNotIn('coddoc', ['7', '2'])->pluck('detdoc', 'coddoc')->toArray();

        $detadoc = Gener18::whereNotIn('codrua', ['TI', 'RC'])->pluck('detdoc', 'codrua')->toArray();

        $codciu = Gener09::whereBetween('codzon', [18000, 19000])->pluck('detzon', 'codzon')->toArray();

        return response()->json([
            'Coddoc' => $coddoc,
            'Tipsoc' => $tipsoc,
            'Codciu' => $codciu,
            'Detadoc' => $detadoc,
        ]);
    }

    public function verifyShow(Request $request, $tipo = null, $coddoc = null, $documento = null, $option_request = null)
    {
        try {
            if ($request->isMethod('post')) {
                $request->validate([
                    'documento' => 'required|numeric|digits_between:6,18',
                    'coddoc' => 'required|numeric|min:1',
                    'tipo' => 'required|string|size:1',
                    'option_request' => 'required|string|size:1',
                ]);
                $payload = [
                    'documento' => $request->input('documento'),
                    'coddoc' => $request->input('coddoc'),
                    'tipo' => $request->input('tipo'),
                    'option_request' => $request->input('option_request'),
                ];
            } else {
                $payload = [
                    'documento' => $documento,
                    'coddoc' => $coddoc,
                    'tipo' => $tipo,
                    'option_request' => $option_request,
                ];
            }

            // Claims básicos a transportar en el token temporal
            $claims = [
                'documento' => $payload['documento'],
                'coddoc' => $payload['coddoc'],
                'tipo' => $payload['tipo'],
                'context' => 'verify',
            ];

            $token = $this->authJwt->SimpleToken($claims);
            // Validar existencia del padre en mercurio07 para evitar romper la FK
            $user07 = Mercurio07::where('documento', $payload['documento'])
                ->where('coddoc', $payload['coddoc'])
                ->where('tipo', $payload['tipo'])
                ->first();

            $user19 = Mercurio19::where('documento', $payload['documento'])
                ->where('coddoc', $payload['coddoc'])
                ->where('tipo', $payload['tipo'])
                ->first();

            $codigoVerify = genera_code();
            if ($user19) {
                $user19->update([
                    'token' => $token,
                ]);
            } else {
                // Si no existe el usuario padre en mercurio07, no crear mercurio19 para no violar la FK
                if (! $user07) {
                    return Inertia::render('Auth/VerifyEmail', [
                        'documento' => $payload['documento'],
                        'coddoc' => $payload['coddoc'],
                        'tipo' => $payload['tipo'],
                        'option_request' => $payload['option_request'],
                        'token' => $token,
                        'error' => 'No existe un usuario registrado con los datos ingresados. Por favor verifique o regístrese.',
                    ]);
                }
                $user19 = Mercurio19::create([
                    'tipo' => $payload['tipo'],
                    'coddoc' => $payload['coddoc'],
                    'documento' => $payload['documento'],
                    'codigo' => '1',
                    'codver' => $codigoVerify,
                    'respuesta' => '',
                    'inicio' => Carbon::now()->format('Y-m-d H:i:s'),
                    'intentos' => '0',
                    'token' => $token,
                ]);
            }

            $payload['token'] = $token;

            return Inertia::render('Auth/VerifyEmail', $payload);
        } catch (\Exception $err) {
            return Inertia::render('Auth/VerifyEmail', [
                'error' => $err->getMessage(),
            ]);
        }
    }

    public function verify(Request $request)
    {
        $payload = [];
        try {
            $verifyAuthService = new VerifyAuthService();

            $request->validate($verifyAuthService->rules());

            $this->authJwt->CheckSimpleToken($request->input('token'));

            $rqs = $verifyAuthService->execute($request);
            if (!$rqs) {
                $payload = $verifyAuthService->getPayload();

                $token = $this->authJwt->SimpleToken(
                    [
                        'documento' => $request->input('documento'),
                        'coddoc' => $request->input('coddoc'),
                        'tipo' => $request->input('tipo'),
                        'context' => 'verify.retry',
                    ]
                );
                $payload['token'] = $token;

                Mercurio19::where('documento', $request->input('documento'))
                    ->where('coddoc', $request->input('coddoc'))
                    ->where('tipo', $request->input('tipo'))
                    ->update(['token' => (string) $token]);
            } else {
                // caso de exito
                $url = url($rqs) ?? url('web/auth/login');
                return Inertia::location($url);
            }
        } catch (AuthException $e) {
            $payload = [
                'success' => false,
                'message' => 'Error de autenticación: ' . $e->getMessage(),
                'errors' => [
                    $e->getMessage()
                ],
            ];
        } catch (ValidationException $e) {
            $payload = [
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ];
        }
        return Inertia::render('Auth/VerifyEmail', $payload);
    }

    public function loadSession(Request $request)
    {
        $request->validate([
            'documento' => 'required|numeric|digits_between:6,18',
            'coddoc' => 'required|string|min:1|max:2',
            'tipo' => 'required|string|size:1',
        ]);

        $tipo = $request->input('tipo');
        switch ($tipo) {
            case 'T':
                $url = 'mercurio/principal/index';
                break;
            case 'E':
                $url = 'mercurio/empresa/index';
                break;
            case 'I':
                $url = 'mercurio/independiente/index';
                break;
            case 'O':
                $url = 'mercurio/pensionado/index';
                break;
            case 'F':
                $url = 'mercurio/facultativo/index';
                break;
            default:
                $url = 'mercurio/principal/index';
                break;
        }

        return Inertia::location(url($url));
    }

    public function notyCambioCorreo()
    {
        return Inertia::render('Auth/NotyEmail');
    }

    public function cambioCorreo(Request $request)
    {
        try {
            $documento = $request->input('documento');
            $coddoc = $request->input('coddoc');
            $tipo = $request->input('tipo');
            $email = $request->input('email');
            $telefono = $request->input('telefono');
            $novedad = $request->input('novedad');

            $notificacion = new NotificacionService();

            $user07 = Mercurio07::whereRaw("documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'")->first();
            if (! $user07) {
                throw new DebugException('Error los parametros de acceso no son validos para solicitar token', 301);
            }

            $emailCaja = Mercurio01::first();
            $html = "Se requiere de actualizar el correo electronico de la cuenta de usuario {$user07->getNombre()}<br/>
            Correo electronico anterior: {$user07->getEmail()}<br/>
            Correo electronico nuevo: {$email}<br/>
            Novedad: {$novedad}<br/>
            Telefono: {$telefono}<br/>";

            if ($tipo == 'T') {
                $to_email = 'afiliacionyregistro@comfaca.com';
                $funcionario = (new AsignarFuncionario)->asignar('1', $user07->getCodciu());
            } else {
                $to_email = 'afiliacionempresas@comfaca.com';
                $funcionario = (new AsignarFuncionario)->asignar('2', $user07->getCodciu());
            }

            $notificacion->createNotificacion(
                [
                    'titulo' => 'Solicitud de cambio de correo',
                    'descripcion' => $html,
                    'user' => $funcionario,
                ]
            );

            $array_tipo = [
                'T' => 'Trabajador',
                'P' => 'Particular',
                'O' => 'Pensionado',
                'F' => 'Facultativo',
                'I' => 'Independiente',
                'E' => 'Empleador',
                'S' => 'Servicio domestico',
            ];
            $str_tipo = $array_tipo[$tipo];
            $asunto = "Solicitud de cambio de correo {$str_tipo} Documento: {$documento}";

            $senderEmail = new SenderEmail;
            $senderEmail->setters(
                "emisor_email: {$emailCaja->getEmail()}",
                "emisor_clave: {$emailCaja->getClave()}",
                "asunto: {$asunto}"
            );

            $senderEmail->send(
                [
                    $to_email,
                ],
                $html
            );

            $salida = [
                'success' => true,
                'msj' => 'Se ha enviado la solicitud de cambio de correo, pronto se contactara con usted para confirmar el cambio. ' .
                    'Este proceso puede tardar ya que se requiere de la confirmación de la persona que solicita el cambio por seguridad de la informacion.',
            ];
        } catch (DebugException $e) {
            $salida = $e->render($request);
        }

        return response()->json($salida);
    }

    public function fueraServicio()
    {
        return Inertia::render('Auth/FueraServicio', [
            'msj' => 'El sistema se encuentra en estado de actualización y mantenimiento.<br/>
            Con el fin de corregir errores y actualizar a versiones más seguras y óptimas que buscan la satisfacción de sus usuarios.</br>',
        ]);
    }

    public function logout()
    {
        SessionCookies::destroyIdentity();
        return redirect()->to('web/login');
    }
}
