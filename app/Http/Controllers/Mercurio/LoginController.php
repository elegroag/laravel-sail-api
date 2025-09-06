<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Library\Auth\AuthJwt;
use App\Library\Auth\SessionCookies;
use App\Models\Adapter\DbBase;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use App\Models\Mercurio30;
use App\Models\Subsi54;
use App\Services\Autentications\AutenticaEmpresa;
use App\Services\Autentications\AutenticaTrabajador;
use App\Services\Autentications\AutenticaIndependiente;
use App\Services\Autentications\AutenticaPensionado;
use App\Services\Autentications\AutenticaFacultativo;
use App\Services\Autentications\AutenticaParticular;
use App\Services\Entidades\NotificacionService;
use App\Services\PreparaFormularios\GestionFirmaNoImage;
use App\Services\Signup\SignupDomestico;
use App\Services\Signup\SignupEmpresas;
use App\Services\Signup\SignupFacultativos;
use App\Services\Signup\SignupIndependientes;
use App\Services\Signup\SignupParticular;
use App\Services\Signup\SignupPensionados;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Request as RequestParams;
use App\Services\Utils\AsignarFuncionario;
use App\Services\Utils\SenderEmail;
use Carbon\Carbon;

class LoginController extends ApplicationController
{

    /**
     * asignarFuncionario variable
     * @var AsignarFuncionario
     */
    protected $asignarFuncionario;

