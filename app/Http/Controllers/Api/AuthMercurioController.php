<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DebugException;
use App\Http\Controllers\Controller;
use App\Library\Auth\AuthJwt;
use App\Models\Adapter\DbBase;
use App\Models\Gener02;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Services\Api\ApiWhatsapp;
use App\Services\Signup\SignupDomestico;
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

class AuthMercurioController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
    }

    public function registerAction(Request $request)
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
                'first_name' => 'required|string|min:3',
                'last_name' => 'required|string|min:3',
                'tipo' => 'required|string|min:1',
            ]);

            $data = $request->all();
            $data['calemp'] = calemp_use_tipo_value($request->input('selected_user_type'));

            $esDelegado = $request->boolean('is_delegado');

            if ($esDelegado) {
                $data['cedrep'] = $request->input('rep_documento');
                $data['repleg'] = $request->input('rep_nombre');
                $data['coddocrepleg'] = $request->input('rep_coddoc');
            } else {
                $data['cedrep'] = $request->input('documento');
                $data['repleg'] = $request->input('nombre');
                $data['coddocrepleg'] = $request->input('coddoc');
            }

            if (
                $request->input('tipo') == 'I' ||
                $request->input('tipo') == 'O' ||
                $request->input('tipo') == 'S'
            ) {
                $request->validate([
                    'contribution_rate' => 'required',
                ]);
            }

            if ($request->input('tipo') == 'T') {
                $request->validate([
                    'razsoc' => 'required|string|min:5',
                    'nit' => 'required|integer|digits_between:6,18',
                ]);
            }

            switch ($request->input('tipo')) {
                case 'E':
                    if ($request->input('is_delegado')) {
                        $request->validate([
                            'rep_nombre' => 'required|string|min:5',
                            'rep_documento' => 'required|integer|digits_between:6,18',
                            'rep_email' => 'required|email',
                            'rep_telefono' => 'required|integer|digits_between:6,10',
                            'rep_coddoc' => 'required|string|min:1',
                            'cargo' => 'required|string|min:5',
                            'tipdoc' => 'required|string|min:1',
                            'razsoc' => 'required|string|min:5',
                            'nit' => 'required|integer|digits_between:6,18',
                            'tipsoc' => 'required|string|min:1',
                            'tipper' => 'required|string|min:1',
                            'is_delegado' => 'required|boolean',
                        ]);
                    }
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
                    $signupEntity = new SignupDomestico;
                    break;
                case 'T':
                case 'P':
                    $signupEntity = null;
                    break;
                default:
                    throw new DebugException('Error el tipo de afiliación es requerido', 1);
                    break;
            }

            $response = (new SignupService)->execute(
                $signupEntity,
                new Srequest($data)
            );

            $this->db->commit();

            return response()->json([
                'success' => true,
                'message' => 'Registro exitoso',
                'data' => $response,
            ], 201);
        } catch (ValidationException $e) {
            $this->db->rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (DebugException $e) {
            $this->db->rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear empresa: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyStore(Request $request)
    {
        try {
            $request->validate([
                'documento' => 'required|numeric|digits_between:6,18',
                'coddoc' => 'required|string|min:1|max:2',
                'tipo' => 'required|string|size:1',
                'delivery_method' => 'required|string|min:5|max:15',
                'token' => 'nullable|string|min:40|max:5000',
            ]);

            $claims = [
                'documento' => $request->input('documento'),
                'coddoc' => $request->input('coddoc'),
                'tipo' => $request->input('tipo'),
                'delivery_method' => $request->input('delivery_method'),
                'context' => 'verifyStore',
            ];
            $token = (new AuthJwt(30))->SimpleToken($claims);
            // Primero validar existencia en mercurio07 para no romper la FK al insertar/actualizar mercurio19
            $user07 = Mercurio07::where('documento', $request->input('documento'))
                ->where('coddoc', $request->input('coddoc'))
                ->where('tipo', $request->input('tipo'))
                ->first();

            if (! $user07) {
                return response()->json([
                    'success' => false,
                    'msj' => 'No existe un usuario registrado con los datos ingresados. Verifique o regístrese para continuar.',
                ], 422);
            }

            $user19 = Mercurio19::where('documento', $request->input('documento'))
                ->where('coddoc', $request->input('coddoc'))
                ->where('tipo', $request->input('tipo'))
                ->first();

            if ($user19) {
                $user19->setToken($token);
                $user19->update();
            } else {
                $user19 = new Mercurio19([
                    'documento' => $request->input('documento'),
                    'coddoc' => $request->input('coddoc'),
                    'tipo' => $request->input('tipo'),
                    'token' => $token,
                ]);
                $user19->save();
            }

            // $user07 ya está validado arriba
            $codigoVerify = genera_code();
            $inicio = Carbon::now()->format('Y-m-d H:i:s');
            $intentos = '0';

            Mercurio19::where('documento', $request->input('documento'))
                ->where('coddoc', $request->input('coddoc'))
                ->where('tipo', $request->input('tipo'))
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

                if (! $user07->getWhatsapp()) {
                    throw new DebugException('No se proporcionó número de whatsapp ' . $user07->getWhatsapp(), 501);
                }

                $html = "> Código de verificación:
                *{$codigoVerify}*. Generación de PIN plataforma Comfaca En Línea, utiliza el código de verificación para confirmar el propietario de la línea de whatsapp.";
                $apiWhatsaap = new ApiWhatsapp;
                $apiWhatsaap->send([
                    'servicio' => 'Whatsapp',
                    'metodo' => 'enviar',
                    'params' => [
                        'numero' => $user07->getWhatsapp(),
                        'mensaje' => $html,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Código de verificación enviado correctamente',
                'token' => $token,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (DebugException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el código de verificación: ' . $e->getMessage(),
            ], 500);
        }
    }

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

            $claims = [
                'documento' => $request->input('documento'),
                'coddoc' => $request->input('coddoc'),
                'tipo' => $request->input('tipo'),
                'delivery_method' => $request->input('delivery_method'),
                'context' => 'recoverySend',
            ];
            $token = (new AuthJwt(30))->SimpleToken($claims);

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
