<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AuthException;
use App\Exceptions\DebugException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Http\Resources\ErrorResource;
use App\Library\Auth\AuthJwt;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Services\Api\ApiWhatsapp;
use App\Services\Autentications\VerifyAuthService;
use App\Services\Signup\SignupEmpresas;
use App\Services\Signup\SignupFacultativos;
use App\Services\Signup\SignupIndependientes;
use App\Services\Signup\SignupPensionados;
use App\Services\Signup\SignupService;
use App\Services\Srequest;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Services\Entidades\EmpresaService;
use App\Services\Entidades\TrabajadorService;
use Illuminate\Http\JsonResponse;


class AuthMercurioController extends Controller
{
    private $db;
    private SignupService $signupService;
    private AuthJwt $authJwt;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->signupService = new SignupService();
        $this->authJwt = new AuthJwt(700);
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */



    public function registerEmpresaAction(Request $request): JsonResponse
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

            return $this->performRegister($data, 'E');
        } catch (ValidationException $e) {
            return ErrorResource::validationError($e->errors(), 'Error de validación')
                ->response()
                ->setStatusCode(422);
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ErrorResource::serverError($e->getMessage(), $e->getTrace())
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */



    public function registerTrabajadorAction(Request $request): JsonResponse
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

            return $this->performRegister($data, 'T');
        } catch (ValidationException $e) {
            return ErrorResource::validationError($e->errors(), 'Error de validación')
                ->response()
                ->setStatusCode(422);
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ErrorResource::serverError($e->getMessage(), $e->getTrace())
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */



    public function registerParticularAction(Request $request): JsonResponse
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

            return $this->performRegister($data, 'P');
        } catch (ValidationException $e) {
            return ErrorResource::validationError($e->errors(), 'Error de validación')
                ->response()
                ->setStatusCode(422);
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ErrorResource::serverError($e->getMessage(), $e->getTrace())
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */



    public function registerIndependienteAction(Request $request): JsonResponse
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

            return $this->performRegister($data, 'I');
        } catch (ValidationException $e) {
            return ErrorResource::validationError($e->errors(), 'Error de validación')
                ->response()
                ->setStatusCode(422);
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ErrorResource::serverError($e->getMessage(), $e->getTrace())
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */



    public function registerPensionadoAction(Request $request): JsonResponse
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

            return $this->performRegister($data, 'O');
        } catch (ValidationException $e) {
            return ErrorResource::validationError($e->errors(), 'Error de validación')
                ->response()
                ->setStatusCode(422);
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ErrorResource::serverError($e->getMessage(), $e->getTrace())
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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

            return $this->performRegister($data, 'F');
        } catch (ValidationException $e) {
            return ErrorResource::validationError($e->errors(), 'Error de validación')
                ->response()
                ->setStatusCode(422);
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ErrorResource::serverError($e->getMessage(), $e->getTrace())
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Registrar nuevo usuario en el sistema
     * Este endpoint permite el registro de nuevos usuarios en el sistema CLISISU.
     * Soporta diferentes tipos de usuarios: empresas, trabajadores, independientes, etc.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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

            return $this->performRegister($data, 'S');
        } catch (ValidationException $e) {
            return ErrorResource::validationError($e->errors(), 'Error de validación')
                ->response()
                ->setStatusCode(422);
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ErrorResource::serverError($e->getMessage(), $e->getTrace())
                ->response()
                ->setStatusCode(500);
        }
    }

    private function performRegister($data, ?string $tipo = null)
    {
        try {
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

            $this->db->commit();

            return ApiResource::success($response, 'Proceso de registro completado exitosamente')->response();
        } catch (DebugException $e) {
            $this->db->rollBack();

            return ErrorResource::serverError($e->getMessage(), $e->getTrace())
                ->response()
                ->setStatusCode(500);
        }
    }

    /**
     * Autenticar usuario en el sistema
     * 
     * Este endpoint permite la autenticación de usuarios existentes en el sistema CLISISU.
     * Retorna un token JWT para las solicitudes subsequentes.
     * 
     * **Ejemplo de uso:**
     * ```bash
     * curl -X POST http://localhost:8000/api/authenticate \
     *   -H "Content-Type: application/json" \
     *   -d '{
     *     "documento": 123456789,
     *     "password": "password123"
     *   }'
     * ```
     * 
     * **Respuesta exitosa:**
     * ```json
     * {
     *   "success": true,
     *   "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *   "user": {
     *     "documento": 123456789,
     *     "nombre": "Juan Pérez",
     *     "email": "juan.perez@ejemplo.com"
     *   }
     * }
     * ```
     * 
     * **Error de credenciales:**
     * ```json
     * {
     *   "success": false,
     *   "message": "Credenciales inválidas"
     * }
     * ```
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */



    public function authenticateAction(Request $request)
    {
        // Implementación de autenticación
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado aún'
        ], 501);
    }

    /**
     * Verificar y enviar código de verificación
     * 
     * Este endpoint verifica los datos del usuario y envía un código de verificación
     * por email o WhatsApp para confirmar la identidad.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */



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
                return ApiResource::success(['url' => $url], "Código enviado exitosamente");
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
        return response()->json($payload);
    }

    /**
     * Enviar código de recuperación de contraseña
     * 
     * Este endpoint envía un código de recuperación de contraseña al usuario
     * por email o WhatsApp para restablecer su contraseña.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
                return response()->json([
                    'success' => false,
                    'msj' => 'No existe un usuario registrado con los datos ingresados. Verifique o regístrese para continuar.',
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
                return response()->json([
                    'success' => false,
                    'msj' => 'El email ingresado no coincide con el registrado. Verifique o regístrese para continuar.',
                ]);
            }

            //se valida que el whatsapp sea igual al que tiene registrado
            if ($delivery_method == 'whatsapp' && $user07->whatsapp != $data['whatsapp']) {
                return response()->json([
                    'success' => false,
                    'msj' => 'El whatsapp ingresado no coincide con el registrado. Verifique o regístrese para continuar.',
                ]);
            }

            $claims = [
                'documento' => $request->input('documento'),
                'coddoc' => $request->input('coddoc'),
                'tipo' => $request->input('tipo'),
                'delivery_method' => $request->input('delivery_method'),
                'context' => 'recoverySend',
            ];
            $token = (new AuthJwt(430))->SimpleToken($claims);

            $codigoVerify = genera_code();
            $inicio = Carbon::now()->format('Y-m-d H:i:s');
            $intentos = '0';

            Mercurio19::where('documento', $data['documento'])
                ->where('coddoc', $data['coddoc'])
                ->where('tipo', $data['tipo'])
                ->update([
                    'inicio' => $inicio,
                    'intentos' => (int) $intentos,
                    'codver' => (string) $codigoVerify,
                ]);

            if ($request->input('delivery_method') == 'email') {

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
                $senderEmail->send($user07->email, $html);
            } else {
                if (env('API_MODE') == 'development') $user07->setWhatsapp('3157145942');

                if (! $user07->whatsapp) {
                    throw new DebugException('No se proporcionó número de whatsapp', 501);
                }

                $html = "> Código de verificación:
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

            //cambiar clave de usuario
            Mercurio07::where('documento', $data['documento'])
                ->where('coddoc', $data['coddoc'])
                ->where('tipo', $data['tipo'])
                ->update([
                    'clave' => 'x0x',
                ]);

            $salida = [
                'success' => true,
                'message' => 'Código de verificación enviado correctamente',
                'token' => $token,
                'data' => [
                    'documento' => $data['documento'],
                    'coddoc' => $data['coddoc'],
                    'tipo' => $data['tipo'],
                ],
            ];
            $httpCode = 201;
        } catch (ValidationException $e) {
            $salida = [
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ];
            $httpCode = 422;
        } catch (DebugException $e) {
            $salida = [
                'success' => false,
                'message' => 'Error de verificación de datos: ' . $e->getMessage(),
            ];
            $httpCode = 500;
        } catch (\Exception $e) {
            $salida = [
                'success' => false,
                'message' => 'Error de sistema: ' . $e->getMessage(),
            ];
            $httpCode = 500;
        }
        return response()->json($salida, $httpCode);
    }
}
