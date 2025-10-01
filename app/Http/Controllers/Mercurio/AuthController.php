<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Library\Auth\AuthJwt;
use App\Library\Auth\SessionCookies;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Models\Subsi54;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

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

    public function verifyAction(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string|max:800',
                'documento' => 'required|numeric|digits_between:6,18',
                'coddoc' => 'required|string|min:1|max:2',
                'tipo' => 'required|string|size:1',
                'code_1' => 'required|string|size:1',
                'code_2' => 'required|string|size:1',
                'code_3' => 'required|string|size:1',
                'code_4' => 'required|string|size:1',
            ]);

            $token = $request->input('token');
            $documento = $request->input('documento');
            $coddoc = $request->input('coddoc');
            $tipo = $request->input('tipo');

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
                $senderEmail->send($user07->getEmail(), $html);

                $error .= "Ha superado el tiempo de validación y es necesario volver a generar un nuevo PIN, " .
                    "y se ha enviado a la dirección de correo registrada en la plataforma. " .
                    "Por favor comprobar en el buzon del correo e ingresar el nuevo PIN.\n";
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

            switch ($tipo) {
                case 'E':
                    $url = "mercurio/empresa/index";
                    break;
                case 'I':
                    $url = "mercurio/independiente/index";
                    break;
                case 'O':
                    $url = "mercurio/pensionado/index";
                    break;
                case 'F':
                    $url = "mercurio/facultativo/index";
                    break;
                default:
                    $url = "mercurio/principal/index";
                    break;
            }

            $auth = new SessionCookies(
                "model: mercurio07",
                "tipo: {$tipo}",
                "coddoc: {$coddoc}",
                "documento: {$documento}",
                "estado: A"
            );

            if (!$auth->authenticate()) {
                throw new DebugException("Error en la autenticación del usuario", 501);
            }

            set_flashdata(
                "success",
                array(
                    "type" => "html",
                    "msj" => "<p style='font-size:1rem' class='text-left'>El usuario ha realizado el pre-registro de forma correcta</p>" .
                        "<p style='font-size:1rem' class='text-left'>El registro realizado es de tipo \"Particular\", ahora puedes realizar las afiliaciones de modo seguro.<br/>" .
                        "Las credenciales de acceso le seran enviadas a la respectiva dirección de correo registrado.<br/></p>"
                )
            );

            return Inertia::location(url($url));
        } catch (ValidationException $e) {
            $payload = [
                "success" => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ];
        } catch (DebugException $e) {
            $payload = [
                "success" => false,
                'message' => 'Error al crear empresa: ' . $e->getMessage()
            ];
        }

        $auth_jwt_temporal = new AuthJwt(10);
        $token = $auth_jwt_temporal->SimpleToken([
            'documento' => $request->input('documento'),
            'coddoc' => $request->input('coddoc'),
            'tipo' => $request->input('tipo'),
            'context' => 'verifyAction.retry',
        ]);

        Mercurio19::where('documento', $request->input('documento'))
            ->where('coddoc', $request->input('coddoc'))
            ->where('tipo', $request->input('tipo'))
            ->update(['token' => (string) $token]);

        $payload['token'] = $token;
        return Inertia::render('Auth/VerifyEmail', $payload);
    }

    public function loadSession()
    {
        $user = session('user');
        $tipo = session('tipo');
        $tipo = session('estado');
        $token = (new AuthJwt(10))->SimpleToken([
            'documento' => $user['documento'],
            'coddoc' => $user['coddoc'],
            'tipo' => $tipo,
            'context' => 'verify',
        ]);

        if (session('estado_afiliado') == 'A') {
            switch ($tipo) {
                case 'E':
                    $url = "mercurio/empresa/index";
                    break;
                case 'I':
                    $url = "mercurio/independiente/index";
                    break;
                case 'O':
                    $url = "mercurio/pensionado/index";
                    break;
                case 'F':
                    $url = "mercurio/facultativo/index";
                    break;
                default:
                    $url = "mercurio/principal/index";
                    break;
            }
            return Inertia::location(url($url));
        } else {
            return Inertia::render('Auth/SessionEmail', [
                'token' => $token,
                'documento' => $user['documento'],
                'coddoc' => $user['coddoc'],
                'tipo' => $tipo,
            ]);
        }
    }
}
