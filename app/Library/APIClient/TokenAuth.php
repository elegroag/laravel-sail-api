<?php

namespace App\Library\APIClient;

use App\Exceptions\AuthException;
use Illuminate\Support\Facades\Http;

class TokenAuth implements AuthClientInterface
{
    private string $token;

    private string $host;

    private ?array $credentials;

    public string $statusCode;

    private string $point;

    public function __construct(
        ?array $credentials,
        string $host,
        string $point
    ) {
        $this->credentials = $credentials;
        $this->host = $host;
        $this->point = $point;
    }

    public function authenticate()
    {
        return $this->newToken($this->host.''.$this->point);
    }

    public function newToken(string $endpoint)
    {
        $response = Http::asForm()
            ->withHeaders($this->getHeader(1))
            ->post($endpoint, $this->credentials);

        $this->statusCode = $response->status();
        $out = $this->procesaRequest($response->body());

        if ($out) {
            if ($out['response']['status'] == true) {
                $this->token = $out['response']['access_token'] ?? $out['response']['token'];

                return $this->token;
            }

            throw new AuthException('Error '.$out['response']['message'], 1);
        }

        throw new AuthException('Error, no es posible generar el token para el servicio', 1);
    }

    public function getHeader($autenticar = 0)
    {
        if ($autenticar == true) {
            return ['Content-Type' => 'application/x-www-form-urlencoded'];
        }

        return [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$this->token}",
        ];
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
}
