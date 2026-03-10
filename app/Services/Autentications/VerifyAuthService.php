<?php

namespace App\Services\Autentications;

use App\Exceptions\DebugException;
use App\Library\Auth\SessionCookies;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Services\Api\ApiSubsidio;
use App\Services\Srequest;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class VerifyAuthService
{

    private $payload;
    private $errors;

    public function __construct()
    {
        $this->payload = null;
        $this->errors = [];
    }

    public function execute(Request $request): ?string
    {
        $url = null;
        $afiliado = null;
        try {
            try {
                $documento = $request->input('documento');
                $coddoc = $request->input('coddoc', 1);
                $tipo = $request->input('tipo', 'P');
                $option_request = $request->input('option_request', 'register');
                $token = $request->input('token');

                $code = [
                    $request->input('code_1', '0'),
                    $request->input('code_2', '0'),
                    $request->input('code_3', '0'),
                    $request->input('code_4', '0'),
                ];

                $user07 = Mercurio07::where('documento', $documento)
                    ->where('coddoc', $coddoc)
                    ->where('tipo', $tipo)
                    ->first();

                if (! $user07) {
                    throw new DebugException('Error no es valido el usuario', 301);
                }

                $user19 = Mercurio19::where('documento', $documento)
                    ->where('coddoc', $coddoc)
                    ->where('tipo', $tipo)
                    ->first();

                if (! $user19) {
                    throw new DebugException('Error no es valido el token de verificación', 301);
                }

                if ($token != $user19->token) {
                    $this->errors[] = "Error el token ya no es valido para continuar.";
                }

                $momento = Carbon::parse($user19->inicio);
                $ahora = Carbon::now();
                $diferenciaEnMinutos = $momento->diffInMinutes($ahora);

                if (
                    $user19->intentos >= 3 &&
                    $diferenciaEnMinutos < 5
                ) {
                    $this->errors[] = "Ha superado el número de intentos permitidos para acceder a la cuenta con PIN de seguridad. Espera un poco más, han pasado {$diferenciaEnMinutos} minutos para poder volver acceder.";
                }

                if (
                    count($this->errors) == 0 &&
                    $diferenciaEnMinutos >= 5
                ) {
                    $codigoVerify = genera_code();
                    $inicio = Carbon::now()->format('Y-m-d H:i:s');
                    $intentos = '0';

                    Mercurio19::where('documento', $documento)
                        ->where('coddoc', $coddoc)
                        ->where('tipo', $tipo)
                        ->update([
                            'inicio' => $inicio,
                            'intentos' => (int) $intentos,
                            'codver' => (string) $codigoVerify,
                        ]);

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

                    $this->errors[] = 'Ha superado el tiempo de validación y es necesario volver a generar un nuevo PIN, ' .
                        'y se ha enviado a la dirección de correo registrada en la plataforma. ' .
                        'Por favor comprobar en el buzon del correo e ingresar el nuevo PIN.';
                }

                if (count($this->errors) == 0) {
                    $codver = trim(implode('', $code));
                    if ($codver != trim($user19->codver)) {
                        $inicio = date('Y-m-d H:i:s');
                        $intentos = $user19->intentos + 1;

                        Mercurio19::where('documento', $documento)
                            ->where('coddoc', $coddoc)
                            ->where('tipo', $tipo)
                            ->update([
                                'inicio' => $inicio,
                                'intentos' => (int) $intentos,
                            ]);

                        $this->errors[] = "Error el código no es valido para continuar. No valido: {$codver}";
                    }
                }

                if (count($this->errors) > 0) {
                    throw new DebugException("Error al verificar el usuario, no es posible continuar con el proceso", 301);
                }

                switch ($tipo) {
                    case 'T':
                        $url = 'mercurio/principal/index';
                        $metodo = 'informacion_trabajador';
                        $params = ['cedtra' => $documento, 'coddoc' => $coddoc];
                        break;
                    case 'E':
                        $url = 'mercurio/empresa/index';
                        $metodo = 'informacion_empresa';
                        $params = ['nit' => $documento, 'coddoc' => $coddoc];
                        break;
                    case 'I':
                        $url = 'mercurio/independiente/index';
                        $metodo = 'informacion_empresa';
                        $params = ['nit' => $documento, 'coddoc' => $coddoc];
                        break;
                    case 'O':
                        $url = 'mercurio/pensionado/index';
                        $metodo = 'informacion_empresa';
                        $params = ['nit' => $documento, 'coddoc' => $coddoc];
                        break;
                    case 'F':
                        $url = 'mercurio/facultativo/index';
                        $metodo = 'informacion_empresa';
                        $params = ['nit' => $documento, 'coddoc' => $coddoc];
                        break;
                    default:
                        $url = 'mercurio/principal/index';
                        $metodo = 'informacion_empresa';
                        $params = ['nit' => $documento, 'coddoc' => $coddoc];
                        break;
                }

                $ps = new ApiSubsidio();
                $ps->send([
                    'servicio' => 'ComfacaEmpresas',
                    'metodo' => $metodo,
                    'params' => $params,
                ]);

                $out = $ps->toArray();

                $isSuccess = $out['success'] ?? null;
                if ($isSuccess) {
                    $afiliado = $out['data'] ?? null;
                }
                $estadoAfiliado = ($afiliado) ? $afiliado['estado'] : 'I';

                if (! SessionCookies::authenticate(
                    'mercurio',
                    new Srequest(
                        [
                            'tipo' => $tipo,
                            'coddoc' => $coddoc,
                            'documento' => $documento,
                            'estado' => 'A',
                            'estado_afiliado' => $estadoAfiliado,
                        ]
                    )
                )) {
                    throw new DebugException('Error en la autenticación del usuario', 501);
                }

                if ($option_request == 'register') {
                    set_flashdata(
                        'success',
                        [
                            'type' => 'html',
                            'msj' => "<p style='font-size:1rem' class='text-left'>El usuario ha realizado el pre-registro de forma correcta</p>" .
                                "<p style='font-size:1rem' class='text-left'>El registro realizado es de tipo \"Particular\", ahora puedes realizar las afiliaciones de modo seguro.<br/>" .
                                'Las credenciales de acceso le seran enviadas a la respectiva dirección de correo registrado.<br/></p>',
                        ]
                    );
                }

                if ($option_request == 'recovery') {
                    $url = 'mercurio/principal/index#change-password';
                }
            } catch (DebugException $e) {
                $this->payload = [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $this->errors ? implode(', ', $this->errors) : [],
                ];
            }
        } catch (Exception $e) {
            $this->payload = [
                'success' => false,
                'message' => 'Error inesperado en el proceso de autenticación',
                'errors' => [
                    $e->getMessage(),
                ],
                'exception' => [
                    'trace' => $e->getTraceAsString(),
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile()),
                ],
            ];
        }

        return $url ?? null;
    }

    public function rules()
    {
        return [
            'token' => 'required|string|max:800',
            'documento' => 'required|numeric|digits_between:6,18',
            'coddoc' => 'required|string|min:1|max:2',
            'tipo' => 'required|string|size:1',
            'code_1' => 'required|string|size:1',
            'code_2' => 'required|string|size:1',
            'code_3' => 'required|string|size:1',
            'code_4' => 'required|string|size:1',
            'option_request' => 'required|string|max:40',
        ];
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }
}
