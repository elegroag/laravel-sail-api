<?php

namespace App\Library\APIClient;

use App\Exceptions\AuthException;

class TokenAuth implements AuthClientInterface
{
    private $token;

    private $host;

    private $credentials;

    private $point;

    public function __construct($credentials, $host, $point)
    {
        $this->credentials = $credentials;
        $this->host = $host;
        $this->point = $point;
    }

    public function authenticate()
    {
        return $this->newToken($this->host.''.$this->point);
    }

    public function newToken($endpoint)
    {
        $cur = curl_init();
        $cadena = http_build_query($this->credentials);
        curl_setopt($cur, CURLOPT_URL, $endpoint);
        curl_setopt($cur, CURLOPT_POST, 1);
        curl_setopt($cur, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cur, CURLOPT_POSTFIELDS, $cadena);
        curl_setopt($cur, CURLOPT_HTTPHEADER, $this->getHeader(1));

        $result = curl_exec($cur);
        $statusCode = curl_getinfo($cur, CURLINFO_HTTP_CODE);
        dd($statusCode);
        curl_close($cur);
        $out = $this->procesaRequest($result);
        if ($out) {
            if ($out['response']['status'] == true) {
                $this->token = (isset($out['response']['access_token']) ? $out['response']['access_token'] : $out['response']['token']);

                return $this->token;
            } else {
                throw new AuthException('Error '.$out['response']['message'], 1);
            }
        } else {
            throw new AuthException('Error, no es posible generar el token para el servicio', 1);
        }
    }

    public function getHeader($autenticar = 0)
    {
        if ($autenticar == true) {
            return ['Content-Type: application/x-www-form-urlencoded'];
        } else {
            return [
                'Content-Type: application/json',
                "Authorization: Bearer {$this->token}",
            ];
        }
    }

    /**
     * procesaRequest function
     *
     * @param [string] $result
     * @return array
     */
    public function procesaRequest($result)
    {
        if ($result == '' || is_null($result)) {
            return false;
        }

        $macth = preg_match('/^\{(.*)\:(.*)\}$/', trim($result));
        if ($macth === 0 || $macth === false) {
            $macth = preg_match('/^\[(.*)\:(.*)\]$/', trim($result));
        }
        if ($macth === 0 || $macth === false) {
            return [
                'success' => false,
                'response' => [],
                'errors' => "Error:\n{$result}",
            ];
        } else {
            return json_decode($result, true);
        }
    }
}
