<?php

namespace App\Services\Api;

use App\Exceptions\DebugException;
use App\Library\APIClient\APIClient;
use App\Library\APIClient\BasicAuth;
use App\Models\ApiEndpoint;
use Illuminate\Support\Facades\Http;

class ApiEpayco extends ApiAbstract
{

    public function __construct()
    {
        $this->mode = config('app.epayco.mode', 'development');
    }

    public function send(array $attr)
    {
        $servicio = $attr['servicio'];
        $metodo = $attr['metodo'] ?? null;
        $params = $attr['params'] ?? null;

        $basicAuth = new BasicAuth(config('app.epayco.public_key'), config('app.epayco.private_key'));

        if (is_null($metodo) || $metodo === '') {
            throw new DebugException('Error no es valido el metodo de acceso API ', 501);
        }

        $endpoint = ApiEndpoint::where('connection_name', 'api-epayco')
            ->where('service_name', $servicio)
            ->first();

        $host = $this->mode == 'development' ? $endpoint->host_dev : $endpoint->host_pro;

        $url = "{$endpoint->endpoint_name}/" . urlencode($metodo);
        $this->setCurlCommand($host, $url, $params, $basicAuth);

        $api = new APIClient($basicAuth, $host, $url);
        $api->setTypeJson(true);
        $this->output = $api->consumeAPI(
            'POST',
            $params
        );

        return $this;
    }


    public function validarReferencia(string $refPayco): array
    {
        $endpoint = ApiEndpoint::where('connection_name', 'api-epayco')
            ->where('service_name', 'Epayco-Reference')
            ->first();

        $host = $this->mode === 'development' ? $endpoint->host_dev :  $endpoint->host_pro;
        $url = $host . "/{$endpoint->endpoint_name}/" . urlencode($refPayco);

        $response = Http::timeout(30)
            ->withoutVerifying()
            ->get($url);

        $status = $response->status();

        if ($status !== 200) {
            return [
                'success' => false,
                'errors' => "ePayco respondio con codigo HTTP: {$status}",
            ];
        }

        $data = $response->json();

        if (! $data || ! isset($data['data'])) {
            return [
                'success' => false,
                'errors' => 'Respuesta invalida de ePayco',
            ];
        }

        $tx = $data['data'];

        return [
            'success' => true,
            'data' => [
                'aprobado' => (int) ($tx['x_cod_transaction_state'] ?? 0) === 1,
                'cod_estado' => intval($tx['x_cod_transaction_state'] ?? 0),
                'respuesta' => $tx['x_response'] ?? 'Sin respuesta',
                'motivo' => $tx['x_response_reason_text'] ?? '',
                'monto' => $tx['x_amount'] ?? '0',
                'ref_payco' => $tx['x_ref_payco'] ?? $refPayco,
            ],
        ];
    }

    public function setCurlCommand(string $hostConnection, string $url, array $params, BasicAuth $basicAuth)
    {
        $token = $basicAuth->authenticate();
        $this->lineaComando = "curl -X POST {$hostConnection}/{$url} \"" .
            " -H 'Content-Type: application/json' " .
            " -H 'Authorization: Basic {$token}'" .
            " -d \"" . json_encode($params) . "\" \"";
    }

    public function generalErrors()
    {
        return [
            'A001' => 'field required: Validación de campos requeridos',
            'A002' => 'field invalid: Validación de campos válidos',
            'A003' => 'field max length: Validación del máximo de caracteres de un campo',
            'A004' => 'code not found: Código no encontrado (Códigos maestros)',
            'A005' => 'email already exist: Correo ya existe en ePayco (creación de cuenta)',
            'A006' => 'restrictive list: Validación de listas restrictivas',
            'A007' => 'error validation: Ocurrió un error en la validación',
            'AL001' => 'URL not send: Validación de campo URL requerido',
            'AL002' => 'URL is required: Validación de campo URL requerido',
            'AL003' => 'The URL structure is wrong: Formato inválido de URL',
            'AED100' => 'La información ingresada no cumple con los parámetros definidos en términos y condiciones. Diligencie el campo de nuevo.'
        ];
    }
}
