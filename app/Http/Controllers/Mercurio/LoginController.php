<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Auth\AuthJwt;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Models\Mercurio30;
use App\Models\Subsi54;
use App\Services\Autentications\AutenticaEmpresa;
use App\Services\Autentications\AutenticaIndependiente;
use App\Services\Autentications\AutenticaParticular;
use App\Services\Autentications\AutenticaPensionado;
use App\Services\Autentications\AutenticaTrabajador;
use App\Services\Entidades\NotificacionService;
use App\Services\PreparaFormularios\GestionFirmaNoImage;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoginController extends ApplicationController
{
    /**
     * asignarFuncionario variable
     *
     * @var AsignarFuncionario
     */
    protected $asignarFuncionario;

    protected $db;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
    }

    public function index()
    {
        return view('mercurio.auth.login');
    }

    public function showRegister()
    {
        return view('mercurio.auth.register');
    }

    /**
     * recuperar_clave function
     * Opción solo para el caso de olvido de clave, para empresas o afiliados comfaca.
     *
     * @return void
     */
    public function recuperarClave(Request $request)
    {
        try {
            $documento = $request->input('documento');
            $coddoc = $request->input('coddoc');
            $email = strtolower($request->input('email'));
            $tipo = $request->input('tipo');

            $res = false;
            switch ($tipo) {
                case 'E':
                    $autentica = new AutenticaEmpresa;
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'T':
                    $autentica = new AutenticaTrabajador;
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'I':
                    $autentica = new AutenticaIndependiente;
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'O':
                    $autentica = new AutenticaPensionado;
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'P':
                    $autentica = new AutenticaParticular;
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                default:
                    throw new DebugException('Error de acceso, el tipo ingreso es requerido.', 501);
                    break;
            }

            if ($res == false) {
                $response = [
                    'success' => false,
                    'msj' => $autentica->getMessage(),
                ];

                return response()->json($response);
            }

            $mercurio07 = $autentica->getAfiliado();
            if ($mercurio07 == false) {
                throw new DebugException('Error acceso incorrecto. Los datos no corresponden a un usuario registrado en el sistema.', 501);
            }

            $usuarioEmail = trim(strtolower($mercurio07->getEmail()));
            if ($usuarioEmail != $email) {
                throw new DebugException('Error, la dirección de email no es igual a la que tenemos registrada. ' .
                    'Y por tal motivo, no se puede restablecer la clave de acceso.  El indicio de email que está registrado es: ' . mask_email($mercurio07->getEmail()), 503);
            }

            $res = $autentica->cambiarClave();
            if ($res == false) {
                throw new DebugException('Error no es posible el cambiar la clave del usuario, el afiliado no es valido', 501);
            }

            $this->autoFirma($mercurio07->getDocumento(), $mercurio07->getCoddoc(), '12345');

            $response = [
                'success' => true,
                'msj' => 'El proceso se completo con éxito. Se envío un correo a su cuenta con su nueva clave.',
            ];
        } catch (\Throwable $error) {
            $salida = $this->handleException($error, request());
            $response = [
                'success' => false,
                'msj' => $salida['msj'],
            ];
        }

        return response()->json($response);
    }

    public function guiaVideos()
    {
        // $this->setParamToView("path_externo", "https://www.comfacaenlinea.com.co/public/");
    }

    public function validaEmail(Request $request)
    {
        try {
            $email = trim(strtoupper($request->input('email')));
            $documento = trim($request->input('documento'));
            $nit = trim($request->input('nit'));

            $l = Mercurio30::whereRaw("UPPER(email)='{$email}' AND documento NOT IN('{$documento}','{$nit}')")->count();
            if ($l > 0) {
                throw new DebugException('Error, ya se encuentra un registro con el email ingresado: ' . mask_email($email), 501);
            }
            $response = [
                'success' => true,
                'msj' => 'El email está disponible para el registro',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
            $response = [
                'success' => false,
                'msj' => $salida['msj'],
            ];
        }

        return response()->json($response);
    }

    public function downloadDocuments(Request $request)
    {
        $archivo = $request->route('archivo');
        $fichero = 'public/docs/formulario_mercurio/' . $archivo;
        $ext = substr(strrchr($archivo, '.'), 1);
        if (file_exists($fichero)) {
            header('Content-Description: File Transfer');
            header("Content-Type: application/{$ext}");
            header("Content-Disposition: attachment; filename={$archivo}");
            header('Cache-Control: must-revalidate');
            header('Expires: 0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fichero));
            ob_clean();
            readfile($fichero);
            exit;
        } else {
            redirect('login/index');
            exit();
        }
    }

    public function fueraServicio()
    {
        $this->setResponse('empty');
        $msj = 'El sistema se encuentra en estado de actualización y mantenimiento.<br/>
        Con el fin de corregir errores y actualizar a versiones más seguras y óptimas que buscan la satisfacción de sus usuarios.</br>';
        /*
        $this->setParamToView("hora_inicia", "3:30");
        $this->setParamToView("hora_finaliza", "4:30");
        $this->setParamToView("nota", $msj); */
    }

    public function integracionServicio()
    {
        try {
            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'fuera_servicio' => env('APP_INTEGRATION', false),
                        'msj' => (env('APP_INTEGRATION', false) == true) ? 'El servicio está suspendido temporalmente.' : 'La ventana de mantenimiento se ha completado con éxito. Muchas gracias por la espera.',
                    ],
                ]
            );
        } catch (\Throwable $error) {
            $salida = $this->handleException($error, request());
            return response()->json(
                [
                    'success' => false,
                    'msj' => $salida['msj'],
                ]
            );
        }
    }

    public function paramsLogin(Request $request)
    {
        try {
            $tipoDocumentos = [];

            foreach (Gener18::all() as $mtipo) {
                if ($mtipo->getCoddoc() == '7' || $mtipo->getCoddoc() == '2') {
                    continue;
                }
                $tipoDocumentos["{$mtipo->getCoddoc()}"] = $mtipo->getDetdoc();
            }

            $tipsoc = [];
            foreach (Subsi54::all() as $entity) {
                $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
            }

            $coddoc = [];
            foreach (Gener18::all() as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') {
                    continue;
                }
                $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $coddocrepleg = [];
            foreach (Gener18::all() as $entity) {
                if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') {
                    continue;
                }
                $coddocrepleg["{$entity->getCodrua()}"] = $entity->getDetdoc();
            }

            $_codciu = [];
            foreach ((new Gener09)->getFind('*', "conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $_codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $tipafi = (new Mercurio07)->getArrayTipos();
            $tipo = array_diff($tipafi, ['T' => 'Trabajador']);

            $data = [
                'tipo' => $tipo,
                'tipafi' => $tipafi,
                'coddoc' => $coddoc,
                'tipper' => (new Mercurio30)->getTipperArray(),
                'tipsoc' => $tipsoc,
                'calemp' => (new Mercurio30)->getCalempArray(),
                'codciu' => $_codciu,
                'coddocrepleg' => $coddocrepleg,
            ];

            $components = [
                ['search' => 'tipo', 'name' => 'tipo',   'type' => 'select', 'placeholder' => 'tipo'],
                ['search' => 'tipafi', 'name' => 'tipafi', 'type' => 'select', 'placeholder' => 'tipafi'],
                ['search' => 'coddoc', 'name' => 'coddoc', 'className' => 'js-choice', 'type' => 'select', 'placeholder' => 'coddoc'],
                ['search' => 'tipper', 'name' => 'tipper', 'type' => 'select', 'placeholder' => 'tipper'],
                ['search' => 'tipsoc', 'name' => 'tipsoc', 'className' => 'js-choice', 'type' => 'select', 'placeholder' => 'tipsoc'],
                ['search' => 'calemp', 'name' => 'calemp', 'type' => 'select', 'placeholder' => 'calemp'],
                ['search' => 'codciu', 'name' => 'codciu', 'className' => 'js-choice', 'type' => 'select', 'placeholder' => 'codciu'],
                ['search' => 'coddocrepleg', 'name' => 'coddocrepleg', 'type' => 'select', 'placeholder' => 'coddocrepleg'],
                ['name' => 'cedrep', 'type' => 'input', 'placeholder' => 'cedrep'],
                ['name' => 'nit', 'type' => 'input', 'placeholder' => 'nit'],
                ['name' => 'razsoc', 'type' => 'input', 'placeholder' => 'razsoc'],
                ['name' => 'repleg', 'type' => 'input', 'placeholder' => 'repleg'],
                ['name' => 'tipafi', 'type' => 'input', 'placeholder' => 'tipafi'],
                ['name' => 'email', 'type' => 'input', 'placeholder' => 'email'],
            ];

            $salida = [
                'success' => true,
                'data' => $data,
                'components' => $components,
                'msj' => 'Consulta de params OK',
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
            $salida = [
                'success' => false,
                'msj' => $salida['msj'],
            ];
        }

        return response()->json($salida);
    }

    public function verify(Request $request)
    {
        try {
            $token = $request->input('token');
            $documento = $request->input('documento');
            $coddoc = $request->input('coddoc');
            $tipo = $request->input('tipo');
            $tipafi = $request->input('tipafi');
            $id = $request->input('id');

            $authJwt = new AuthJwt(10);
            $authJwt->CheckSimpleToken($token);

            $code = [
                $request->input('code_1', 'addslashes', 'alpha', 'extraspaces', 'striptags'),
                $request->input('code_2', 'addslashes', 'alpha', 'extraspaces', 'striptags'),
                $request->input('code_3', 'addslashes', 'alpha', 'extraspaces', 'striptags'),
                $request->input('code_4', 'addslashes', 'alpha', 'extraspaces', 'striptags'),
            ];

            $user07 = (new Mercurio07)->findFirst(" documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'");
            if (! $user07) {
                throw new DebugException('Error no es valido el usuario particular', 301);
            }

            $error = '';
            $user19 = (new Mercurio19)->findFirst(" documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'");
            if ($token != $user19->getToken()) {
                $error .= "Error el token ya no es valido para continuar. \n";
            }

            $momento = new \DateTime($user19->getInicio());
            // Obtener el momento actual
            $ahora = new \DateTime('now');
            // Calcular la diferencia
            $diferencia = $momento->diff($ahora);
            // Convertir la diferencia a minutos
            $diferenciaEnMinutos = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;

            // para mas de tres intentos fallidos
            if ($user19->getIntentos() >= 3 && $diferenciaEnMinutos < 5) {
                // Verificar si la diferencia es exactamente 10 minutos
                $error .= "Ha superado el número de intentos permitidos para acceder a la cuenta con PIN de seguridad. Espera un poco más, han pasado {$diferenciaEnMinutos} minutos para poder volver acceder. \n";
            }

            if (strlen($error) == 0 && $diferenciaEnMinutos >= 5) {
                // volver a generar PIN
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
                $error .= "Ha superado el tiempo de validación y es necesario volver a generar un nuevo PIN, y se ha enviado a la dirección de correo registrada en la plataforma. Por favor comprobar en el buzon del correo e ingresar el nuevo PIN.\n";
            }

            if (strlen($error) == 0) {
                $codver = trim(implode('', $code));
                if ($codver != trim($user19->getCodver())) {
                    $inicio = date('Y-m-d H:i:s');
                    $intentos = $user19->getIntentos() + 1;

                    Mercurio19::where('documento', $documento)
                        ->where('coddoc', $coddoc)
                        ->where('tipo', $tipo)
                        ->update([
                            'inicio' => $inicio,
                            'intentos' => (int) $intentos,
                        ]);

                    $error .= "Error el código no es valido para continuar. {$codver} = {$user19->getCodver()} \n";
                }
            }

            if (strlen($error) == 0) {
                $tk = Kencrypt(
                    json_encode(
                        [
                            'documento' => $documento,
                            'coddoc' => $coddoc,
                            'tipo' => $tipo,
                            'tipafi' => $tipafi,
                            'id' => $id,
                        ]
                    )
                );

                if (! Mercurio07::where('documento', $documento)
                    ->where('coddoc', $coddoc)
                    ->where('tipo', $tipo)
                    ->where('estado', 'A')
                    ->exists()) {
                    throw new DebugException('Error en la autenticación del usuario', 501);
                }

                $salida = [
                    'success' => true,
                    'token' => base64_encode($tk[0] . '|' . $tk[1]),
                    'isValid' => true,
                    'location' => 'principal/index',
                    'msj' => "El proceso de registro como persona particular, se ha completado con éxito,
                    vamos a continuar.<br/><p class='text-center'><i class='fa fa-arrow-down fa-2x' aria-hidden='true'></i></p>",
                ];
            } else {
                $token = $authJwt->SimpleToken();
                Mercurio19::where('documento', $documento)
                    ->where('coddoc', $coddoc)
                    ->where('tipo', $tipo)
                    ->update(['token' => (string) $token]);

                $salida = [
                    'success' => true,
                    'isValid' => false,
                    'token' => $token,
                    'msj' => $error,
                ];
            }
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
            $salida = [
                'success' => false,
                'msj' => $salida['msj'],
            ];
        }

        return response()->json($salida);
    }

    public function tokenParticular(Request $request)
    {
        try {
            $documento = sanetizar($request->input('documento'));
            $coddoc = sanetizar($request->input('coddoc'));
            $tipo = sanetizar($request->input('tipo'));

            $authJwt = new AuthJwt(10);
            $token = $authJwt->SimpleToken();

            $user19 = Mercurio19::where('documento', $documento)
                ->where('coddoc', $coddoc)
                ->where('tipo', $tipo)
                ->first();

            if ($user19) {
                $user19->setToken($token);
                $user19->update();
            } else {
                throw new DebugException('Error los parametros de acceso no son validos para solicitar token', 301);
            }

            $salida = [
                'success' => true,
                'token' => $token,
            ];
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
            $salida = [
                'success' => false,
                'msj' => $salida['msj'],
            ];
        }

        return response()->json($salida);
    }

    public function cambioCorreo(Request $request)
    {
        try {

            $documento = $request->input('email');
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
        } catch (\Throwable $e) {
            $salida = $this->handleException($e, request());
            $salida = [
                'success' => false,
                'msj' => $salida['msj'],
            ];
        }

        return response()->json($salida);
    }

    public function autoFirma($documento, $coddoc, $clave)
    {
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
    }

    public function showPdf(Request $request)
    {
        $filename = $request->input('filename');
        $path = storage_path('temp/' . $filename);

        if (! file_exists($path)) {
            return response()->json(['success' => false]);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
