<?php
namespace App\Http\Controllers\Mercurio;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Gener09;
use App\Models\Gener18;
use App\Models\Mercurio07;
use App\Models\Subsi54;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginController extends ApplicationController
{

    /**
     * asignarFuncionario variable
     * @var AsignarFuncionario
     */
    protected $asignarFuncionario;


    public function indexAction()
    {
    }

    public function autenticarAction(Request $request, Response $response)
    {
        try {
            $tipo = $this->getPostParam("tipo", "striptags", "extraspaces");
            $documento = $this->getPostParam("documento", "striptags", "extraspaces");
            $coddoc = $this->getPostParam("coddoc", "striptags", "extraspaces");
            $clave = $this->getPostParam("clave", "extraspaces");
            $res = False;

            switch ($tipo) {
                case 'E':
                    $autentica = $services->get('AutenticaEmpresa', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                case 'T':
                    $autentica = $services->get('AutenticaTrabajador', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                case 'I':
                    $autentica = $services->get('AutenticaIndependiente', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                case 'O':
                    $autentica = $services->get('AutenticaPensionado', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                case 'F':
                    $autentica = $services->get('AutenticaFacultativo', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                case 'P':
                    $autentica = $services->get('AutenticaParticular', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                case 'N':
                    $autentica = $services->get('AutenticaFoninnez', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                default:
                    throw new Exception("Error de acceso, el tipo ingreso es requerido.", 501);
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

            $mercurio07 = $this->Mercurio07->findFirst(" tipo='{$tipo}' AND documento='{$documento}' AND coddoc='{$coddoc}'");
            if ($mercurio07 == False) $mercurio07 = $autentica->getAfiliado();

            if ($mercurio07 == False) {
                throw new Exception("Error acceso incorrecto. Los datos no corresponden a un usuario registrado en el sistema.", 501);
            }

            if ($clave === 'xxxx') {

                if ($tipo == 'N' || $tipo == 'P') {
                    throw new Exception("Alerta. El usuario ya posee un registro en plataforma y requiere de ingresar con la clave valida.", 501);
                } else {
                    //create validation mediante token
                    $codigoVerify = $this->generaCode();
                    $autentica->verificaPin($mercurio07, $codigoVerify);

                    $authJwt = new AuthJwt();
                    $token = $authJwt->SimpleToken();

                    $user19 = (new Mercurio19)->findFirst("documento='{$documento}' AND coddoc='{$coddoc}' AND tipo='{$tipo}'");
                    $inicio  = date('Y-m-d H:i:s');
                    if ($user19) {
                        $momento = new DateTime($user19->getInicio());
                        // Obtener el momento actual
                        $ahora = new DateTime("now");
                        // Calcular la diferencia
                        $diferencia = $momento->diff($ahora);
                        // Convertir la diferencia a minutos
                        $diferenciaEnMinutos = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;
                        if ($diferenciaEnMinutos >= 5) {
                            $intentos = 0;
                        } else {
                            $intentos = $user19->getIntentos() ? $user19->getIntentos() + 1 : 0;
                        }

                        (new Mercurio19)->updateAll(
                            "intentos='{$intentos}', inicio='{$inicio}', codver='{$codigoVerify}', token='{$token}'",
                            "conditions: documento='{$documento}' AND coddoc='{$coddoc}' AND tipo='{$tipo}'"
                        );
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
                            throw new Exception("Error al guardar Token Access, {$msj}", 501);
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

            if ($mercurio07->getClave() != md5(password_hash_old(strval($clave)))) {
                throw new \Exception("Error el valor de la clave no es valido para ingresar a la plataforma.", 503);
            }

            $auth = new Auth('model', "class: Mercurio07", "tipo: {$tipo}", "coddoc: {$coddoc}", "documento: {$documento}", "estado: A");
            if (!$auth->authenticate()) {
                throw new \Exception("Error acceso incorrecto. No se logra completar la autenticación", 504);
            } else {
                $this->autoFirma($documento, $coddoc);
                $msj = "La autenticación se ha completado con éxito.";
                $response = array(
                    "success" => true,
                    "location" => 'principal/index',
                    "msj" => $msj
                );

            }
        } catch (\Exception $e) {
            $response = array(
                "success" => false,
                "msj" => $e->getMessage() . ' ' . $e->getLine() . ' ' . basename($e->getFile())
            );
        }

        return $this->renderObject($response);
    }

    public function salirAction()
    {
        Auth::destroyIdentity();
        Router::rTa("login/index");
        exit;
    }

    /**
     * recuperar_claveAction function
     * Opción solo para el caso de olvido de clave, para empresas o afiliados comfaca.
     * @return void
     */
    public function recuperar_claveAction()
    {
        $this->setResponse("ajax");
        $services = Services::Init();
        try {
            $documento = $this->getPostParam('documento', "addslaches", "extraspaces", "striptags");
            $coddoc = $this->getPostParam('coddoc', "addslaches", "extraspaces", "striptags");
            $email = strtolower($this->getPostParam('email', "addslaches", "extraspaces", "striptags"));
            $tipo = $this->getPostParam('tipo', "addslaches", "extraspaces", "striptags");

            $res = False;
            switch ($tipo) {
                case 'E':
                    $autentica = $services->get('AutenticaEmpresa', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                case 'T':
                    $autentica = $services->get('AutenticaTrabajador', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                case 'I':
                    $autentica = $services->get('AutenticaIndependiente', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                case 'O':
                    $autentica = $services->get('AutenticaPensionado', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                case 'P':
                    $autentica = $services->get('AutenticaParticular', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                case 'N':
                    $autentica = $services->get('AutenticaFoninnez', true);
                    $res = $autentica->comprobarSISU($documento, $coddoc);
                    $autentica->endTransa();
                    break;
                default:
                    throw new Exception("Error de acceso, el tipo ingreso es requerido.", 501);
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
                throw new Exception("Error acceso incorrecto. Los datos no corresponden a un usuario registrado en el sistema.", 501);
            }

            $usuarioEmail = trim(strtolower($mercurio07->getEmail()));
            if ($usuarioEmail != $email) {
                throw new Exception("Error, la dirección de email no es igual a la que tenemos registrada. " .
                    "Y por tal motivo, no se puede restablecer la clave de acceso.  El indicio de email que está registrado es: " . mask_email($mercurio07->getEmail()), 503);
            }

            $res = $autentica->cambiarClave();
            if ($res == False) {
                throw new Exception("Error no es posible el cambiar la clave del usuario, el afiliado no es valido", 501);
            }

            $this->autoFirma($mercurio07->getDocumento(), $mercurio07->getCoddoc());

            $response = array(
                "success" => true,
                "msj" => "El proceso se completo con éxito. Se envío un correo a su cuenta con su nueva clave."
            );
        } catch (Exception $error) {
            $response = array(
                "success" => false,
                "msj" => $error->getMessage() . ' ' . $error->getLine()
            );
        }
        return $this->renderObject($response, false);
    }

    /**
     * registroAction function
     * @changed [2024-03-20]
     * @author elegroag <elegroag@ibero.edu.co>
     * @return void
     */
    public function registroAction()
    {
        $services = Services::Init();
        clearstatcache();
        $this->setResponse("ajax");
        try {
            AuthCSRF::Valid();
            $cedrep = $this->getPostParam('cedrep', "addslaches", "alpha", "extraspaces", "striptags");
            $coddoc = $this->getPostParam('coddoc', "addslaches", "alpha", "extraspaces", "striptags");
            $repleg = $this->getPostParam('repleg', "addslaches", "extraspaces", "striptags");
            $email = $this->getPostParam('email', "addslaches", "extraspaces", "striptags");
            $codciu = $this->getPostParam('codciu', "addslaches", "extraspaces", "striptags");
            $tipper = $this->getPostParam('tipper', "addslaches", "extraspaces", "striptags");
            $telefono = $this->getPostParam('telefono', "addslaches", "extraspaces", "striptags");
            $tipo = $this->getPostParam('tipo', "addslaches", "extraspaces", "striptags");
            $calemp = $this->getPostParam('calemp', "addslaches", "extraspaces", "striptags");
            $tipsoc = $this->getPostParam('tipsoc', "addslaches", "extraspaces", "striptags");
            $razsoc = $this->getPostParam('razsoc', "addslaches", "extraspaces", "striptags");
            $coddocrepleg = $this->getPostParam('coddocrepleg', "addslaches", "alpha", "extraspaces", "striptags");
            $nit = $this->getPostParam('nit', "addslaches", "alpha", "extraspaces", "striptags");

            switch ($tipo) {
                case 'E':
                    $signupEntity = $services->get('SignupEmpresas', true);
                    break;
                case 'I':
                    $signupEntity = $services->get('SignupIndependientes', true);
                    break;
                case 'F':
                    $signupEntity = $services->get('SignupFacultativos', true);
                    break;
                case 'O':
                    $signupEntity = $services->get('SignupPensionados', true);
                    break;
                case 'S':
                    $signupEntity = $services->get('SignupDomestico', true);
                    break;
                case 'P':
                    $signupParticular = new SignupParticular(false);
                    $signupParticular->setTransa();
                    $signupParticular->settings(
                        new Request(
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
                    $signupParticular->endTransa();
                    $solicitud = $this->Mercurio07->findFirst("coddoc='{$coddoc}' and documento='{$cedrep}' and tipo='{$tipo}'");
                    break;
                default:
                    throw new Exception("Error el tipo de afiliación es requerido", 1);
                    break;
            }

            if ($tipo !== 'P') {
                $this->asignarFuncionario = $services->get('AsignarFuncionario');
                $usuario = $this->asignarFuncionario->asignar($signupEntity->getTipopc(), $codciu);

                $signupParticular = new SignupParticular($signupEntity);
                $signupParticular->main(
                    new Request(
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

                $signupParticular->endTransa();
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
        } catch (Exception $e) {
            $response = array(
                "success" => false,
                "msj" => $e->getMessage() . "<br/> También puedes comunicar a soporte técnico el problema presentado, dirección soportesistemas.comfaca@gmail.com, línea 4366300 ext 1012",
                "error" => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile()
            );
        }
        return $this->renderObject($response, false);
    }

    public function guia_afiliationAction() {}

    public function guia_videosAction()
    {
        $this->setParamToView("path_externo", "https://www.comfacaenlinea.com.co/public/");
    }

    public function valida_emailAction()
    {
        $this->setResponse("ajax");
        try {
            try {
                $email = trim(strtoupper($this->getPostParam('email')));
                $documento = trim($this->getPostParam('documento'));
                $nit = trim($this->getPostParam('nit'));

                $l = (new Mercurio30)->count(
                    "*",
                    "conditions: UPPER(email)='{$email}' AND documento NOT IN('{$documento}','{$nit}')"
                );
                if ($l > 0) {
                    throw new Exception("Error, ya se encuentra un registro con el email ingresado: " . mask_email($email), 501);
                }
                $response = array(
                    "success" => true,
                    "msj" => "El email está disponible para el registro"
                );
            } catch (DbException $e) {
                $response = array(
                    "success" => false,
                    "msj" => $e->getMessage()
                );
            }
        } catch (Exception $err) {
            $response = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }
        $this->renderText(json_encode($response));
    }

    public function download_docsAction($archivo)
    {
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
            $this->redirect('login/index');
            exit();
        }
    }

    public function fuera_servicioAction()
    {
        $this->setTemplateAfter('none');
        $msj = "El sistema se encuentra en estado de actualización y mantenimiento.<br/> 
        Con el fin de corregir errores y actualizar a versiones más seguras y óptimas que buscan la satisfacción de sus usuarios.</br>";
        $this->setParamToView("hora_inicia", "3:30");
        $this->setParamToView("hora_finaliza", "4:30");
        $this->setParamToView("nota", $msj);
    }

    public function integracion_servicioAction()
    {
        $this->setResponse('ajax');
        Core::ConfigMode();
        return $this->renderObject(
            array(
                "success" => true,
                "data" => array(
                    'fuera_servicio' => Core::$modeIntegration,
                    'msj' => (Core::$modeIntegration == true) ? 'El servicio está suspendido temporalmente.' : 'La ventana de mantenimiento se ha completado con éxito. Muchas gracias por la espera.'
                )
            )
        );
    }

    public function paramsLoginAction()
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
        } catch (\Exception $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }
        return $this->renderObject($salida, false);
    }

    public function verifyAction()
    {
        $this->setResponse("ajax");
        Services::Init();
        try {
            Core::middlewares('AuthJwt');
            $authJwt = new AuthJwt(1000);
            $authJwt->CheckSimpleToken();

            $documento = sanetizar($this->getPostParam('documento', "addslaches", "alpha", "extraspaces", "striptags"));
            $coddoc = sanetizar($this->getPostParam('coddoc', "addslaches", "alpha", "extraspaces", "striptags"));
            $tipo = sanetizar($this->getPostParam('tipo', "addslaches", "extraspaces", "striptags"));
            $tipafi = sanetizar($this->getPostParam('tipafi', "addslaches", "extraspaces", "striptags"));
            $id = sanetizar($this->getPostParam('id', "addslaches", "extraspaces", "striptags"));

            $code = array(
                sanetizar($this->getPostParam('code_1', "addslaches", "alpha", "extraspaces", "striptags")),
                sanetizar($this->getPostParam('code_2', "addslaches", "alpha", "extraspaces", "striptags")),
                sanetizar($this->getPostParam('code_3', "addslaches", "alpha", "extraspaces", "striptags")),
                sanetizar($this->getPostParam('code_4', "addslaches", "alpha", "extraspaces", "striptags")),
            );

            $user07 = (new Mercurio07)->findFirst(" documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'");
            if (!$user07) {
                throw new Exception("Error no es valido el usuario particular", 301);
            }

            $error = '';
            $user19 = (new Mercurio19)->findFirst(" documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'");
            if ($authJwt->getToken() != $user19->getToken()) {
                $error .= "Error el token ya no es valido para continuar. \n";
            }

            $momento = new DateTime($user19->getInicio());
            // Obtener el momento actual
            $ahora = new DateTime("now");
            // Calcular la diferencia
            $diferencia = $momento->diff($ahora);
            // Convertir la diferencia a minutos
            $diferenciaEnMinutos = ($diferencia->days * 24 * 60) + ($diferencia->h * 60) + $diferencia->i;

            //para mas de tres intentos fallidos
            if ($user19->getIntentos() >= 3 && $diferenciaEnMinutos < 5) {
                // Verificar si la diferencia es exactamente 10 minutos
                $error .= "Ha superado el número de intentos permitidos para acceder a la cuenta con PIN de seguridad. Espera un poco más, han pasado {$diferenciaEnMinutos} minutos para poder volver acceder. \n";
            }

            if (strlen($error) == 0 && $diferenciaEnMinutos > 5) {
                //volver a generar PIN
                $codigoVerify = $this->generaCode();
                $inicio  = date('Y-m-d H:i:s');
                $intentos = '0';

                (new Mercurio19)->updateAll(
                    " inicio='{$inicio}', intentos='{$intentos}', codver='{$codigoVerify}'",
                    "conditions: documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'"
                );

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

                $senderEmail->send(
                    array(array(
                        "email" => $user07->getEmail(),
                        "nombre" => $user07->getNombre()
                    )),
                    $html
                );

                $error .= "Ha superado el tiempo de validación y es necesario volver a generar un nuevo PIN, y se ha enviado a la dirección de correo registrada en la plataforma. Por favor comprobar en el buzon del correo e ingresar el nuevo PIN.\n";
            }

            if (strlen($error) == 0) {
                $codver = trim(implode('', $code));
                if ($codver != trim($user19->getCodver())) {
                    $inicio  = date('Y-m-d H:i:s');
                    $intentos = $user19->getIntentos() + 1;

                    (new Mercurio19())->updateAll(
                        " inicio='{$inicio}', intentos='{$intentos}' ",
                        "conditions: documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'"
                    );

                    $error .= "Error el código no es valido para continuar. {$codver} = {$user19->getCodver()} \n";
                }
            }

            if (strlen($error) == 0) {
                $tk = encrypt(
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

                $auth = new Auth(
                    'model',
                    "class: Mercurio07",
                    "tipo: {$tipo}",
                    "coddoc: {$coddoc}",
                    "documento: {$documento}",
                    "estado: A"
                );

                if (!$auth->authenticate()) {
                    throw new Exception("Error en la autenticación del usuario", 501);
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
                (new Mercurio19)->updateAll(
                    " token='{$token}' ",
                    "conditions: documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'"
                );
                $salida = array(
                    "success" => true,
                    "isValid" => false,
                    "token" => $token,
                    "msj" => $error
                );
            }
        } catch (Exception $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    public function tokenParticularAction()
    {
        Core::middlewares('AuthJwt');
        $this->setResponse("ajax");
        try {
            $documento = sanetizar($this->getPostParam('documento', "addslaches", "alpha", "extraspaces", "striptags"));
            $coddoc = sanetizar($this->getPostParam('coddoc', "addslaches", "alpha", "extraspaces", "striptags"));
            $tipo = sanetizar($this->getPostParam('tipo', "addslaches", "extraspaces", "striptags"));

            $authJwt = new AuthJwt();
            $token = $authJwt->SimpleToken();
            $user19 = $this->Mercurio19->findFirst(" documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'");
            if ($user19) {
                $user19->setToken($token);
                $user19->save();
            } else {
                throw new Exception("Error los parametros de acceso no son validos para solicitar token", 301);
            }

            $salida = array(
                "success" => true,
                "token" => $token,
            );
        } catch (Exception $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }
        return $this->renderObject($salida);
    }

    function generaCode()
    {
        $codigo_verify = "";
        $seed = str_split('1234567890');
        shuffle($seed);
        foreach (array_rand($seed, 4) as $k) $codigo_verify .= $seed[$k];
        return $codigo_verify;
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

    public function cambio_correoAction()
    {
        Core::middlewares('AuthJwt');
        $this->setResponse("ajax");
        try {
            $documento = sanetizar($this->getPostParam('documento', "addslaches", "alpha", "extraspaces", "striptags"));
            $coddoc = sanetizar($this->getPostParam('coddoc', "addslaches", "alpha", "extraspaces", "striptags"));
            $tipo = sanetizar($this->getPostParam('tipo', "addslaches", "extraspaces", "striptags"));
            $email = $this->getPostParam('email', "addslaches", "alpha", "extraspaces", "striptags");
            $telefono = sanetizar($this->getPostParam('telefono', "addslaches", "alpha", "extraspaces", "striptags"));
            $novedad = $this->getPostParam('novedad', "addslaches", "alpha", "extraspaces", "striptags");

            $notificacion = new NotificacionService();
            $notificacion->setTransa();

            $user07 = (new Mercurio07)->findFirst(" documento='{$documento}' and coddoc='{$coddoc}' and tipo='{$tipo}'");
            if (!$user07) {
                throw new Exception("Error los parametros de acceso no son validos para solicitar token", 301);
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

            $notificacion->endTransa();
            $salida = array(
                "success" => true,
                "msj" => "Se ha enviado la solicitud de cambio de correo, pronto se contactara con usted para confirmar el cambio. " .
                    "Este proceso puede tardar ya que se requiere de la confirmación de la persona que solicita el cambio por seguridad de la informacion.",
            );
        } catch (Exception $err) {
            $salida = array(
                "success" => false,
                "msj" => $err->getMessage()
            );
        }
        return $this->renderObject($salida);
    }
}
