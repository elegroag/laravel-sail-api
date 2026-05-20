<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\Refresh\NoRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use NoRefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered()
    {
        $response = $this->get('/web/password/request');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested()
    {
        $user = User::factory()->create();

        $this->post('/web/recovery_send', [
            'documento' => $user->documento,
            'coddoc' => $user->coddoc,
            'tipo' => $user->tipo,
            'delivery_method' => 'email',
            'email' => $user->email,
        ]);
    }

    public function test_reset_password_screen_can_be_rendered()
    {
        $response = $this->get('/web/password/request');

        $response->assertStatus(200);
    }

    public function test_password_reset_flow()
    {
        $user = User::factory()->create();

        $this->post('/web/recovery_send', [
            'documento' => $user->documento,
            'coddoc' => $user->coddoc,
            'tipo' => $user->tipo,
            'delivery_method' => 'email',
            'email' => $user->email,
        ]);

        $response = $this->get('/web/password/request');
        $response->assertStatus(200);
    }
}
