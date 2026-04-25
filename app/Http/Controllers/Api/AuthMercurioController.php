<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Library\Auth\AuthJwt;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Services\Api\ApiWhatsapp;
use App\Services\Utils\SenderEmail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthMercurioController extends Controller
{

    /**
     * Autenticación para dispositivos móviles
     * Valida credenciales contra Mercurio07
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function authenticateMovile(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'tipo' => 'required|string',
                'coddoc' => 'required|string',
                'documento' => 'required|string',
                'clave' => 'required|string',
            ]);

            $tipo = $request->input('tipo');
            $coddoc = $request->input('coddoc');
            $documento = $request->input('documento');
            $clave = $request->input('clave');

            // Buscar usuario en Mercurio07
            $usuario = Mercurio07::where('tipo', $tipo)
                ->where('coddoc', $coddoc)
                ->where('documento', $documento)
                ->where('estado', 'A')
                ->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado o inactivo',
                ], 404);
            }

            // Validar clave
            if (!Hash::check($clave, $usuario->getClave())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales inválidas',
                ], 401);
            }

            // Generar token JWT
            $auth_jwt = new AuthJwt();
            $token = $auth_jwt->SimpleToken([
                'documento' => $documento,
                'coddoc' => $coddoc,
                'tipo' => $tipo,
                'nombre' => $usuario->getNombre(),
                'email' => $usuario->getEmail(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Autenticación exitosa',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 7200,
                'user' => [
                    'documento' => $documento,
                    'coddoc' => $coddoc,
                    'tipo' => $tipo,
                    'nombre' => $usuario->getNombre(),
                    'email' => $usuario->getEmail(),
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en autenticación: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cambio de contraseña para dispositivos móviles
     * Genera nueva clave y la envía por correo
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function changePasswordMovile(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'documento' => 'required|string',
                'coddoc' => 'required|string',
                'tipo' => 'required|string',
            ]);

            $documento = $request->input('documento');
            $coddoc = $request->input('coddoc');
            $tipo = $request->input('tipo');

            // Buscar usuario en Mercurio07
            $usuario = Mercurio07::where('tipo', $tipo)
                ->where('coddoc', $coddoc)
                ->where('documento', $documento)
                ->where('estado', 'A')
                ->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado o inactivo',
                ], 404);
            }

            // Generar nueva clave aleatoria
            $nuevaClave = Str::random(10);
            $claveHasheada = Hash::make($nuevaClave);

            // Actualizar clave en Mercurio07
            $usuario->setClave($claveHasheada);
            $usuario->save();

            // Enviar correo con nueva clave
            $email = $usuario->getEmail();
            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no tiene correo electrónico registrado',
                ], 400);
            }

            $emailCaja = Mercurio01::first();

            $senderEmail = new SenderEmail();
            $senderEmail->setters(
                "emisor_email: {$emailCaja->getEmail()}",
                "emisor_clave: {$emailCaja->getClave()}",
                "asunto: Nueva contraseña - COMFACA En Línea"
            );

            $body = "
                <html>
                <head>
                    <title>Nueva Contraseña</title>
                </head>
                <body>
                    <h2>Estimado/a {$usuario->getNombre()},</h2>
                    <p>Su contraseña ha sido restablecida exitosamente.</p>
                    <p><strong>Su nueva contraseña es: {$nuevaClave}</strong></p>
                    <p>Por seguridad, le recomendamos cambiar esta contraseña la próxima vez que ingrese al sistema.</p>
                    <p>Si no solicitó este cambio, por favor contáctenos inmediatamente.</p>
                    <br>
                    <p>Saludos,<br>Equipo COMFACA En Línea</p>
                </body>
                </html>
            ";

            $senderEmail->send($email, $body);

            // Enviar por WhatsApp si el usuario tiene número válido
            $whatsapp = $usuario->getWhatsapp();
            if ($whatsapp) {
                // Validar longitud del número (mínimo 10 dígitos para Colombia)
                $whatsappNumerico = preg_replace('/[^0-9]/', '', $whatsapp);
                if (strlen($whatsappNumerico) >= 10) {
                    $mensaje = "Su contraseña ha sido restablecida exitosamente. Su nueva contraseña es: *{$nuevaClave}*. Por seguridad, le recomendamos cambiar esta contraseña la próxima vez que ingrese al sistema. COMFACA En Línea";

                    $apiWhatsapp = new ApiWhatsapp();
                    $apiWhatsapp->send([
                        'servicio' => 'Whatsapp',
                        'metodo' => 'enviar',
                        'params' => [
                            'numero' => $whatsapp,
                            'mensaje' => $mensaje,
                        ],
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Nueva contraseña enviada al correo electrónico',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar contraseña: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Solicita un token para autenticación
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function solicitarToken(Request $request): JsonResponse
    {
        try {
            $usuario = $request->input('usuario');
            $clave = $request->input('password');

            $auth_jwt = new AuthJwt();
            $auth_jwt->AuthHttp($clave, $usuario);
            $token = $auth_jwt->getToken();

            $salida = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 1199,
                'url' => config('app.dominio') . "/api",
            ];
            return response()->json($salida, 201);
        } catch (Exception $err) {
            $salida = [
                'message' => 'Error ' . $err->getMessage() . ' ' . basename($err->getFile()) . ' ' . $err->getLine(),
                'code' => 500,
            ];
            return response()->json($salida, 500);
        }
    }

    /**
     * Validar token JWT
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function validateToken(Request $request): JsonResponse
    {
        try {
            $token = $request->input('token');

            if (!$token) {
                $token = $this->extractTokenFromHeader($request);
            }

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token no proporcionado',
                ], 401);
            }

            $auth_jwt = new AuthJwt();
            $auth_jwt->CheckSimpleToken($token);

            return response()->json([
                'success' => true,
                'message' => 'Token válido',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado: ' . $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Extraer token del header Authorization
     *
     * @param  Request  $request
     * @return string|null
     */
    private function extractTokenFromHeader(Request $request): ?string
    {
        $authorization = $request->header('Authorization');

        if (!$authorization) {
            return null;
        }

        if (preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
