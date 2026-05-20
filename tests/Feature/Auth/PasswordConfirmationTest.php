<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\Refresh\NoRefreshDatabase;
use Tests\TestCase;

/**
 * Password confirmation tests.
 *
 * Note: The project does not have a /confirm-password route.
 * The MercurioAuthController uses PIN-based verification via
 * /web/verify/{tipo}/{coddoc}/{documento} instead of Laravel's
 * standard email verification flow.
 */
class PasswordConfirmationTest extends TestCase
{
    use NoRefreshDatabase;

    public function test_confirm_password_route_does_not_exist()
    {
        // The /confirm-password route does not exist in this project.
        // The system uses PIN-based password recovery via /web/password/request
        // and /web/recovery_send endpoints instead.
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/confirm-password');
        $response->assertStatus(404);
    }
}