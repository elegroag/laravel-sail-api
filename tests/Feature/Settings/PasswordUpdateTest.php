<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Tests\Refresh\NoRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @group skipped
 * Skipped: /settings/password route does not exist in the system.
 * No equivalent password update functionality exists in this application.
 */
class PasswordUpdateTest extends TestCase
{
    use NoRefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $this->markTestSkipped('Ruta /settings no existe en el sistema');
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $this->markTestSkipped('Ruta /settings no existe en el sistema');
    }
}