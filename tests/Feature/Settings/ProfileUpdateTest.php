<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Tests\Refresh\NoRefreshDatabase;
use Tests\TestCase;

/**
 * @group skipped
 * Skipped: /settings/profile and /settings/password routes do not exist in the system.
 * The User model (gener02 table) uses 'nombre' not 'name', and has no email_verified_at field.
 * No equivalent settings functionality exists in this application.
 */
class ProfileUpdateTest extends TestCase
{
    use NoRefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $this->markTestSkipped('Ruta /settings no existe en el sistema');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $this->markTestSkipped('Ruta /settings no existe en el sistema');
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $this->markTestSkipped('Ruta /settings no existe en el sistema');
    }

    public function test_user_can_delete_their_account(): void
    {
        $this->markTestSkipped('Ruta /settings no existe en el sistema');
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $this->markTestSkipped('Ruta /settings no existe en el sistema');
    }
}
