<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Library\Auth\AuthJwt;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Models\Subsi54;
use App\Services\Api\ApiWhatsapp;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;

class AuthController extends Controller
{
    public function index()
    {
        $tipsoc = array();
        $coddoc = array();
        $detadoc = array();
        $codciu = array();

        foreach (Subsi54::all() as $entity) {
            $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
        }

        foreach (Gener18::all() as $entity) {
            if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') continue;
            $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
        }

        foreach (Gener18::all() as $entity) {
            if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') continue;
            $detadoc["{$entity->getCodrua()}"] = $entity->getDetdoc();
        }

        foreach (Gener09::where("codzon", '>=',  18000)->where("codzon", '<=', 19000)->get() as $entity) {
            $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
        }

        return Inertia::render('Auth/Login', [
            'Coddoc' => $coddoc,
            'Tipsoc' => $tipsoc,
            'Codciu' => $codciu,
            'Detadoc' => $detadoc
        ]);
    }

    public function register()
    {
        $tipsoc = array();
        $coddoc = array();
        $detadoc = array();
        $codciu = array();

        foreach (Subsi54::all() as $entity) {
            $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
        }

        foreach (Gener18::all() as $entity) {
            if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') continue;
            $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
        }

        foreach (Gener18::all() as $entity) {
            if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') continue;
            $detadoc["{$entity->getCodrua()}"] = $entity->getDetdoc();
        }

        foreach (Gener09::where("codzon", '>=',  18000)->where("codzon", '<=', 19000)->get() as $entity) {
            $codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
        }
        return Inertia::render('Auth/Register', [
            'Coddoc' => $coddoc,
            'Tipsoc' => $tipsoc,
            'Codciu' => $codciu,
            'Detadoc' => $detadoc
        ]);
    }

    public function resetPassword()
    {
        return Inertia::render('Auth/ResetPassword');
    }