    protected $db;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
    }

    public function indexAction()
    {
        return view('auth.login');
    }

    public function showRegisterAction()
    {
        return view('auth.register');
    }

    public function authenticateAction(Request $request, Response $response)
    {
        try {
            // Sanitización ligera
            $tipo = $request->input("tipo");
            $documento = $request->input("documento");
            $coddoc = $request->input("coddoc");
            $clave = $request->input("clave");
            $res = False;

            // Validación básica de requeridos
            if ($tipo === '' || $documento === '' || $coddoc === '') {
                throw new DebugException("Error de acceso, los parámetros tipo, documento y coddoc son requeridos.", 422);
            }

            switch ($tipo) {
                case 'E':
                    $autentica = new AutenticaEmpresa();
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'T':
                    $autentica = new AutenticaTrabajador();
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'I':
                    $autentica = new AutenticaIndependiente();
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'O':
                    $autentica = new AutenticaPensionado();
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'F':
                    $autentica = new AutenticaFacultativo();
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'P':
                    $autentica = new AutenticaParticular();
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                default:
                    throw new DebugException("Error de acceso, el tipo ingreso es requerido.", 501);
                    break;
            }

            if ($res == False) {
                return $this->renderObject(
                    array(
                        "success" => false,
                        'msj' => $autentica->getMessage(),
                        'noAccess' => -1
                    ),
                    false
                );
            }

            $mercurio07 = (new Mercurio07)->findFirst(" tipo='{$tipo}' AND documento='{$documento}' AND coddoc='{$coddoc}'");
            if ($mercurio07 == False) $mercurio07 = $autentica->getAfiliado();

            if ($mercurio07 == False) {
                throw new DebugException("Error acceso incorrecto. Los datos no corresponden a un usuario registrado en el sistema.", 501);
            }

            // Validar estado activo si existe campo estado
            if (method_exists($mercurio07, 'getEstado')) {
                $estado = $mercurio07->getEstado();
                if (!empty($estado) && $estado !== 'A') {
                    throw new DebugException("La cuenta no se encuentra activa para iniciar sesión.", 403);
                }
            }

            if ($clave === 'xxxx') {

                if ($tipo == 'N' || $tipo == 'P') {
                    throw new DebugException("Alerta. El usuario ya posee un registro en plataforma y requiere de ingresar con la clave valida.", 501);
                } else {
                    //create validation mediante token
                    $codigoVerify = generaCode();
                    $autentica->verificaPin($mercurio07, $codigoVerify);

                    $authJwt = new AuthJwt();
                    $token = $authJwt->SimpleToken();

                    $user19 = (new Mercurio19())->findFirst("documento='{$documento}' AND coddoc='{$coddoc}' AND tipo='{$tipo}'");
                    $inicio  = date('Y-m-d H:i:s');
                    if ($user19) {
                        $momento = new \DateTime($user19->getInicio());
                        // Obtener el momento actual
                        $ahora = new \DateTime("now");
                        // Calcular la diferencia
                        $diferencia = $momento->diff($ahora);
                        // Convertir la diferencia a minutos
                        $diferenciaEnMinutos = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
                        if ($diferenciaEnMinutos >= 5) {
                            $intentos = 0;
                        } else {
                            $intentos = $user19->getIntentos() ? $user19->getIntentos() + 1 : 0;
                        }

                        Mercurio19::where('documento', $documento)
                            ->where('coddoc', $coddoc)
                            ->where('tipo', $tipo)
                            ->update([
                                'intentos' => (int) $intentos,
                                'inicio'   => $inicio,
                                'codver'   => (string) $codigoVerify,
                                'token'    => (string) $token,
                            ]);
                    } else {
                        $user19 = new Mercurio19();
                        $user19->setTipo($tipo);
                        $user19->setCoddoc($coddoc);
                        $user19->setDocumento($documento);
                        $user19->setIntentos(0);
                        $user19->setInicio($inicio);
                        $user19->setCodver($codigoVerify);
                        $user19->setToken($token);
                        $user19->setCodigo(1);
                        if (!$user19->save()) {
                            $msj = '';
                            foreach ($user19->getMessages() as $message)  $msj .= ' ' . $message->getMessage();
                            throw new DebugException("Error al guardar Token Access, {$msj}", 501);
                        }
                    }

                    $this->autoFirma($documento, $coddoc);

                    return $this->renderObject(
                        array(
                            'success' => false,
                            'msj' => "Alerta. El usuario ya posee un registro en plataforma y requiere de ingresar con PIN de validación.",
                            'noAccess' => 2,
                            "documento" => $documento,
                            "coddoc" => $coddoc,
                            "tipafi" => $tipo,
                            "tipo" => $tipo,
                            "id" => null
                        ),
                        false
                    );
                }
            }

            // Comparación segura del hash de clave (esquema actual: md5(password_hash_old(clave)))
            $expectedHash = md5(password_hash_old((string) $clave));
            $storedHash = (string) $mercurio07->getClave();
            if (!hash_equals($storedHash, $expectedHash)) {
                throw new DebugException("Error el valor de la clave no es válido para ingresar a la plataforma.", 503);
            }

            $auth = new SessionCookies(
                "model: mercurio07",
                "tipo: {$tipo}",
                "coddoc: {$coddoc}",
                "documento: {$documento}",
                "estado: A"
            );

            if (!$auth->authenticate()) {
                throw new DebugException("Error acceso incorrecto. No se logra completar la autenticación", 504);
            } else {
                $this->autoFirma($documento, $coddoc);
                $msj = "La autenticación se ha completado con éxito.";
                $response = array(
                    "success" => true,
                    "location" => 'principal/index',
                    "msj" => $msj
                );
            }
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "msj" => $e->getMessage() . ' ' . $e->getLine() . ' ' . basename($e->getFile())
            );
        }

        return $this->renderObject($response);
    }

    public function logoutAction()
    {
        SessionCookies::destroyIdentity();
        redirect()->to("mercurio/login");
    }

    /**
     * recuperar_claveAction function
     * Opción solo para el caso de olvido de clave, para empresas o afiliados comfaca.
     * @return void
     */
    public function recuperarClaveAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $documento = $request->input('documento');
            $coddoc = $request->input('coddoc');
            $email = strtolower($request->input('email'));
            $tipo = $request->input('tipo');

            $res = False;
            switch ($tipo) {
                case 'E':
                    $autentica = new AutenticaEmpresa();
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'T':
                    $autentica = new AutenticaTrabajador();
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'I':
                    $autentica = new AutenticaIndependiente();
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'O':
                    $autentica = new AutenticaPensionado();
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                case 'P':
                    $autentica = new AutenticaParticular();
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    break;
                default:
                    throw new DebugException("Error de acceso, el tipo ingreso es requerido.", 501);
                    break;
            }

            if ($res == False) {
                $response = array(
                    "success" => false,
                    'msj' => $autentica->getMessage()
                );
                return $this->renderObject($response, false);
                exit;
            }

            $mercurio07 = $autentica->getAfiliado();
            if ($mercurio07 == False) {
                throw new DebugException("Error acceso incorrecto. Los datos no corresponden a un usuario registrado en el sistema.", 501);
            }

            $usuarioEmail = trim(strtolower($mercurio07->getEmail()));
            if ($usuarioEmail != $email) {
                throw new DebugException("Error, la dirección de email no es igual a la que tenemos registrada. " .
                    "Y por tal motivo, no se puede restablecer la clave de acceso.  El indicio de email que está registrado es: " . mask_email($mercurio07->getEmail()), 503);
            }

            $res = $autentica->cambiarClave();
            if ($res == False) {
                throw new DebugException("Error no es posible el cambiar la clave del usuario, el afiliado no es valido", 501);
            }

            $this->autoFirma($mercurio07->getDocumento(), $mercurio07->getCoddoc());

            $response = array(
                "success" => true,
                "msj" => "El proceso se completo con éxito. Se envío un correo a su cuenta con su nueva clave."
            );
        } catch (DebugException $error) {
            $response = array(
                "success" => false,
                "msj" => $error->getMessage() . ' ' . $error->getLine()
            );
        }
        return $this->renderObject($response);
    }

    /**
     * registroAction function
     * @changed [2024-03-20]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function registroAction(Request $request)
    {
        try {
            $this->db->begin();
            $cedrep = $request->input('cedrep');
            $coddoc = $request->input('coddoc');
            $repleg = $request->input('repleg');
            $email = $request->input('email');
            $codciu = $request->input('codciu');
            $tipper = $request->input('tipper');
            $telefono = $request->input('telefono');
            $tipo = $request->input('tipo');
            $calemp = $request->input('calemp');
            $tipsoc = $request->input('tipsoc');
            $razsoc = $request->input('razsoc');
            $coddocrepleg = $request->input('coddocrepleg');
            $nit = $request->input('nit');

            switch ($tipo) {
                case 'E':
                    $signupEntity = new SignupEmpresas();
                    break;
                case 'I':
                    $signupEntity = new SignupIndependientes();
                    break;
                case 'F':
                    $signupEntity = new SignupFacultativos();
                    break;
                case 'O':
                    $signupEntity = new SignupPensionados();
                    break;
                case 'S':
                    $signupEntity = new SignupDomestico();
                    break;
                case 'P':
                    $signupParticular = new SignupParticular();
                    $signupParticular->settings(
                        new RequestParams(
                            array(
                                "documento" => $cedrep,
                                "coddoc" => $coddoc,
                                "nombre" => $repleg,
                                "email" => $email,
                                "codciu" => $codciu,
                                "tipo" => $tipo,
                                "razsoc" => $razsoc
                            )
                        )
                    );
                    $signupParticular->createUserMercurio();
                    $solicitud = (new Mercurio07)->findFirst("coddoc='{$coddoc}' and documento='{$cedrep}' and tipo='{$tipo}'");
                    break;
                default:
                    throw new DebugException("Error el tipo de afiliación es requerido", 1);
                    break;
            }

            if ($tipo !== 'P') {
                $this->asignarFuncionario = new AsignarFuncionario();
                $usuario = $this->asignarFuncionario->asignar($signupEntity->getTipopc(), $codciu);

                $signupParticular = new SignupParticular($signupEntity);
                $signupParticular->main(
                    new RequestParams(
                        array(
                            "nit" => $nit,
                            "cedrep" => $cedrep,
                            "coddoc" => $coddoc,
                            "repleg" => $repleg,
                            "email" => $email,
                            "codciu" => $codciu,
                            "tipper" => $tipper,
                            "telefono" => $telefono,
                            "calemp" => $calemp,
                            "tipo" => $tipo,
                            "tipsoc" => $tipsoc,
                            "coddocrepleg" => $coddocrepleg,
                            "razsoc" => $razsoc,
                            "usuario" => $usuario
                        )
                    )
                );
                $solicitud = $signupEntity->getSolicitud();
            }

            $this->autoFirma($solicitud->getDocumento(), $solicitud->getCoddoc());
            $response = array(
                "success" => true,
                "msj" => "El proceso de registro como persona particular, se ha completado con éxito, " .
                    "las credenciales de acceso le serán enviadas al respectivo correo registrado. " .
                    "Vamos a continuar.\n",
                "documento" => $solicitud->getDocumento(),
                "coddoc" => $solicitud->getCoddoc(),
                "tipo" => 'P',
                "tipafi" => $tipo,
                "id" => ($tipo == 'P') ? $solicitud->getDocumento() : $solicitud->getId()
            );
            $this->db->commit();
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "msj" => $e->getMessage() . "<br/> También puedes comunicar a soporte técnico el problema presentado, dirección soportesistemas.comfaca@gmail.com, línea 4366300 ext 1012",
                "error" => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()
            );
            $this->db->rollBack();
        }
        return $this->renderObject($response, false);
    }

    public function guiaVideosAction()
    {
        # $this->setParamToView("path_externo", "https://www.comfacaenlinea.com.co/public/");
    }

    public function validaEmailAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $email = trim(strtoupper($request->input('email')));
            $documento = trim($request->input('documento'));
            $nit = trim($request->input('nit'));

            $l = (new Mercurio30)->getCount(
                "*",
                "conditions: UPPER(email)='{$email}' AND documento NOT IN('{$documento}','{$nit}')"
            );
            if ($l > 0) {
                throw new DebugException("Error, ya se encuentra un registro con el email ingresado: " . mask_email($email), 501);
            }
            $response = array(
                "success" => true,
                "msj" => "El email está disponible para el registro"
            );
        } catch (DebugException $e) {
            $response = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($response);
    }

    public function downloadDocumentsAction(Request $request)
    {
        $archivo = $request->route('archivo');
        $fichero = "public/docs/formulario_mercurio/" . $archivo;
        $ext = substr(strrchr($archivo, "."), 1);
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

    public function fueraServicioAction()
    {
        $this->setResponse("empty");
        $msj = "El sistema se encuentra en estado de actualización y mantenimiento.<br/>
        Con el fin de corregir errores y actualizar a versiones más seguras y óptimas que buscan la satisfacción de sus usuarios.</br>";
        /*
        $this->setParamToView("hora_inicia", "3:30");
        $this->setParamToView("hora_finaliza", "4:30");
        $this->setParamToView("nota", $msj); */
    }

    public function integracionServicioAction()
    {
        $this->setResponse('ajax');
        return $this->renderObject(
            array(
                "success" => true,
                "data" => array(
                    'fuera_servicio' => env('APP_INTEGRATION', false),
                    'msj' => (env('APP_INTEGRATION', false) == true) ? 'El servicio está suspendido temporalmente.' : 'La ventana de mantenimiento se ha completado con éxito. Muchas gracias por la espera.'
                )
            )
        );
    }

    public function paramsLoginAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $tipoDocumentos = array();

            foreach ((new Gener18())->find() as $mtipo) {
                if ($mtipo->getCoddoc() == '7' || $mtipo->getCoddoc() == '2') continue;
                $tipoDocumentos["{$mtipo->getCoddoc()}"] = $mtipo->getDetdoc();
            }

            $tipsoc = array();
            foreach ((new Subsi54())->find() as $entity) {
                $tipsoc["{$entity->getTipsoc()}"] = $entity->getDetalle();
            }

            $coddoc = array();
            foreach ((new Gener18())->find() as $entity) {
                if ($entity->getCoddoc() == '7' || $entity->getCoddoc() == '2') continue;
                $coddoc["{$entity->getCoddoc()}"] = $entity->getDetdoc();
            }

            $coddocrepleg = array();
            foreach ((new Gener18())->find() as $entity) {
                if ($entity->getCodrua() == 'TI' || $entity->getCodrua() == 'RC') continue;
                $coddocrepleg["{$entity->getCodrua()}"] = $entity->getDetdoc();
            }

            $_codciu = array();
            foreach ((new Gener09())->find("*", "conditions: codzon >='18000' and codzon <= '19000'") as $entity) {
                $_codciu["{$entity->getCodzon()}"] = $entity->getDetzon();
            }

            $tipafi = (new Mercurio07())->getArrayTipos();
            $tipo = array_diff($tipafi, array("T" => "Trabajador"));

            $data = array(
                'tipo' => $tipo,
                'tipafi' => $tipafi,
                'coddoc' => $coddoc,
                'tipper' => (new Mercurio30)->getTipperArray(),
                'tipsoc' => $tipsoc,
                'calemp' => (new Mercurio30)->getCalempArray(),
                'codciu' => $_codciu,
                'coddocrepleg' => $coddocrepleg
            );

            $components = array(
                array('search' => 'tipo', 'name' => 'tipo',   'type' => 'select', 'placeholder' => 'tipo'),
                array('search' => 'tipafi', 'name' => 'tipafi', 'type' => 'select', 'placeholder' => 'tipafi'),
                array('search' => 'coddoc', 'name' => 'coddoc', 'className' => 'js-choice', 'type' => 'select', 'placeholder' => 'coddoc'),
                array('search' => 'tipper', 'name' => 'tipper', 'type' => 'select', 'placeholder' => 'tipper'),
                array('search' => 'tipsoc', 'name' => 'tipsoc', 'className' => 'js-choice', 'type' => 'select', 'placeholder' => 'tipsoc'),
                array('search' => 'calemp', 'name' => 'calemp', 'type' => 'select', 'placeholder' => 'calemp'),
                array('search' => 'codciu', 'name' => 'codciu', 'className' => 'js-choice', 'type' => 'select', 'placeholder' => 'codciu'),
                array('search' => 'coddocrepleg', 'name' => 'coddocrepleg', 'type' => 'select', 'placeholder' => 'coddocrepleg'),
                array('name' => 'cedrep', 'type' => 'input', 'placeholder' => 'cedrep'),
                array('name' => 'nit', 'type' => 'input', 'placeholder' => 'nit'),
                array('name' => 'razsoc', 'type' => 'input', 'placeholder' => 'razsoc'),
                array('name' => 'repleg', 'type' => 'input', 'placeholder' => 'repleg'),
                array('name' => 'tipafi', 'type' => 'input', 'placeholder' => 'tipafi'),
                array('name' => 'email', 'type' => 'input', 'placeholder' => 'email'),
            );

            $salida = array(
                "success" => true,
                "data" => $data,
                'components' => $components,
                "msj" => 'Consulta de params OK'
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function verifyAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $token = $request->input('token');
            $documento = $request->input('documento');
            $coddoc = $request->input('coddoc');
            $tipo = $request->input('tipo');
            $tipafi = $request->input('tipafi');
            $id = $request->input('id');

            $authJwt = new AuthJwt();
            $authJwt->CheckSimpleToken($token);

            $code = array(
                $request->input('code_1', "addslashes", "alpha", "extraspaces", "striptags"),
                $request->input('code_2', "addslashes", "alpha", "extraspaces", "striptags"),
                $request->input('code_3', "addslashes", "alpha", "extraspaces", "striptags"),
                $request->input('code_4', "addslashes", "alpha", "extraspaces", "striptags"),
            );

            $user07 = (new Mercurio07)->findFirst(" documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'");
            if (!$user07) {
                throw new DebugException("Error no es valido el usuario particular", 301);
            }

            $error = '';
            $user19 = (new Mercurio19)->findFirst(" documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'");
            if ($token != $user19->getToken()) {
                $error .= "Error el token ya no es valido para continuar. \n";
            }

            $momento = new \DateTime($user19->getInicio());
            // Obtener el momento actual
            $ahora = new \DateTime("now");
            // Calcular la diferencia
            $diferencia = $momento->diff($ahora);
            // Convertir la diferencia a minutos
            $diferenciaEnMinutos = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;

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
                $emailCaja = (new Mercurio01)->findFirst();
                $senderEmail = new SenderEmail();
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

                $salida = array(
                    "success" => true,
                    "token" =>  base64_encode($tk[0] . '|' . $tk[1]),
                    "isValid" => true,
                    "location" => "principal/index",
                    "msj" => "El proceso de registro como persona particular, se ha completado con éxito,
                    vamos a continuar.<br/><p class='text-center'><i class='fa fa-arrow-down fa-2x' aria-hidden='true'></i></p>",
                );
            } else {
                $token = $authJwt->SimpleToken();
                Mercurio19::where('documento', $documento)
                    ->where('coddoc', $coddoc)
                    ->where('tipo', $tipo)
                    ->update(['token' => (string) $token]);

                $salida = array(
                    "success" => true,
                    "isValid" => false,
                    "token" => $token,
                    "msj" => $error
                );
            }
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    public function tokenParticularAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $documento = sanetizar($request->input('documento'));
            $coddoc = sanetizar($request->input('coddoc'));
            $tipo = sanetizar($request->input('tipo'));

            $authJwt = new AuthJwt();
            $token = $authJwt->SimpleToken();

            $user19 = Mercurio19::where("documento", $documento)
                ->where("coddoc", $coddoc)
                ->where("tipo", $tipo)
                ->first();

            if ($user19) {
                $user19->setToken($token);
                $user19->update();
            } else {
                throw new DebugException("Error los parametros de acceso no son validos para solicitar token", 301);
            }

            $salida = array(
                "success" => true,
                "token" => $token,
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    public function cambioCorreoAction(Request $request)
    {
        $this->setResponse("ajax");
        try {
            $documento = $request->input('documento');
            $coddoc = $request->input('coddoc');
            $tipo = $request->input('tipo');
            $email = $request->input('email');
            $telefono = $request->input('telefono');
            $novedad = $request->input('novedad');

            $notificacion = new NotificacionService();

            $user07 = (new Mercurio07)->findFirst(" documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'");
            if (!$user07) {
                throw new DebugException("Error los parametros de acceso no son validos para solicitar token", 301);
            }

            $emailCaja = (new Mercurio01)->findFirst();
            $html = "Se requiere de actualizar el correo electronico de la cuenta de usuario {$user07->getNombre()}<br/>
            Correo electronico anterior: {$user07->getEmail()}<br/>
            Correo electronico nuevo: {$email}<br/>
            Novedad: {$novedad}<br/>
            Telefono: {$telefono}<br/>";

            if ($tipo == 'T') {
                $to_email = "afiliacionyregistro@comfaca.com";
                $funcionario = (new AsignarFuncionario)->asignar('1', $user07->getCodciu());
            } else {
                $to_email = "afiliacionempresas@comfaca.com";
                $funcionario = (new AsignarFuncionario)->asignar('2', $user07->getCodciu());
            }

            $notificacion->createNotificacion(
                array(
                    'titulo' => 'Solicitud de cambio de correo',
                    'descripcion' => $html,
                    'user'   => $funcionario,
                )
            );

            $array_tipo = array(
                'T' => 'Trabajador',
                'P' => 'Particular',
                'O' => 'Pensionado',
                'F' => 'Facultativo',
                'I' => 'Independiente',
                'E' => 'Empleador',
                'S' => 'Servicio domestico'
            );
            $str_tipo = @$array_tipo[$tipo];
            $asunto = "Solicitud de cambio de correo {$str_tipo} Documento: {$documento}";

            $senderEmail = new SenderEmail();
            $senderEmail->setters(
                "emisor_email: {$emailCaja->getEmail()}",
                "emisor_clave: {$emailCaja->getClave()}",
                "asunto: {$asunto}"
            );

            $senderEmail->send(
                array(
                    array(
                        "email" => $to_email,
                        "nombre" => 'Comfaca en linea'
                    )
                ),
                $html
            );

            $salida = array(
                "success" => true,
                "msj" => "Se ha enviado la solicitud de cambio de correo, pronto se contactara con usted para confirmar el cambio. " .
                    "Este proceso puede tardar ya que se requiere de la confirmación de la persona que solicita el cambio por seguridad de la informacion.",
            );
        } catch (DebugException $e) {
            $salida = array(
                "success" => false,
                "msj" => $e->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    function autoFirma($documento, $coddoc)
    {
        $gestionFirmas = new GestionFirmaNoImage(
            array(
                "documento" => $documento,
                "coddoc" => $coddoc
            )
        );
        if ($gestionFirmas->hasFirma() == False) {
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
}
