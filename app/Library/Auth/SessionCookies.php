<?php
namespace App\Library\Auth;

class SessionCookies
{
    private $model, $tipo, $coddoc, $documento, $estado;
    private $cookieName = 'MER_CURR_SESS';
    private $ttlSeconds = 7200; // 2 horas por defecto

    public function __construct(...$params)
    {
        $arguments = get_params_destructures($params);
        // Aceptar tanto 'model' como 'class' para compatibilidad
        $this->model = $arguments['model'] ?? $arguments['class'] ?? null;
        $this->tipo = $arguments['tipo'];
        $this->coddoc = $arguments['coddoc'];
        $this->documento = $arguments['documento'];
        $this->estado = $arguments['estado'];
    }

    public function init()
    {
    }

    /**
     * authenticate function
     * @return bool
     */
    public function authenticate()
    {
        // Validaciones básicas de parámetros
        if (empty($this->model) || empty($this->tipo) || empty($this->coddoc) || empty($this->documento)) {
            return false;
        }

        // Resolver clase de modelo (asumimos namespace App\Models)
        $modelClass = '\\App\\Models\\' . ltrim(trim($this->model));
        if (!class_exists($modelClass)) {
            return false;
        }

        // Construir condición de búsqueda
        $condiciones = " tipo='{$this->tipo}' AND coddoc='{$this->coddoc}' AND documento='{$this->documento}'";
        if (!empty($this->estado)) {
            $condiciones .= " AND estado='{$this->estado}'";
        }

        // Buscar usuario
        $usuario = (new $modelClass())->findFirst($condiciones);
        if (!$usuario) {
            return false;
        }

        // Preparar payload mínimo
        $payload = [
            'm' => $this->model,
            't' => $this->tipo,
            'c' => $this->coddoc,
            'd' => $this->documento,
            'u' => method_exists($usuario, 'getId') ? $usuario->getId() : ($usuario->id ?? null),
            'n' => method_exists($usuario, 'getNombre') ? $usuario->getNombre() : null,
            'e' => method_exists($usuario, 'getEmail') ? $usuario->getEmail() : null,
            'ts' => time(),
        ];

        // Firmar cookie
        $token = $this->buildSignedToken($payload);
        if ($token === null) {
            return false;
        }

        // Establecer cookie segura
        return $this->setCookie($token);
    }

    public static function destroyIdentity()
    {
        $instance = new self("model: Mercurio07", "tipo:", "coddoc:", "documento:", "estado:");
        $name = $instance->cookieName();
        // Invalidar cookie en el cliente
        setcookie($name, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        // Limpiar superglobal por consistencia
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
        }
    }

    // ==========================
    // Métodos auxiliares privados
    // ==========================

    private function cookieName(): string
    {
        // Permitir sobreescritura vía config si existe
        $configured = function_exists('config') ? (config('session.cookie', null) ?? config('app.session_cookie', null)) : null;
        return $configured ?: $this->cookieName;
    }

    private static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') return true;
        if (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) return true;
        return false;
    }

    private function setCookie(string $value): bool
    {
        $name = $this->cookieName();
        $expires = time() + (function_exists('config') ? (int) (config('session.lifetime', 120) * 60) : $this->ttlSeconds);
        return setcookie($name, $value, [
            'expires' => $expires,
            'path' => '/',
            'secure' => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    private function buildSignedToken(array $payload): ?string
    {
        $json = json_encode($payload);
        if ($json === false) return null;
        $data = rtrim(strtr(base64_encode($json), '+/', '-_'), '=');

        // Clave de firma desde app.key
        $secret = null;
        if (function_exists('config')) {
            $secret = config('app.key');
            // En Laravel APP_KEY puede venir con el prefijo base64:
            if (is_string($secret) && str_starts_with($secret, 'base64:')) {
                $decoded = base64_decode(substr($secret, 7), true);
                if ($decoded !== false) {
                    $secret = $decoded;
                }
            }
        }
        if (empty($secret)) {
            // Fallback inseguro pero funcional en entornos sin config cargada
            $secret = 'insecure-fallback-secret';
        }

        $sig = hash_hmac('sha256', $data, $secret);
        return $data . '.' . $sig;
    }
}
