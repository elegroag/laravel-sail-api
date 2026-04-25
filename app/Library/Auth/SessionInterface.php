<?php

namespace App\Library\Auth;

interface SessionInterface
{
    /**
     * Authenticate user with session data
     *
     * @param array $request
     * @return array|false
     */
    public function authenticate(array $request): array|false;
}