    public function verify(Request $request, $tipo = null, $coddoc = null, $documento = null)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'documento' => 'required|numeric|digits_between:6,18',
                'coddoc' => 'required|numeric|min:1',
                'tipo' => 'required|string|size:1',
            ]);
            $payload = [
                'documento' => $request->input('documento'),
                'coddoc' => $request->input('coddoc'),
                'tipo' => $request->input('tipo'),
            ];
        } else {
            $payload = [
                'documento' => $documento,
                'coddoc' => $coddoc,
                'tipo' => $tipo,
            ];
        }

        // Claims básicos a transportar en el token temporal
        $claims = [
            'documento' => $payload['documento'],
            'coddoc' => $payload['coddoc'],
            'tipo' => $payload['tipo'],
            'context' => 'verify',
        ];
        $token = (new AuthJwt(10))->SimpleToken($claims);
        // Validar existencia del padre en mercurio07 para evitar romper la FK
        $user07 = Mercurio07::where("documento", $payload['documento'])
            ->where("coddoc", $payload['coddoc'])
            ->where("tipo", $payload['tipo'])
            ->first();

        $user19 = Mercurio19::where("documento", $payload['documento'])
            ->where("coddoc", $payload['coddoc'])
            ->where("tipo", $payload['tipo'])
            ->first();

        $codigoVerify = generaCode();
        if ($user19) {
            $user19->token = $token;
            $user19->update();
        } else {
            // Si no existe el usuario padre en mercurio07, no crear mercurio19 para no violar la FK
            if (!$user07) {
                return Inertia::render('Auth/VerifyEmail', [
                    'documento' => $payload['documento'],
                    'coddoc' => $payload['coddoc'],
                    'tipo' => $payload['tipo'],
                    'token' => $token,
                    'error' => 'No existe un usuario registrado con los datos ingresados. Por favor verifique o regístrese.'
                ]);
            }
            $user19 = new Mercurio19();
            $user19->fill([
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
            $user19->save();
        }

        $payload['token'] = $token;
        return Inertia::render('Auth/VerifyEmail', $payload);
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
            $token = (new AuthJwt(10))->SimpleToken($claims);
            // Primero validar existencia en mercurio07 para no romper la FK al insertar/actualizar mercurio19
            $user07 = Mercurio07::where("documento", $request->input('documento'))
                ->where("coddoc", $request->input('coddoc'))
                ->where("tipo", $request->input('tipo'))
                ->first();

            if (!$user07) {
                return response()->json([
                    "success" => false,
                    "msj" => "No existe un usuario registrado con los datos ingresados. Verifique o regístrese para continuar."
                ]);
            }

            $user19 = Mercurio19::where("documento", $request->input('documento'))
                ->where("coddoc", $request->input('coddoc'))
                ->where("tipo", $request->input('tipo'))
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

            $codigoVerify = generaCode();
            $inicio  = Carbon::now()->format('Y-m-d H:i:s');
            $intentos = '0';

            Mercurio19::where('documento', $request->input('documento'))
                ->where('coddoc', $request->input('coddoc'))
                ->where('tipo', $request->input('tipo'))
                ->update([
                    'inicio'   => $inicio,
                    'intentos' => (int) $intentos,
                    'codver'   => (string) $codigoVerify,
                ]);

            if ($request->input('delivery_method') == 'email') {
                $res = $this->sendCodeEmail(
                    $codigoVerify,
                    $user07->getEmail()
                );
            } else {
                $res = $this->sendCodeWhatsapp(
                    $codigoVerify,
                    $user07->getWhatsapp()
                );
            }

            $salida = [
                "success" => true,
                "msj" => "Código de verificación enviado correctamente",
                "token" => $token,
                "res" => $res
            ];
        } catch (ValidationException $e) {
            $salida = [
                "success" => false,
                "msj" => $e->getMessage()
            ];
        }
        return response()->json($salida);
    }

    public function verifyAction(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string|max:255',
                'documento' => 'required|numeric|digits_between:6,18',
                'coddoc' => 'required|string|min:1|max:2',
                'tipo' => 'required|string|size:1',
                'tipafi' => 'required|string|size:1',
                'id' => 'required|numeric',
            ]);

            $token = $request->input('token');
            $documento = $request->input('documento');
            $coddoc = $request->input('coddoc');
            $tipo = $request->input('tipo');
            $tipafi = $request->input('tipafi');
            $id = $request->input('id');

            $auth_jwt_temporal = new AuthJwt(10);
            $auth_jwt_temporal->CheckSimpleToken($token);

            $code = [
                $request->input('code_1'),
                $request->input('code_2'),
                $request->input('code_3'),
                $request->input('code_4'),
            ];

            $user07 = Mercurio07::where("documento", $documento)
                ->where("coddoc", $coddoc)
                ->where("tipo", $tipo)
                ->first();

            if (!$user07) {
                throw new DebugException("Error no es valido el usuario particular", 301);
            }

            $error = '';
            $user19 = Mercurio19::where("documento", $documento)
                ->where("coddoc", $coddoc)
                ->where("tipo", $tipo)
                ->first();

            if ($token != $user19->getToken()) {
                $error .= "Error el token ya no es valido para continuar. \n";
            }

            $momento = Carbon::parse($user19->getInicio());
            // Obtener el momento actual
            $ahora = Carbon::now();
            // Calcular la diferencia
            $diferenciaEnMinutos = $momento->diffInMinutes($ahora);

            //para mas de tres intentos fallidos
            if ($user19->getIntentos() >= 3 && $diferenciaEnMinutos < 5) {
                // Verificar si la diferencia es exactamente 10 minutos
                $error .= "Ha superado el número de intentos permitidos para acceder a la cuenta con PIN de seguridad. Espera un poco más, han pasado {$diferenciaEnMinutos} minutos para poder volver acceder. \n";
            }

            if (strlen($error) == 0 && $diferenciaEnMinutos >= 5) {
                //volver a generar PIN
                $codigoVerify = generaCode();
                $inicio  = Carbon::now()->format('Y-m-d H:i:s');
                $intentos = '0';

                Mercurio19::where('documento', $documento)
                    ->where('coddoc', $coddoc)
                    ->where('tipo', $tipo)
                    ->update([
                        'inicio'   => $inicio,
                        'intentos' => (int) $intentos,
                        'codver'   => (string) $codigoVerify,
                    ]);

                $this->sendCodeEmail($codigoVerify, $user07->getEmail());
                $error .= "Ha superado el tiempo de validación y es necesario volver a generar un nuevo PIN, y se ha enviado a la dirección de correo registrada en la plataforma. Por favor comprobar en el buzon del correo e ingresar el nuevo PIN.\n";
            }

            if (strlen($error) == 0) {
                $codver = trim(implode('', $code));
                if ($codver != trim($user19->getCodver())) {
                    $inicio  = date('Y-m-d H:i:s');
                    $intentos = $user19->getIntentos() + 1;

                    Mercurio19::where('documento', $documento)
                        ->where('coddoc', $coddoc)
                        ->where('tipo', $tipo)
                        ->update([
                            'inicio'   => $inicio,
                            'intentos' => (int) $intentos,
                        ]);

                    $error .= "Error el código no es valido para continuar. {$codver} = {$user19->getCodver()} \n";
                }
            }

            if (strlen($error) == 0) {
                $tk = Kencrypt(
                    json_encode(
                        array(
                            "documento" => $documento,
                            "coddoc" => $coddoc,
                            'tipo' => $tipo,
                            'tipafi' => $tipafi,
                            'id' => $id
                        )
                    )
                );

                if (!Mercurio07::where("documento", $documento)
                    ->where("coddoc", $coddoc)
                    ->where("tipo", $tipo)
                    ->where("estado", "A")
                    ->exists()) {
                    throw new DebugException("Error en la autenticación del usuario", 501);
                }

                $salida = [
                    "success" => true,
                    "token" =>  base64_encode($tk[0] . '|' . $tk[1]),
                    "isValid" => true,
                    "location" => "principal/index",
                    "msj" => "El proceso de registro como persona particular, se ha completado con éxito,
                    vamos a continuar.<br/><p class='text-center'><i class='fa fa-arrow-down fa-2x' aria-hidden='true'></i></p>",
                ];
            } else {
                // Reintento: regenerar token con claims para trazabilidad
                $token = $auth_jwt_temporal->SimpleToken([
                    'documento' => $documento,
                    'coddoc' => $coddoc,
                    'tipo' => $tipo,
                    'context' => 'verifyAction.retry',
                ]);
                Mercurio19::where('documento', $documento)
                    ->where('coddoc', $coddoc)
                    ->where('tipo', $tipo)
                    ->update(['token' => (string) $token]);

                $salida = [
                    "success" => true,
                    "isValid" => false,
                    "token" => $token,
                    "msj" => $error
                ];
            }
        } catch (ValidationException $e) {
            $salida = [
                "success" => false,
                "msj" => $e->getMessage()
            ];
        } catch (DebugException $e) {
            $salida = [
                "success" => false,
                "msj" => $e->getMessage()
            ];
        }

        return response()->json($salida);
    }

    function sendCodeEmail($codigoVerify, $email)
    {
        $html = "Utiliza el siguiente código de verificación, para confirmar el propietario de la dirección de correo:<br/>
        <span style=\"font-size:16px;color:#333\">CÓDIGO DE VERIFICACIÓN: </span><br/>
        <span style=\"font-size:30px;color:#11cdef\"><b>{$codigoVerify}</b></span>";

        $asunto = "Generación nuevo PIN plataforma Comfaca En Línea";
        $emailCaja = Mercurio01::first();
        $senderEmail = new SenderEmail();
        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            "asunto: {$asunto}"
        );
        return $senderEmail->send($email, $html);
    }

    function sendCodeWhatsapp($codigoVerify, $whatsapp)
    {
        $html = "Código de verificación:            
        *{$codigoVerify}*. Generación de PIN plataforma Comfaca En Línea, 
        utiliza el código de verificación para confirmar el propietario de la línea de whatsapp.";
        $apiWhatsaap = new ApiWhatsapp();
        $apiWhatsaap->send([
            'servicio' => 'Whatsapp',
            'metodo' => 'enviar',
            'params' => [
                'numero' => $whatsapp,
                'mensaje' => $html
            ],
        ]);
        return $apiWhatsaap->toArray();
    }
}
