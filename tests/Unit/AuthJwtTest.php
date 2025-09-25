<?php

namespace Tests\Unit;

use App\Library\Auth\AuthJwt;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Providers\JWT\Provider;
use PHPUnit\Framework\Attributes\Test;

class AuthJwtTest extends TestCase
{
    #[Test]
    public function genera_token_simple_con_claims_esperados()
    {
        // Arrange: configurar entorno AJAX y una IP
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        // Forzar un secret de JWT para las pruebas (no dependemos de .env)
        // Llave de 256 bits (>=32 bytes). Usamos 64 hex chars (32 bytes)
        config(['jwt.secret' => '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef']);
        // Forzar algoritmo simétrico HS256 en pruebas
        config(['jwt.algo' => Provider::ALGO_HS256]);
        // Asegurar que no se usen llaves asimétricas
        config(['jwt.keys.public' => null, 'jwt.keys.private' => null, 'jwt.keys.passphrase' => null]);
        // TTL global (no crítico porque el constructor fija TTL por emisión)
        config(['jwt.ttl' => 2]); // minutos

        $auth = new AuthJwt(120); // 120 segundos de TTL

        // Act: generar token simple
        $token = $auth->SimpleToken();
        // Assert: token no vacío y decodificable
        $this->assertIsString($token);
        $this->assertNotEmpty($token);

        $payload = JWTAuth::setToken($token)->getPayload();

        $this->assertSame('127.0.0.1', $payload->get('ip'));
        $this->assertNull($payload->get('usuario'));
        $this->assertNull($payload->get('tipfun'));
        $this->assertNull($payload->get('estado'));

        // Y la verificación simplificada debe ser true
        $this->assertTrue($auth->CheckSimpleToken($token));
    }

    #[Test]
    public function simple_token_incluye_claims_personalizados_y_protege_reservados()
    {
        // Arrange: entorno AJAX e IP fija
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $_SERVER['REMOTE_ADDR'] = '10.0.0.2';

        // Forzar configuración JWT válida en tests
        config(['jwt.secret' => '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef']);
        config(['jwt.algo' => Provider::ALGO_HS256]);
        config(['jwt.keys.public' => null, 'jwt.keys.private' => null, 'jwt.keys.passphrase' => null]);
        config(['jwt.ttl' => 2]);

        $auth = new AuthJwt(60);

        // Claims personalizados, incluyendo intentos de sobrescribir reservados
        $extra = [
            'foo' => 'bar',
            'data' => ['x' => 1, 'y' => 'z'],
            'ip' => 'malicioso', // debe ignorarse
            'sub' => 'override', // debe ignorarse
        ];

        // Act
        $token = $auth->SimpleToken($extra);

        // Assert
        $this->assertIsString($token);
        $this->assertNotEmpty($token);

        $payload = JWTAuth::setToken($token)->getPayload();

        // Claims base protegidos
        $this->assertSame('10.0.0.2', $payload->get('ip'));
        $this->assertSame('simple-token', $payload->get('sub'));

        // Claims personalizados presentes
        $this->assertSame('bar', $payload->get('foo'));
        $this->assertEquals(['x' => 1, 'y' => 'z'], $payload->get('data'));

        // Verificar que el token es válido con CheckSimpleToken
        $this->assertTrue($auth->CheckSimpleToken($token));
    }
}
