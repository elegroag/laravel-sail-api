<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginController extends ApplicationController
{

    protected $db;

    public function initialize()
    {
        Core::importLibrary("Services", "Services");
        Core::importHelper('hash');
        Core::importHelper('format');
        Core::middlewares('AuthHtml');
        $this->db = (object) DbBase::rawConnect();
        $this->db->setFetchMode(DbBase::DB_ASSOC);
    }

    public function indexAction()
    {
        $this->setResponse("view");
        $this->setParamToView("titulo", "Login");
    }

    public function autenticarAction($comfirmar = 0)
    {
        Services::Init();
        $this->setResponse("ajax");
        $user = $this->getPostParam("user");
        $clave = $this->getPostParam("password");
        $auth = new AuthHtml();
        try {
            try {
                $usuarioServices = new UsuarioServices();
                $auth->buscar_usuario($usuarioServices, $user);
                $auth->principal($clave, $comfirmar);
                $msj = $auth->getResultado();
                $usuarioServices->actualizaUsuario($auth->getUsuario());

                Flash::set_flashdata("success", array(
                    "msj" => $msj,
                    "template" => "tmp_bienvenida"
                ));
            } catch (AuthException $auth_err) {

                $code = $auth_err->getCode();
                $msj = $auth_err->getMessage();

                //si es diferente a error de captcha
                if ($code != 1) {
                    $auth->cargar_intentos($user);
                    $msj = $auth->getResultado();
                }
                Flash::set_flashdata("error", array(
                    "msj" => $msj,
                    "code" => $code
                ));
                Router::rTa('login/index');
                exit;
            }
        } catch (Exception $err) {

            Flash::set_flashdata("error", array(
                "msj" => $err->getMessage() . " " . $err->getFile() . " " . $err->getLine(),
                "code" => $err->getCode()
            ));
            Router::rTa('login/index');
            exit;
        }
        $this->setRequestParam("password", "");
        Router::rTa('principal/index');
        exit;
    }

    public function salirAction()
    {
        Auth::destroyIdentity();
        Router::redirectToApplication('login/index', true);
        exit;
    }

    public function error_accessAction()
    {
        $flash = Flash::get_flashdata();
        if (!isset($flash['error'])) {
            $flash = array('error' => array("message" => "", "code" => 404));
        }
        $this->setParamToView("flash", $flash['error']);
    }

    public function error_access_restAction()
    {
        $this->setResponse("ajax");
        $flash = Flash::get_flashdata();
        if (isset($flash['error'])) {
            return $this->renderObject($flash['error'], false);
        }
        http_response_code($flash['error']['code']);
    }

    public function recoveryAction()
    {
        try {
            $db = (object) DbBase::rawConnect();
            $db->setFetchMode(DbBase::DB_ASSOC);
            $fecha = date('Y-m-d H:i:s');
            $cedula = $this->getPostParam("recovery_cedula");
            $captcha = $this->getPostParam("captcha");
            $_usuario = $this->getPostParam("recovery_usuario");
            if (!Session::get("captcha")) {
                if (!$this->validationCpAction($captcha)) {
                    throw new AuthException("El código captcha no es valido para continuar.", 1);
                } else {
                    Session::set("captcha", true);
                }
            }
            $gener02 = $db->fetchOne("SELECT * FROM gener02 WHERE cedtra='{$cedula}' AND usuario='{$_usuario}' AND estado IN('A','B') LIMIT 1");
            if (!$gener02) {
                throw new AuthException("El usuario no se encuentra registrado en el sistema. No se puede continuar el proceso de recuperación de la cuenta.", 1);
            } else {
                $email = $gener02['estacion'];
                $nombre = $gener02['nombre'];
                $usuario = $gener02['usuario'];
                if (!validar_email($email)) {
                    throw new AuthException("La dirección email del usuario no es valido para continuar con el proceso recuperación de cuenta.", 6);
                }
                $nueva_clave = $this->clave_aleatoria(8);
                $mclave = password_hash_old($nueva_clave);
                $hash = clave_hash($nueva_clave, 10);
                $this->Gener02->updateAll("clave='{$mclave}', update_at='{$fecha}', criptada='{$hash}', estado='A', intentos='0'", "conditions: cedtra='{$cedula}' and usuario='{$_usuario}'");

                ob_start();
                $this->setParamToView("assets", "https://comfacaenlinea.com/public/");
                $this->setParamToView("nombre", $nombre);
                $this->setParamToView("user", $usuario);
                $this->setParamToView("clave", $nueva_clave);
                View::renderView("login/tmp/mail_recovery");
                $mensaje = ob_get_contents();
                ob_end_clean();
                $this->send_email("Recuperar cuenta de acceso al sistema SISUWEB.", $mensaje, array(array("nombre" => $nombre, "email" => $email)));
                $_email = mask_email($email);
                Flash::set_flashdata("success", array(
                    "msj" => "Se ha emitido un mensaje a la dirección email {$_email}. Con los parametros de acceso. Para el ingreso al aplicativo SISUWEB 2021. \nPor favor probar con las nuevas credenciales de acceso. \nGracias."
                ));
            }
        } catch (\AuthException $auth_err) {
            Flash::set_flashdata("error", array(
                "msj" => $auth_err->getMessage(),
                "code" => $auth_err->getCode()
            ));
        } catch (\Exception $err) {
            Flash::set_flashdata("error", array(
                "msj" => $err->getMessage() . " " . $err->getFile() . " " . $err->getLine(),
                "code" => $err->getCode()
            ));
        }
        Router::redirectToApplication('Subsidio/login/index');
        exit;
    }

    public function changeAction()
    {
        try {
            $user = $this->getPostParam("change_usuario");
            $clave_actual = $this->getPostParam("change_password");
            $clave_nueva = $this->getPostParam("change_nuevo_password");
            $captcha = $this->getPostParam("captcha");

            if (!Session::get("captcha")) {
                if (!$this->validationCpAction($captcha)) {
                    throw new AuthException("El código captcha no es valido para continuar.", 1);
                } else {
                    Session::set("captcha", true);
                }
            }
            if (!$user) {
                throw new AuthException("El usuario es requerido para la autenticación.", 2);
            }
            if (!$clave_actual) {
                throw new AuthException("La clave actual es requerida para la autenticación.", 3);
            }
            if (!$clave_nueva) {
                throw new AuthException("La clave nueva es requerida para la autenticación.", 4);
            }
            if (!validar_clave($clave_nueva)) {
                throw new AuthException("La clave no cumple con las reglas exigidas para la seguridad en la autenticación.", 5);
            }
            $usuario = $this->Gener02->findFirst("estado='A' AND usuario='{$user}'");
            if (!$usuario) {
                throw new AuthException("El usuario no es correcto para continuar con la autenticación.", 6);
            }
            $email = $usuario->getEstacion();
            $nombre = $usuario->getNombre();

            if (!validar_email($email)) {
                throw new AuthException("La dirección email del usuario no es valido para continuar con el proceso de cambio de clave para la autenticación.", 7);
            }

            if (strlen($usuario->getCriptada()) > 0) {
                if (!clave_verify($clave_actual, $usuario->getCriptada())) {
                    throw new AuthException("La clave actual no es correcta para continuar la operación.", 9);
                }
            } else {
                $claveOld = password_hash_old($clave_actual);
                if ($usuario->getClave() != $claveOld) {
                    throw new AuthException("La clave actual no es correcta para continuar la operación.", 8);
                }
            }
            $mclave = password_hash_old($clave_nueva);
            $fecha = date('Y-m-d');
            $update_at = date('Y-m-d H:i:s');
            $nhash = clave_hash($clave_nueva, 10);
            $res = $this->Gener02->updateAll("feccla='{$fecha}', update_at='{$update_at}', criptada='{$nhash}', clave='{$mclave}'", "conditions: estado='A' AND usuario='{$user}'");
            if ($res) {
                ob_start();
                $this->setParamToView("assets", "https://comfacaenlinea.com/public/");
                $this->setParamToView("nombre", $nombre);
                $this->setParamToView("user", $user);
                $this->setParamToView("clave_nueva", $clave_nueva);
                View::renderView("login/tmp/mail_login");
                $mensaje = ob_get_contents();
                ob_end_clean();
                $this->send_email("Cambio de clave sistema SISUWEB.", $mensaje, array(array("nombre" => $nombre, "email" => $email)));
                $_email = mask_email($email);
                Flash::set_flashdata("success", array("msj" => "El cambio de clave se ha completado de forma exitosa. Y se ha emitido un mensaje a la dirección email {$_email} para confirmar la solicitud. Gracias."));
            }
        } catch (\AuthException $auth_err) {
            Flash::set_flashdata("error", array(
                "msj" => $auth_err->getMessage(),
                "code" => $auth_err->getCode()
            ));
        } catch (\Exception $err) {
            Flash::set_flashdata("error", array(
                "msj" => $err->getMessage() . " " . $err->getFile() . " " . $err->getLine(),
                "code" => $err->getCode()
            ));
        }
        $this->setRequestParam("password", "");
        Router::redirectToApplication('Subsidio/login/index');
        exit;
    }

    public function captchaAction($fecha = '')
    {
        $rz = Core::getInitialPath() . "public/securimage/";
        require_once "{$rz}securimage.php";
        $this->setResponse("view");

        ob_get_clean();
        $option = array(
            'image_width' => 280,
            'image_height' => 90,
            'text_color' => new Securimage_Color('#164B1F'),
            'code_length' => 5,
            'num_lines' => 3,
            'noise_level' => 2,
            'font_file' => "{$rz}Lato-Regular.ttf"
        );
        $img = new Securimage($option);
        $img->wordlist_file =  "{$rz}words/words.txt";
        $img->signature_font = "{$rz}Lato-Regular.ttf";
        $img->ttf_file = "{$rz}Lato-Regular.ttf";
        $img->audio_path = "{$rz}Lato-Regular.ttf";
        $img->show('');
        $this->render(NULL);
    }

    function send_email($asunto, $mensaje, $destinatarios)
    {
        Core::importFromLibrary("Swift", "Swift.php");
        Core::importFromLibrary("Swift", "Swift/Connection/SMTP.php");
        $smtp = new Swift_Connection_SMTP(
            "smtp.gmail.com",
            Swift_Connection_SMTP::PORT_SECURE,
            Swift_Connection_SMTP::ENC_TLS
        );
        $smtp->setUsername('soporte_sistemas@comfaca.com');
        $smtp->setPassword(env('MAIL_PASSWORD'));
        $smsj = new Swift_Message();
        $smsj->setSubject($asunto);
        $smsj->setContentType("text/html");
        $smsj->setBody($mensaje);
        $swift = new Swift($smtp);
        $email = new Swift_RecipientList();
        foreach ($destinatarios as $ai => $persona) {
            $email->addTo($persona['email'], $persona['nombre']);
        }
        $swift->send($smsj, $email, new Swift_Address('soporte_sistemas@comfaca.com'));
    }

    function clave_aleatoria($long = 8)
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890&_.";
        $password = "";
        for ($i = 0; $i < $long; $i++) {
            $password .= substr($str, rand(0, 62), 1);
        }
        return $password;
    }

    function validationCpAction($code)
    {
        if ($code) {
            require_once Core::getInitialPath() . 'public/securimage/securimage.php';
            $img = new Securimage();
            $valid = $img->check($code);
        }
        if ($valid == true) {
            Session::setData("captcha", true);
            return true;
        } else {
            Session::setData("captcha", false);
            return false;
        }
    }

    public function tokenAction()
    {
        $this->setResponse("ajax");
        try {
            Core::middlewares('AuthJwt');
            $db = (object) DbBase::rawConnect();
            $db->setFetchMode(DbBase::DB_ASSOC);
            $usuario = htmlentities(trim($this->getPostParam("usuario")));
            $clave = htmlentities(trim($this->getPostParam("password")));
            $client_id = htmlentities(trim($this->getPostParam("client_id")));
            $client_secret = htmlentities(trim($this->getPostParam("client_secret")));
            $grant_type = htmlentities(trim($this->getPostParam("grant_type")));

            $auth_jwt = new AuthJwt();
            $auth_jwt->AuthHttp($db, $clave, $usuario, $client_id, $grant_type);
            $token = $auth_jwt->getToken();
            $clientes = AuthJwt::clientesId();
            $servicio_url = false;

            foreach ($clientes as $ai => $cliente) {
                if ($cliente['cliente_id'] == $client_id) {
                    $servicio_url = $cliente['url'];
                    break;
                }
            }
            $salida = array(
                "access_token" => $token,
                "token_type" => 'bearer',
                "expires_in" => 1199,
                "url" => "http://186.119.116.228:8091/{$servicio_url}"
            );
        } catch (Exception $err) {
            $salida = array(
                "message" => "Error " . $err->getMessage() . ' ' . basename($err->getFile()) . ' ' . $err->getLine(),
                "code" => 500
            );
            http_response_code(500);
        }

        return $this->renderObject($salida, false);
    }

    public function enviarCorreoPruebasAction()
    {
        $this->setResponse("ajax");
        $asunto = "PRUEBA";
        $mensaje = "Hola";
        $persona = array(
            "email" => "soportesistemas.comfaca@gmail.com",
            "nombre" => "Soporte Sistemas"
        );
        Core::importFromLibrary("Swift", "Swift.php");
        Core::importFromLibrary("Swift", "Swift/Connection/SMTP.php");
        $smtp = new Swift_Connection_SMTP(
            "smtp.mi.com.co",
            "587", //Swift_Connection_SMTP::PORT_SECURE,
            "SSL" //Swift_Connection_SMTP::ENC_TLS
        );
        $smtp->setUsername("soportesistemas@comfaca.info");
        $smtp->setPassword(env('MAIL_PASSWORD'));
        $smsj = new Swift_Message();
        $smsj->setSubject($asunto);
        $smsj->setContentType("text/html");
        $smsj->setBody($mensaje);
        $swift = new Swift($smtp);
        $email = new Swift_RecipientList();

        $email->addTo($persona['email'], $persona['nombre']);
        $salida = $swift->send($smsj, $email, new Swift_Address($persona['email']));
        var_dump($salida);
    }

    public function fuera_servicioAction()
    {
        $this->setTemplateAfter('none');
        $msj = "El sistema se encuentra en estado de actualización y mantenimiento.<br/>
        Con el fin de corregir errores y actualizar a versiones más seguras y óptimas que buscan la satisfacción de sus usuarios.</br>";
        $this->setParamToView("hora_inicia", "7:30");
        $this->setParamToView("hora_finaliza", "11:30");
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
}
