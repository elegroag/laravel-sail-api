<?php

namespace App\Library\APIClient;

use App\Exceptions\DebugException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class APIClient
{
    private AuthClientInterface $auth;

    private string $hostConnection;

    private string $apiUrl;

    private string $statusCode;

    private bool $typeJson = true;

    public function __construct(AuthClientInterface $auth, string $host, string $url)
    {
        $this->auth = $auth;
        $this->apiUrl = $url;
        $this->hostConnection = $host;
    }

    public function consumeAPI(string $method, ?array $request = null): object|array
    {
        $url = rtrim($this->hostConnection, '/') . '/' . ltrim($this->apiUrl, '/');
        $headers = [];

        if ($this->auth instanceof BasicAuth) {
            $this->auth->authenticate();
            if ($this->typeJson) {
                $headers = $this->auth->getHeader();
            }
        }

        if ($this->auth instanceof TokenAuth) {
            $this->auth->authenticate();
            $headers = $this->auth->getHeader();
        }

        $body = $request ?? [];

        #Log::info('ApiSubsidio body' . json_encode($body));
        #Log::info('ApiSubsidio url' . json_encode($url));

        $response = match ($method) {
            'POST' => Http::withHeaders($headers)
                ->timeout(30)
                ->withoutVerifying()
                ->post($url, $body),
            'GET' => Http::withHeaders($headers)
                ->timeout(30)
                ->withoutVerifying()
                ->get($url, $body),
            'PUT' => Http::withHeaders($headers)
                ->timeout(30)
                ->withoutVerifying()
                ->put($url, $body),
            'DELETE' => Http::withHeaders($headers)
                ->timeout(30)
                ->withoutVerifying()
                ->delete($url, $body),
            default => throw new DebugException('Error no está definido el metodo http', 1),
        };

        $this->statusCode = $response->status();
        $bodyResponse = $response->body();

        #Log::info('ApiSubsidio response status' . $this->statusCode);
        #Log::info('ApiSubsidio response body' . $bodyResponse);

        if ($response->failed()) {
            throw new DebugException('Error access api', 501, $bodyResponse);
        }

        if ($this->auth instanceof AuthClientInterface) {
            return $this->auth->procesaRequest($bodyResponse);
        }

        return json_decode($bodyResponse, true);
    }

    public function setTypeJson(?bool $value = null): void
    {
        $this->typeJson = $value;
    }

    public function getStatusCode(): string
    {
        return $this->statusCode;
    }
}
