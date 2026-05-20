<?php

namespace Tests\Feature\Auth;

use Tests\Refresh\NoRefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use NoRefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/web/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        $response = $this->post('/web/register/trabajador', [
            'coddoc' => '1',
            'documento' => '12345678',
            'tipo' => 'T',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'delivery_method' => 'email',
        ]);

        $this->assertAuthenticated();
    }
}
