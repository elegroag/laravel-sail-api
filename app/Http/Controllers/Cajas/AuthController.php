<?php

namespace App\Http\Controllers\Cajas;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\CajaServices\UsuarioServices;
use App\Exceptions\AuthException;
use App\Exceptions\DebugException;
use App\Library\Auth\AuthJwt;
use App\Library\Auth\AuthCajas;
use App\Services\Utils\SenderEmail;
use App\Services\View as ServicesView;
use Exception;

class AuthController extends ApplicationController
{

    protected $db;
    protected $user;
    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function indexAction()
    {
        return view('cajas.auth.login');
    }

    public function authenticateAction(Request $request, Response $response)
    {
        $user = $request->input("user");
        $clave = $request->input("password");
        try {
            try {
                $auth = new AuthCajas();
                $auth->autenticar($user, $clave);
                $usuarioServices = new UsuarioServices();
                $usuarioServices->actualizaUsuario($auth->getUsuario());

                set_flashdata(
                    "success",
                    [
                        "msj" => "Bienvenido al sistema",
                        "template" => "tmp_bienvenida"
                    ]
                );
            } catch (AuthException $auth_err) {

                $code = $auth_err->getCode();
                $msj = $auth_err->getMessage();

                //si es diferente a error de captcha
                if ($code != 1) {
                    $auth->cargarIntentos($user);
                    $msj = $auth->getResultado();
                }
                set_flashdata("error", array(
                    "msj" => $msj,
                    "code" => $code
                ));
                return redirect()->route('cajas.login');
            }
        } catch (Exception $err) {

            set_flashdata("error", array(
                "msj" => $err->getMessage() . " " . $err->getFile() . " " . $err->getLine(),
                "code" => $err->getCode()
            ));
            return redirect()->route('cajas.login');
        }
        return redirect()->route('cajas.principal');
    }

    public function salirAction()
    {
        session()->forget('user');
        return redirect('cajas/login');
    }

    public function error_accessAction()
    {
        $flash = get_flashdata();
        if (!isset($flash['error'])) {
            $flash = array('error' => array("message" => "", "code" => 404));
        }

        return view('cajas/auth/error_access', [
            'flash' => $flash['error']
        ]);
    }

    public function error_access_restAction()
    {
        $this->setResponse("ajax");
        $flash = get_flashdata();
        if (isset($flash['error'])) {
            return $this->renderObject($flash['error'], false);
        }
        http_response_code($flash['error']['code']);
    }

