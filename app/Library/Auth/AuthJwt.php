<?php
namespace App\Library\Auth;

use App\Exceptions\AuthException;
use App\Models\Gener02;
use JwtManager;

class AuthJwt
{
    protected $jwt;
    protected $token;
    protected $headers;
    protected $authorization;
    protected $auth_type;

    private $key = "3ducl@c3s2o1p.@9l1c@c10n3duc@t3m@s";
    private $encrypt = "HS384";
    private $expire;
    private $user;
    private $password;

    public function __construct($expire = 7200)
    {
        if ($expire) {
            $this->expire = $expire;
        }
        $this->jwt = new JwtManager();
        $this->jwt->setTokenKey($this->key);
        $this->jwt->setTokenEncrypt($this->encrypt);
        $this->jwt->setTokenExpire($this->expire);
        $this->headers = getallheaders();
    }

    /**
     * loadHeaders function
     * @param [type] $ajax
     * @return void
     */
    public function loadHeaders($ajax)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: POST");
        header("Allow: POST");
        if ($ajax) {
            header("Content-type: application/json; charset=utf-8");
        } else {
            header("Content-type: application/x-www-form-urlencoded; charset=utf-8");
        }
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") die();
    }

    /**
     * AuthHttp function
     * @param [type] $clave
     * @param [type] $usuario
     * @return string token
     */
    public function AuthHttp($clave, $usuario)
    {
        $this->loadHeaders(false);

        if (is_ajax()) {
            throw new AuthException("El acceso AJAX no es correcto al servicio solicitado", 404);
        }
        /**
         * no existe el authorization
         */
        $this->authorization = (isset($this->headers['Authorization'])) ? $this->headers['Authorization'] : null;
        if ($this->authorization) {
            throw new AuthException('La autenticación no requiere de Authorization Token', 404);
        }

        $gener02 = new Gener02();
        $usuario = $gener02->findFirst("gener02.*", "conditions: usuario='{$usuario}'");

        if ($usuario == false) {
            throw new AuthException("El usuario no es valido para continuar con la autenticación.", 6);
        }

        if (!clave_verify($this->password, $usuario->getPassword())) {
            throw new AuthException("La clave no es correcta para continuar con la autenticación.", 6);
        }

        if (!$usuario) {
            throw new AuthException("El acceso no es correcto para continuar con la autenticación. 4", 4);
        }

        if (!$clave) {
            throw new AuthException("La clave es requerida para la autenticación. 3", 3);
        }

        if (!clave_verify($clave, $usuario->getPassword())) {
            throw new AuthException("La clave no es correcta para continuar con la autenticación. 6", 6);
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $this->token = $this->jwt->create(array(
            "ip" => $ip,
            "usuario" => $usuario->getUsuario(),
            "tipfun" => $usuario->getTipfun(),
            "estado" => $usuario->getEstado(),
        ));

        return $this->token;
    }

    /**
     * InitRest function
     * @return bool
     */
    public function InitRest()
    {
        try {
            $this->loadHeaders(true);
            if (!is_ajax()) {
                throw new AuthException("El acceso no es correcto al servicio", 404);
            }

            $this->authorization = (isset($this->headers['Authorization'])) ? $this->headers['Authorization'] : null;

            if ($this->authorization) {
                if (preg_match('/^basic/i', $this->authorization)) {
                    $tk = base64_decode(trim(preg_replace('/^basic/i', "", $this->authorization)));
                    $exp = explode(':', $tk);
                    if (count($exp) == 2) {
                        $this->user = $exp[0];
                        $this->password = $exp[1];
                        $this->auth_type = 'basic';

                        $gener02 = new Gener02();
                        $gener02->findFirst("usuario='{$this->user}'");

                        if (!clave_verify($this->password, $gener02->getCriptada())) {
                            throw new AuthException("La clave no es correcta para continuar con la autenticación.", 501);
                        } else {
                            return true;
                        }
                    } else {
                        throw new AuthException('La autenticación basica requiere de 3 parametros atob(user:password).', 404);
                    }
                }

                if (preg_match('/^bearer/i', $this->authorization)) {
                    $this->token = trim(preg_replace('/^bearer/i', "", $this->authorization));
                    $this->auth_type = 'bearer';
                    $res = $this->jwt->check($this->token);
                    if ($res === false) {
                        throw new AuthException("No es correcto el usuario para su ingreso.", 501);
                    } else {
                        $res = $this->jwt->show($this->token);
                        $this->user = $res->data->user;
                        $gener02 = new Gener02();
                        $usuario = $gener02->findFirst("usuario='{$this->user}'");
                        if ($usuario == false) {
                            throw new AuthException("El usuario no es valido para su ingreso.", 501);
                        }
                        return true;
                    }
                }
            } else {
                throw new AuthException("No se ha recepcionado las credenciales de acceso.", 501);
            }
        } catch (AuthException $err) {
            return $err->getMessage();
        }
    }

    /**
     * SimpleToken function
     * @return string token
     */
    public function SimpleToken()
    {
        $this->loadHeaders(true);
        if (!is_ajax()) {
            throw new AuthException("El acceso no es correcto al servicio, la solicitud no es REST", 404);
        }
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $this->jwt->create(array(
            "usuario" => null,
            "tipfun" => null,
            "estado" => null,
            "ip" => $ip,
        ));
    }

    public function findToken()
    {
        $authorization = (isset($this->headers['Authorization'])) ? $this->headers['Authorization'] : null;
        if (is_null($authorization) == false) {
            if (preg_match('/^bearer/i', $authorization)) {
                $this->token = trim(preg_replace('/^bearer/i', "", $authorization));
            } else {
                throw new AuthException("Error no hay un token de validación", 5);
            }
        } else {
            throw new AuthException("No se ha recepcionado las credenciales de acceso.", 6);
        }
        return $this->token;
    }

    public function CheckSimpleToken($token)
    {
        $this->loadHeaders(true);
        if (!is_ajax()) {
            throw new AuthException("El acceso no es correcto al servicio", 404);
        }
        $token = (is_null($token)) ? $this->findToken() : $token;
        $res = $this->jwt->check($token);
        if ($res) {
            $data = $this->jwt->show($token);
            if ($data->ip) {
                return true;
            }
        } else {
            throw new AuthException("Error el token no es valido para su ingreso", 5);
        }
    }

    public function getToken()
    {
        return $this->token;
    }


    public function validaToken()
    {
        return $this->jwt->check($this->token);
    }

    public function validJson($data)
    {
        if ($data == null || $data == false) {
            return false;
        }
        if (preg_match('/^\{(.*)\:(.*)\}$/', $data)) {
            return true;
        }
        return false;
    }
}
