<?php

namespace App\Library\Auth;

use App\Exceptions\AuthException;
use App\Models\Gener02;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthJwt
{
    protected $token;
    protected $headers;
    protected $authorization;
    protected $auth_type;

    private $expire; // segundos
    private $expireMinutes; // minutos para tymon/jwt-auth
    private $user;
    private $password;

    public function __construct($expire = 7200)
    {
        if ($expire) {
            $this->expire = $expire;
        }
        // tymon/jwt-auth utiliza minutos para TTL
        $this->expireMinutes = (int) ceil(($this->expire ?? 7200) / 60);
        // En entorno CLI (tests) getallheaders puede no existir
        $this->headers = function_exists('getallheaders') ? getallheaders() : [];
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
        // En CLI (tests) REQUEST_METHOD puede no existir
        $method = $_SERVER['REQUEST_METHOD'] ?? null;
        if ($method === "OPTIONS") {
            die();
        }
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

        $gener02 = Gener02::where('usuario', $usuario)->first();

        if ($gener02 == false) {
            throw new AuthException("El usuario no es valido para continuar con la autenticación.", 6);
        }

        if (!clave_verify($this->password, $gener02->getPassword())) {
            throw new AuthException("La clave no es correcta para continuar con la autenticación.", 6);
        }

        if (!$gener02) {
            throw new AuthException("El acceso no es correcto para continuar con la autenticación. 4", 4);
        }

        if (!$clave) {
            throw new AuthException("La clave es requerida para la autenticación. 3", 3);
        }

        if (!clave_verify($clave, $gener02->getPassword())) {
            throw new AuthException("La clave no es correcta para continuar con la autenticación. 6", 6);
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // Construimos claims personalizados y emitimos el token con TTL configurado
        $claims = [
            'sub' => $usuario->getUsuario(),
            'ip' => $ip,
            'usuario' => $usuario->getUsuario(),
            'tipfun' => $usuario->getTipfun(),
            'estado' => $usuario->getEstado(),
        ];

        try {
            // Establecer TTL por emisión
            JWTAuth::factory()->setTTL($this->expireMinutes);
            $payload = JWTFactory::customClaims($claims)->make();
            $this->token = JWTAuth::encode($payload)->get();
        } catch (JWTException $e) {
            throw new AuthException('No fue posible generar el token de autenticación: ' . $e->getMessage(), 500);
        }

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

                        $gener02 = Gener02::where('usuario', $this->user)->first();

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

                    try {
                        $payload = JWTAuth::setToken($this->token)->getPayload();
                        // Recuperar usuario desde claim si está disponible
                        $this->user = $payload->get('usuario');
                        if ($this->user) {
                            $gener02 = Gener02::where('usuario', $this->user)->first();
                            if ($gener02 == false) {
                                throw new AuthException("El usuario no es valido para su ingreso.", 501);
                            }
                        }
                        return true;
                    } catch (TokenExpiredException $e) {
                        throw new AuthException('El token ha expirado.', 401);
                    } catch (TokenInvalidException $e) {
                        throw new AuthException('El token no es válido.', 401);
                    } catch (JWTException $e) {
                        throw new AuthException('No se pudo procesar el token.', 500);
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
     * Genera un token "simple" con claims base e incluye opcionalmente claims personalizados.
     * No permite sobreescribir claims reservados.
     *
     * @param array $extraClaims Claims adicionales a incluir en el payload
     * @return string token
     */
    public function SimpleToken(array $extraClaims = [])
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

        $claims = [
            'sub' => 'simple-token',
            'usuario' => null,
            'tipfun' => null,
            'estado' => null,
            'ip' => $ip,
        ];

        // Proteger claims reservados para no ser sobreescritos
        $reserved = ['sub','usuario','tipfun','estado','ip','iat','exp','nbf','jti','iss'];
        foreach ($reserved as $key) {
            if (array_key_exists($key, $extraClaims)) {
                unset($extraClaims[$key]);
            }
        }
        if (!empty($extraClaims)) {
            $claims = array_merge($claims, $extraClaims);
        }

        try {
            JWTAuth::factory()->setTTL($this->expireMinutes);
            $payload = JWTFactory::customClaims($claims)->make();
            return JWTAuth::encode($payload)->get();
        } catch (JWTException $e) {
            throw new AuthException('No fue posible generar el token: ' . $e->getMessage(), 500);
        }
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
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $ip = $payload->get('ip');
            if ($ip) {
                return true;
            }
            throw new AuthException("Error el token no contiene IP válida", 5);
        } catch (TokenExpiredException $e) {
            throw new AuthException('El token ha expirado.', 401);
        } catch (TokenInvalidException $e) {
            throw new AuthException('El token no es válido.', 401);
        } catch (JWTException $e) {
            throw new AuthException('No se pudo procesar el token.', 500);
        }
    }

    public function getToken()
    {
        return $this->token;
    }


    public function validaToken()
    {
        try {
            if (!$this->token) {
                $this->token = $this->findToken();
            }
            JWTAuth::setToken($this->token)->getPayload();
            return true;
        } catch (TokenExpiredException $e) {
            return false;
        } catch (TokenInvalidException $e) {
            return false;
        } catch (JWTException $e) {
            return false;
        }
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