    public function recoveryAction()
    {
        $request = request();
        try {
            $db = DbBase::rawConnect();

            $fecha = now()->format('Y-m-d H:i:s');
            $cedula = $request->input("recovery_cedula");
            $captcha = $request->input("captcha");
            $_usuario = $request->input("recovery_usuario");


            $gener02 = $db->fetchOne("SELECT * FROM gener02 WHERE cedtra='{$cedula}' AND usuario='{$_usuario}' AND estado IN('A','B') LIMIT 1");

            if (!$gener02) {
                throw new AuthException("El usuario no se encuentra registrado en el sistema. No se puede continuar el proceso de recuperación de la cuenta.", 1);
            } else {
                $email = $gener02['estacion'];
                $nombre = $gener02['nombre'];
                $usuario = $gener02['usuario'];

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new AuthException("La dirección email del usuario no es valido para continuar con el proceso recuperación de cuenta.", 6);
                }

                $nueva_clave = $this->clave_aleatoria(8);
                // Migrado a Hash de Laravel
                $hash = Hash::make($nueva_clave);

                // TODO: Migrar a Eloquent
                $this->Gener02->updateAll("clave='{$hash}', update_at='{$fecha}', estado='A', intentos='0'", "conditions: cedtra='{$cedula}' and usuario='{$_usuario}'");

                // TODO: Migrar a Mail de Laravel
                $mensaje = view('login.tmp.mail_recovery', [
                    'assets' => asset('/'),
                    'nombre' => $nombre,
                    'user' => $usuario,
                    'clave' => $nueva_clave
                ])->render();

                // TODO: Implementar Mail de Laravel
                $senderEmail = new SenderEmail();
                $senderEmail->send("Recuperar cuenta de acceso al sistema SISUWEB.", $mensaje, array(array("nombre" => $nombre, "email" => $email)));

                $_email = mask_email($email);
                session()->flash("success", array(
                    "msj" => "Se ha emitido un mensaje a la dirección email {$_email}. Con los parametros de acceso. Para el ingreso al aplicativo SISUWEB 2021. \nPor favor probar con las nuevas credenciales de acceso. \nGracias."
                ));
            }
        } catch (AuthException $auth_err) {
            session()->flash("error", array(
                "msj" => $auth_err->getMessage(),
                "code" => $auth_err->getCode()
            ));
        } catch (DebugException $err) {
            session()->flash("error", array(
                "msj" => $err->getMessage() . " " . $err->getFile() . " " . $err->getLine(),
                "code" => $err->getCode()
            ));
        }
        return redirect()->route('subsidio.login.index');
    }

    public function changeAction()
    {
        $request = request();
        try {
            $user = $request->input("change_usuario");
            $clave_actual = $request->input("change_password");
            $clave_nueva = $request->input("change_nuevo_password");
            $captcha = $request->input("captcha");


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
                ServicesView::renderView("login/tmp/mail_login");
                $mensaje = ob_get_contents();
                ob_end_clean();

                $senderEmail = new SenderEmail();
                $senderEmail->send("Cambio de clave sistema SISUWEB.", $mensaje, array(array("nombre" => $nombre, "email" => $email)));
                $_email = mask_email($email);
                set_flashdata("success", array("msj" => "El cambio de clave se ha completado de forma exitosa. Y se ha emitido un mensaje a la dirección email {$_email} para confirmar la solicitud. Gracias."));
            }
        } catch (AuthException $auth_err) {
            set_flashdata("error", array(
                "msj" => $auth_err->getMessage(),
                "code" => $auth_err->getCode()
            ));
        } catch (\Exception $err) {
            set_flashdata("error", array(
                "msj" => $err->getMessage() . " " . $err->getFile() . " " . $err->getLine(),
                "code" => $err->getCode()
            ));
        }

        return redirect()->route('subsidio.login.index');
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

    public function tokenAction()
    {
        $request = request();
        $this->setResponse("ajax");
        try {
            $db = DbBase::rawConnect();
            $usuario = $request->input("usuario");
            $clave = $request->input("password");
            $client_id = $request->input("client_id");
            $client_secret = $request->input("client_secret");
            $grant_type = $request->input("grant_type");

            $auth_jwt = new AuthJwt();
            $auth_jwt->AuthHttp($db, $clave, $usuario, $client_id, $grant_type);
            $token = $auth_jwt->getToken();
            $servicio_url = false;
            $salida = [
                "access_token" => $token,
                "token_type" => 'bearer',
                "expires_in" => 1199,
                "url" => "http://186.119.116.228:8091/{$servicio_url}"
            ];
        } catch (Exception $err) {
            $salida = array(
                "message" => "Error " . $err->getMessage() . ' ' . basename($err->getFile()) . ' ' . $err->getLine(),
                "code" => 500
            );
            http_response_code(500);
        }

        return $this->renderObject($salida, false);
    }

    public function fuera_servicioAction()
    {
        $msj = "El sistema se encuentra en estado de actualización y mantenimiento.<br/>
        Con el fin de corregir errores y actualizar a versiones más seguras y óptimas que buscan la satisfacción de sus usuarios.</br>";
        $this->setParamToView("hora_inicia", "7:30");
        $this->setParamToView("hora_finaliza", "11:30");
        $this->setParamToView("nota", $msj);
    }
}
