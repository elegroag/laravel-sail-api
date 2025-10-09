<?php

namespace App\Library\APIClient;

interface AuthClientInterface
{
    public function authenticate();

    /**
     * procesaRequest function
     *
     * @param [string] $result
     * @return array
     */
    public function procesaRequest($result);

    public function getHeader($autenticar = 0);
}
