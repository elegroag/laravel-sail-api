<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\Refresh\NoRefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use NoRefreshDatabase;

    public function test_email_verification_screen_can_be_rendered()
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/web/verify/T/' . $user->coddoc . '/' . $user->documento);

        $response->assertStatus(200);
    }
}