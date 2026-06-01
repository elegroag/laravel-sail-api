<?php

namespace App\Library\APIClient;

interface AuthClientInterface
{
    public function authenticate();

    public function procesaRequest(?string $result = null);

    public function getHeader($autenticar = 0);
}
