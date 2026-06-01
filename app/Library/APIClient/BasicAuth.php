<?php

namespace App\Library\APIClient;

class BasicAuth implements AuthClientInterface
{
    private string $username;

    private string $password;

    private string $token;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function encript(string $encryption, string $portal_clave)
    {
        $iv = '';
        $seed = str_split('1234567890123456');
        shuffle($seed);
        foreach (array_rand($seed, 16) as $k) {
            $iv .= $seed["$k"];
        }
        $encryptedData = openssl_encrypt($this->password, $encryption, $portal_clave, 0, $iv);
        $this->password = "{$encryptedData}||{$iv}";
    }

    public function authenticate()
    {
        $this->token = base64_encode("$this->username:$this->password");

        return $this->token;
    }

    public function procesaRequest(?string $result = null)
    {
        if ($result === '' || is_null($result)) {
            return [
                'success' => false,
                'response' => [],
                'errors' => 'Respuesta vacía',
            ];
        }

        if (is_array($result)) {
            return $result;
        }

        $decoded = json_decode($result, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($decoded)) {
                return $decoded;
            }

            return [
                'success' => true,
                'response' => $decoded,
                'errors' => [],
            ];
        }

        return [
            'success' => false,
            'response' => [],
            'errors' => $result,
        ];
    }

    public function getHeader($autenticar = 0)
    {
        if ($autenticar == true) {
            return ['Content-Type' => 'application/x-www-form-urlencoded'];
        }

        return [
            'Content-Type' => 'application/json',
            'Authorization' => "Basic {$this->token}",
        ];
    }

    public function getToken()
    {
        return $this->token;
    }
}
