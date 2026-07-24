<?php

namespace App\Services\Signup;

interface SignupInterface
{
    public function findByDocumentTemp(
        int $documento,
        int $coddoc,
        string $calemp = ''
    ): mixed;

    public function createSignupService(?array $data = null): void;

    public function getSolicitud(): mixed;

    public function getTipopc(): ?string;
}
