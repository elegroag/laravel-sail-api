<?php

namespace Tests\Feature\Cajas;

use App\Http\Middleware\CajasAuthenticated;
use App\Services\CaptchaService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CajasLoginInertiaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        }

        config(['logging.default' => 'stderr']);

        $this->withoutMiddleware(CajasAuthenticated::class);
    }

    public function test_rutas_login_captcha_y_autenticar_existen(): void
    {
        foreach (['cajas.login', 'cajas.captcha.image', 'cajas.autenticar'] as $name) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
        }

        $login = Route::getRoutes()->getByName('cajas.login');
        $captcha = Route::getRoutes()->getByName('cajas.captcha.image');
        $auth = Route::getRoutes()->getByName('cajas.autenticar');

        $this->assertContains('GET', $login->methods());
        $this->assertSame('index', $login->getActionMethod());
        $this->assertContains('GET', $captcha->methods());
        $this->assertContains('POST', $auth->methods());
        $this->assertSame('authenticate', $auth->getActionMethod());
    }

    public function test_captcha_service_rechaza_vacio_y_acepta_codigo_vigente(): void
    {
        Session::start();

        $this->assertFalse(CaptchaService::verify(''));
        $this->assertFalse(CaptchaService::verify(null));

        $code = CaptchaService::generate(5);
        $this->assertSame(5, strlen($code));
        $this->assertTrue(CaptchaService::verify($code));
        // one-shot: segundo intento falla
        $this->assertFalse(CaptchaService::verify($code));
    }
}
