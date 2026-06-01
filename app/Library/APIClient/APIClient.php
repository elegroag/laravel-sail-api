<?php

namespace App\Library\APIClient;

use App\Exceptions\DebugException;

class APIClient
{
    private AuthClientInterface $auth;
    private string $hostConnection;
    private string $apiUrl;
    private string $statusCode;
    private bool $curlHeader = false;
    private bool $curlVerbose = false;
    private bool $typeJson = true;

    public function __construct(AuthClientInterface $auth, string $host, string $url)
    {
        $this->auth = $auth;
        $this->apiUrl = $url;
        $this->hostConnection = $host;
    }

    public function consumeAPI(string $method, ?array $request = null)
    {
        // Aquí va el código para consumir la API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, trim($this->hostConnection . '/' . $this->apiUrl));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, $this->curlHeader);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->curlVerbose);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($this->auth instanceof BasicAuth) {
            $this->auth->authenticate();
            // Colocar headers solo en caso de usar json Request
            if ($this->typeJson === true) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->auth->getHeader());
            }
        }

        if ($this->auth instanceof TokenAuth) {
            $this->auth->authenticate();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->auth->getHeader());
        }

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->typeJson ? json_encode($request) : $request);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_PUT, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->typeJson ? json_encode($request) : $request);
                break;
            case 'GET':
                curl_setopt($ch, CURLOPT_POST, false);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_POST, false);
                break;
            default:
                throw new DebugException('Error no está definido el metodo http', 1);
                break;
        }

        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 250000);
        $result = curl_exec($ch);

        $this->statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (is_null($result) || $this->statusCode >= 400) {
            $error = curl_error($ch);
            unset($ch);
            if ($error) {
                throw new DebugException('Error access api', 501, $error);
            }
        } else {
            unset($ch);
            if ($this->auth instanceof AuthClientInterface) {
                return $this->auth->procesaRequest($result);
            } else {
                return json_decode($result, true);
            }
        }
    }

    /**
     * setTypeJson function
     *
     * @param  bool  $value
     * @return void
     */
    public function setTypeJson($value)
    {
        $this->typeJson = $value;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
