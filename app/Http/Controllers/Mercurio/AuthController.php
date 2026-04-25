<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\AuthException;
use App\Exceptions\DebugException;
use App\Http\Controllers\Controller;
use App\Library\Auth\SessionCookies;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Models\Subsi54;
use App\Services\Autentications\AutenticaService;
use App\Services\Autentications\VerifyAuthService;
use App\Services\Signup\SignupEmpresas;
use App\Services\Signup\SignupFacultativos;
use App\Services\Signup\SignupIndependientes;
use App\Services\Signup\SignupPensionados;
use App\Services\Signup\SignupService;
use App\Services\Srequest;
use App\Services\Utils\SenderEmail;
use App\Services\Autentications\AutenticaGeneral;
use App\Services\CajaServices\NotificacionService;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Entidades\EmpresaService;
use App\Services\Entidades\TrabajadorService;
use App\Services\Api\ApiWhatsapp;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    private $db;
    private SignupService $signupService;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->signupService = new SignupService();
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

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Inertia\Response
     */
    public function registerEmpresaAction(Request $request)
    {
        $this->db->begin();
        try {
            $request->validate([
                'rep_nombre' => 'required|string|min:5',
                'rep_documento' => 'required|integer|digits_between:6,18',
                'rep_email' => 'required|email',
                'rep_telefono' => 'required|integer|digits_between:6,10',
                'rep_coddoc' => 'required|string|min:1',
                'tipdoc' => 'required|string|min:1',
                'razsoc' => 'required|string|min:5',
                'nit' => 'required|integer|digits_between:6,18',
                'tipsoc' => 'required|string|min:1',
                'tipper' => 'required|string|min:1',
                'is_delegado' => 'required|boolean',
            ]);

            if ($request->boolean('is_delegado')) {
                $request->validate([
                    'cargo' => 'required|string|min:5',
                ]);
            }

            $data = $request->all();
            $data = array_merge($data, [
                'cedrep' => $request->input('rep_documento'),
                'repleg' => $request->input('rep_nombre'),
                'coddocrepleg' => $request->input('rep_coddoc'),
                'calemp' => 'E',
            ]);

            $response = $this->performRegister($data, 'E');
            $this->db->commit();

            // Usar los datos de la respuesta (documento del representante, no NIT)
            return redirect()->route('verify.show', [
                'tipo' => $response['tipo'],
                'coddoc' => $response['coddoc'],
                'documento' => $response['documento'],
                'option_request' => 'register',
            ])->with('success', $response['msj']);
        } catch (ValidationException $e) {
            $this->db->rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (DebugException $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        } catch (Exception $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => 'Error del servidor'])->withInput();
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Inertia\Response
     */
    public function registerTrabajadorAction(Request $request)
    {
        $this->db->begin();
        try {
            $request->validate([
                'coddoc' => 'required|string|min:1',
                'documento' => 'required|integer|digits_between:6,18',
                'password' => 'required|string|min:8',
                'nombre' => 'required|string|min:5',
                'email' => 'required|email',
                'telefono' => 'required|integer|digits_between:6,10',
                'codciu' => 'required|integer|digits:5',
                'tipo' => 'required|string|min:1',
                'razsoc' => 'required|string|min:5',
                'nit' => 'required|integer|digits_between:6,18',
            ]);

            $data = $request->all();
            $data['calemp'] = null;

            $response = $this->performRegister($data, 'T');
            $this->db->commit();

            return redirect()->route('verify.show', [
                'tipo' => $response['tipo'],
                'coddoc' => $response['coddoc'],
                'documento' => $response['documento'],
                'option_request' => 'register',
            ])->with('success', $response['msj']);
        } catch (ValidationException $e) {
            $this->db->rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (DebugException $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => 'Error del servidor'])->withInput();
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Inertia\Response
     */
    public function registerParticularAction(Request $request)
    {
        $this->db->begin();
        try {
            $request->validate([
                'coddoc' => 'required|string|min:1',
                'documento' => 'required|integer|digits_between:6,18',
                'password' => 'required|string|min:8',
                'nombre' => 'required|string|min:5',
                'email' => 'required|email',
                'telefono' => 'required|integer|digits_between:6,10',
                'codciu' => 'required|integer|digits:5',
                'tipo' => 'required|string|min:1',
            ]);
            $data = $request->all();
            $data['calemp'] = null;

            $response = $this->performRegister($data, 'P');
            $this->db->commit();

            return redirect()->route('verify.show', [
                'tipo' => $response['tipo'],
                'coddoc' => $response['coddoc'],
                'documento' => $response['documento'],
                'option_request' => 'register',
            ])->with('success', $response['msj']);
        } catch (ValidationException $e) {
            $this->db->rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (DebugException $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        } catch (Exception $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => 'Error del servidor'])->withInput();
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Inertia\Response
     */
    public function registerIndependienteAction(Request $request)
    {
        $this->db->begin();
        try {
            $request->validate([
                'coddoc' => 'required|string|min:1',
                'documento' => 'required|integer|digits_between:6,18',
                'password' => 'required|string|min:8',
                'nombre' => 'required|string|min:5',
                'email' => 'required|email',
                'telefono' => 'required|integer|digits_between:6,10',
                'codciu' => 'required|integer|digits:5',
                'tipo' => 'required|string|min:1',
                'contribution_rate' => 'required',
            ]);
            $data = $request->all();
            $data['calemp'] = 'I';

            $response = $this->performRegister($data, 'I');
            $this->db->commit();

            return redirect()->route('verify.show', [
                'tipo' => $response['tipo'],
                'coddoc' => $response['coddoc'],
                'documento' => $response['documento'],
                'option_request' => 'register',
            ])->with('success', $response['msj']);
        } catch (ValidationException $e) {
            $this->db->rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (DebugException $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        } catch (Exception $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => 'Error del servidor'])->withInput();
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Inertia\Response
     */
    public function registerPensionadoAction(Request $request)
    {
        $this->db->begin();
        try {
            $request->validate([
                'coddoc' => 'required|string|min:1',
                'documento' => 'required|integer|digits_between:6,18',
                'password' => 'required|string|min:8',
                'nombre' => 'required|string|min:5',
                'email' => 'required|email',
                'telefono' => 'required|integer|digits_between:6,10',
                'codciu' => 'required|integer|digits:5',
                'tipo' => 'required|string|min:1',
                'contribution_rate' => 'required',
            ]);
            $data = $request->all();
            $data['calemp'] = 'O';

            $response = $this->performRegister($data, 'O');
            $this->db->commit();

            return redirect()->route('verify.show', [
                'tipo' => $response['tipo'],
                'coddoc' => $response['coddoc'],
                'documento' => $response['documento'],
                'option_request' => 'register',
            ])->with('success', $response['msj']);
        } catch (ValidationException $e) {
            $this->db->rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (DebugException $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        } catch (Exception $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => 'Error del servidor'])->withInput();
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Inertia\Response
     */
    public function registerFacultativoAction(Request $request)
    {
        $this->db->begin();
        try {
            $request->validate([
                'coddoc' => 'required|string|min:1',
                'documento' => 'required|integer|digits_between:6,18',
                'password' => 'required|string|min:8',
                'nombre' => 'required|string|min:5',
                'email' => 'required|email',
                'telefono' => 'required|integer|digits_between:6,10',
                'codciu' => 'required|integer|digits:5',
                'tipo' => 'required|string|min:1',
            ]);

            $data = $request->all();
            $data['calemp'] = 'F';

            $response = $this->performRegister($data, 'F');
            $this->db->commit();

            return redirect()->route('verify.show', [
                'tipo' => $response['tipo'],
                'coddoc' => $response['coddoc'],
                'documento' => $response['documento'],
                'option_request' => 'register',
            ])->with('success', $response['msj']);
        } catch (ValidationException $e) {
            $this->db->rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (DebugException $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        } catch (Exception $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => 'Error del servidor'])->withInput();
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Inertia\Response
     */
    public function registerDomesticoAction(Request $request)
    {
        $this->db->begin();
        try {
            $request->validate([
                'coddoc' => 'required|string|min:1',
                'documento' => 'required|integer|digits_between:6,18',
                'password' => 'required|string|min:8',
                'nombre' => 'required|string|min:5',
                'email' => 'required|email',
                'telefono' => 'required|integer|digits_between:6,10',
                'codciu' => 'required|integer|digits:5',
                'tipo' => 'required|string|min:1',
                'contribution_rate' => 'required'
            ]);

            $data = $request->all();
            $data['calemp'] = 'S'; // Para domésticos

            $response = $this->performRegister($data, 'S');
            $this->db->commit();

            return redirect()->route('verify.show', [
                'tipo' => $response['tipo'],
                'coddoc' => $response['coddoc'],
                'documento' => $response['documento'],
                'option_request' => 'register',
            ])->with('success', $response['msj']);
        } catch (ValidationException $e) {
            $this->db->rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (DebugException $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        } catch (Exception $e) {
            $this->db->rollBack();
            return back()->withErrors(['general' => 'Error del servidor'])->withInput();
        }
    }

    private function performRegister($data, ?string $tipo = null)
    {
        switch ($tipo) {
            case 'E':
                $signupEntity = new SignupEmpresas;
                break;
            case 'I':
                $signupEntity = new SignupIndependientes;
                break;
            case 'F':
                $signupEntity = new SignupFacultativos;
                break;
            case 'O':
                $signupEntity = new SignupPensionados;
                break;
            case 'S':
            case 'T':
            case 'P':
                $signupEntity = null;
                break;
            default:
                throw new DebugException('Error el tipo de afiliación es requerido', 1);
                break;
        }

        $response = $this->signupService->execute(
            $signupEntity,
            new Srequest($data)
        );

        return $response;
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

    private function generateAndSendVerificationCode($documento, $coddoc, $tipo, $user07, $deliveryMethod = 'email')
    {
        $codigoVerify = genera_code();
        $inicio = Carbon::now()->format('Y-m-d H:i:s');
        $intentos = '0';

        Mercurio19::updateOrCreate(
            [
                'documento' => $documento,
                'coddoc' => $coddoc,
                'tipo' => $tipo,
            ],
            [
                'inicio' => $inicio,
                'intentos' => (int) $intentos,
                'codver' => (string) $codigoVerify,
            ]
        );

        if ($deliveryMethod == 'email') {
            $html = "Utiliza el siguiente código de verificación, para confirmar el propietario de la dirección de correo:<br/>
            <span style=\"font-size:16px;color:#333\">CÓDIGO DE VERIFICACIÓN: </span><br/>
            <span style=\"font-size:30px;color:#11cdef\"><b>{$codigoVerify}</b></span>";

            $asunto = 'Generación nuevo PIN plataforma Comfaca En Línea';
            $emailCaja = Mercurio01::first();
            $senderEmail = new SenderEmail;
            $senderEmail->setters(
                "emisor_email: {$emailCaja->getEmail()}",
                "emisor_clave: {$emailCaja->getClave()}",
                "asunto: {$asunto}"
            );
            $senderEmail->send($user07->getEmail(), $html);
        } else {
            if (config('app.api_mode') == 'development') $user07->setWhatsapp('3157145942');

            if (! $user07->whatsapp) {
                throw new DebugException('No se proporcionó número de whatsapp', 501);
            }

            $html = "Utiliza el siguiente código de verificación, para confirmar el propietario de la línea de whatsapp:<br/>
            *{$codigoVerify}*. Generación de PIN plataforma Comfaca En Línea, utiliza el código de verificación para confirmar el propietario de la línea de whatsapp.";
            $apiWhatsaap = new ApiWhatsapp;
            $apiWhatsaap->send([
                'servicio' => 'Whatsapp',
                'metodo' => 'enviar',
                'params' => [
                    'numero' => $user07->whatsapp,
                    'mensaje' => $html,
                ],
            ]);
        }

        return $codigoVerify;
    }

    /**
     * Reenviar código de verificación
     * Este método regenera y reenvía el código PIN al usuario
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendVerificationCode(Request $request)
    {
        try {
            $data = $request->validate([
                'documento' => 'required|numeric|digits_between:6,18',
                'coddoc' => 'required|string|min:1',
                'tipo' => 'required|string|size:1',
                'delivery_method' => 'required|string|in:email,whatsapp',
            ]);

            $user07 = Mercurio07::where('documento', $data['documento'])
                ->where('coddoc', $data['coddoc'])
                ->where('tipo', $data['tipo'])
                ->first();

            if (! $user07) {
                return back()->withErrors([
                    'general' => 'No existe un usuario registrado con los datos ingresados.',
                ]);
            }

            $this->generateAndSendVerificationCode(
                $data['documento'],
                $data['coddoc'],
                $data['tipo'],
                $user07,
                $data['delivery_method']
            );

            return back()->with('status', 'verification-link-sent');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'No fue posible reenviar el código. Intenta nuevamente.']);
        }
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

            // Validar existencia del padre en mercurio07 para evitar romper la FK
            $user07 = Mercurio07::where('documento', $payload['documento'])
                ->where('coddoc', $payload['coddoc'])
                ->where('tipo', $payload['tipo'])
                ->first();

            if (! $user07) {
                return Inertia::render('Auth/VerifyEmail', [
                    'documento' => $payload['documento'],
                    'coddoc' => $payload['coddoc'],
                    'tipo' => $payload['tipo'],
                    'option_request' => $payload['option_request'],
                    'error' => 'No existe un usuario registrado con los datos ingresados. Por favor verifique o regístrese.',
                ]);
            }

            $user19 = Mercurio19::where('documento', $payload['documento'])
                ->where('coddoc', $payload['coddoc'])
                ->where('tipo', $payload['tipo'])
                ->first();

            // Solo generar y enviar PIN si no existe un registro previo (caso de registro directo)
            // Si viene de recoverySend, el PIN ya fue generado y enviado
            if (! $user19) {
                $this->generateAndSendVerificationCode($payload['documento'], $payload['coddoc'], $payload['tipo'], $user07);
            }

            return Inertia::render('Auth/VerifyEmail', $payload);
        } catch (\Exception $err) {
            return back()->withErrors([
                'general' => $err->getMessage(),
            ]);
        }
    }

    public function verify(Request $request)
    {
        try {
            $verifyAuthService = new VerifyAuthService();

            $request->validate($verifyAuthService->rules());

            $rqs = $verifyAuthService->execute($request);
            if (!$rqs) {
                $payload = $verifyAuthService->getPayload();

                // Agregar datos necesarios para renderizar la vista
                $payload['documento'] = $request->input('documento');
                $payload['coddoc'] = $request->input('coddoc');
                $payload['tipo'] = $request->input('tipo');
                $payload['option_request'] = $request->input('option_request');

                return Inertia::render('Auth/VerifyEmail', $payload);
            } else {
                // caso de exito
                $url = url($rqs) ?? url('web/auth/login');
                return Inertia::location($url);
            }
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (AuthException | \Exception $e) {
            return back()->withErrors([
                'general' => $e->getMessage(),
            ]);
        }
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

    /**
     * Enviar código de recuperación de contraseña
     * Este método envía un código de recuperación de contraseña al usuario
     * por email o WhatsApp para restablecer su contraseña.
     * @param Request $request
     * @return \Inertia\Response
     */
    public function recoverySend(Request $request)
    {
        try {
            $data = $request->validate([
                'documento' => 'required|numeric|digits_between:6,18',
                'coddoc' => 'required|string|min:1|max:2',
                'tipo' => 'required|string|size:1',
                'delivery_method' => 'required|string|min:5|max:15'
            ]);

            $delivery_method = $data['delivery_method'];
            if ($delivery_method == 'email') {
                //valida email
                $request->validate([
                    'email' => 'required|string|email',
                ]);
                $data['email'] = $request->input('email');
            } else {
                //valida whatsapp
                $request->validate([
                    'whatsapp' => 'required|string|numeric|digits_between:6,18',
                ]);
                $data['whatsapp'] = $request->input('whatsapp');
            }

            $user07 = Mercurio07::where('documento', $data['documento'])
                ->where('coddoc', $data['coddoc'])
                ->where('tipo', $data['tipo'])
                ->first();

            if (! $user07) {
                return back()->withErrors([
                    'general' => 'No existe un usuario registrado con los datos ingresados. Verifique o regístrese para continuar.',
                ]);
            }

            if (
                ($data['tipo'] == 'E' ||
                    $data['tipo'] == 'P' ||
                    $data['tipo'] == 'S')  && ($user07->whatsapp == null || $user07->email == null)
            ) {
                //consulta a la api externa 
                $empresa = (new EmpresaService())->buscarEmpresaSubsidio($user07->documento);
                if ($empresa) {
                    $user07->whatsapp = $empresa['telr'];
                    $user07->email = $empresa['email'];
                    $user07->save();
                }
            }

            if (
                ($data['tipo'] == 'T' ||
                    $data['tipo'] == 'I' ||
                    $data['tipo'] == 'F' ||
                    $data['tipo'] == 'O'
                )  && ($user07->whatsapp == null || $user07->email == null)
            ) {
                //consulta a la api externa 
                $trabajador = (new TrabajadorService())->buscarTrabajadorSubsidio($user07->documento);
                if ($trabajador) {
                    $user07->whatsapp = $trabajador['telefono'];
                    $user07->email = $trabajador['email'];
                    $user07->save();
                }
            }

            //se valida que el email sea igual al que tiene registrado
            if ($delivery_method == 'email' && strtolower($user07->email ?? '')  != strtolower($data['email'] ?? '')) {
                return back()->withErrors([
                    'email' => 'El email ingresado no coincide con el registrado. Verifique o regístrese para continuar.',
                ]);
            }

            //se valida que el whatsapp sea igual al que tiene registrado
            if ($delivery_method == 'whatsapp' && $user07->whatsapp != $data['whatsapp']) {
                return back()->withErrors([
                    'whatsapp' => 'El whatsapp ingresado no coincide con el registrado. Verifique o regístrese para continuar.',
                ]);
            }

            $this->generateAndSendVerificationCode($data['documento'], $data['coddoc'], $data['tipo'], $user07, $delivery_method);

            //cambiar clave de usuario
            Mercurio07::where('documento', $data['documento'])
                ->where('coddoc', $data['coddoc'])
                ->where('tipo', $data['tipo'])
                ->update([
                    'clave' => 'x0x',
                ]);

            // Redirigir a verifyShow con los datos necesarios
            return redirect()->route('verify.show', [
                'tipo' => $data['tipo'],
                'coddoc' => $data['coddoc'],
                'documento' => $data['documento'],
                'option_request' => 'recovery',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (DebugException | \Exception $e) {
            return back()->withErrors([
                'general' => $e->getMessage(),
            ]);
        }
    }
}
