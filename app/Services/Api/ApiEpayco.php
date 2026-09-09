<?php

namespace App\Services\Api;

use App\Models\ApiEndpoint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Cliente ePayco para validación de referencia y Smart Checkout (Apify).
 * No hereda ApiAbstract: el flujo vigente no usa send()/BasicAuth en el consumo.
 */
class ApiEpayco
{
    protected string $mode;

    public function __construct()
    {
        $this->mode = (string) (config('app.epayco.mode') ?: 'development');
    }

    public function validarReferencia(string $refPayco): array
    {
        $endpoint = ApiEndpoint::where('connection_name', 'api-epayco')
            ->where('service_name', 'Epayco-Reference')
            ->first();

        if (! $endpoint) {
            return [
                'success' => false,
                'errors' => 'No existe configuración de endpoint para validar pagos ePayco (Epayco-Reference)',
            ];
        }

        $host = $this->mode === 'development' ? $endpoint->host_dev : $endpoint->host_pro;
        $url = $host."/{$endpoint->endpoint_name}/".urlencode($refPayco);

        try {
            $http = Http::timeout(30);

            if (! config('app.epayco.verify_ssl', true)) {
                $http = $http->withoutVerifying();
            }

            $response = $http->get($url);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'errors' => 'Error de conexion con ePayco: '.$e->getMessage(),
            ];
        }

        $status = $response->status();

        if ($status !== 200) {
            return [
                'success' => false,
                'errors' => "ePayco respondio con codigo HTTP: {$status}",
            ];
        }

        $data = $response->json();

        if (! is_array($data) || $data === []) {
            return [
                'success' => false,
                'errors' => 'Respuesta invalida de ePayco',
            ];
        }

        if ($this->esRespuestaErrorEpayco($data)) {
            return [
                'success' => false,
                'errors' => $this->mensajeErrorEpayco($data),
            ];
        }

        if (! isset($data['data']) || ! is_array($data['data'])) {
            return [
                'success' => false,
                'errors' => 'Respuesta invalida de ePayco',
            ];
        }

        $tx = $data['data'];

        // data presente pero sin campos de transacción (p. ej. {} o error parcial)
        if (! isset($tx['x_cod_transaction_state']) && ! isset($tx['x_cod_respuesta'])) {
            return [
                'success' => false,
                'errors' => $this->mensajeErrorEpayco($data),
            ];
        }

        $codEstado = (int) ($tx['x_cod_transaction_state'] ?? $tx['x_cod_respuesta'] ?? 0);

        $resultado = [
            'success' => true,
            'data' => [
                'aprobado' => $codEstado === 1,
                'cod_estado' => $codEstado,
                'respuesta' => $tx['x_response'] ?? $tx['x_respuesta'] ?? 'Sin respuesta',
                'motivo' => $tx['x_response_reason_text'] ?? '',
                'monto' => $tx['x_amount'] ?? '0',
                'ref_payco' => $tx['x_ref_payco'] ?? $refPayco,
                'x_id_invoice' => $tx['x_id_invoice'] ?? $tx['x_id_factura'] ?? null,
                'x_transaction_id' => $tx['x_transaction_id'] ?? null,
                'x_approval_code' => $tx['x_approval_code'] ?? null,
                'x_bank_name' => $tx['x_bank_name'] ?? null,
                'x_franchise' => $tx['x_franchise'] ?? null,
                'x_card_number' => $tx['x_card_number'] ?? $tx['x_cardnumber'] ?? null,
                'x_quotas' => $tx['x_quotas'] ?? null,
                'x_currency_code' => $tx['x_currency_code'] ?? null,
                'x_date' => $tx['x_date'] ?? $tx['x_transaction_date'] ?? $tx['x_fecha_transaccion'] ?? null,
                'x_signature' => $tx['x_signature'] ?? null,
                'payload_raw' => $tx,
            ],
        ];

        if ($this->debeForzarAprobacion()) {
            $resultado['data']['aprobado'] = true;
            $resultado['data']['cod_estado'] = 1;
            $resultado['data']['respuesta'] = 'Aceptada (FORCE_APPROVED)';
            $resultado['data']['motivo'] = 'Simulado por EPAYCO_FORCE_APPROVED';
        }

        return $resultado;
    }

    /**
     * Detecta envelopes de error de ePayco (HTTP 200 con fallo de datos/conexión).
     *
     * Ejemplos:
     * - {"status":false,"message":"...","data":{"status":"error","description":"..."}}
     * - {"status":"error","description":"..."}
     *
     * @param  array<string, mixed>  $payload
     */
    protected function esRespuestaErrorEpayco(array $payload): bool
    {
        $status = $payload['status'] ?? null;
        if ($status === false || $status === 'error') {
            return true;
        }

        if (($payload['success'] ?? null) === false) {
            return true;
        }

        $inner = $payload['data'] ?? null;
        if (is_array($inner) && ($inner['status'] ?? null) === 'error') {
            return true;
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function mensajeErrorEpayco(array $payload): string
    {
        $inner = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        $mensaje = $inner['description']
            ?? $payload['description']
            ?? $payload['message']
            ?? $payload['textResponse']
            ?? null;

        if (is_string($mensaje) && trim($mensaje) !== '') {
            return trim($mensaje);
        }

        return 'Error de datos o conexion con ePayco';
    }

    /**
     * Bandera temporal de QA: solo aplica fuera de production (APP_ENV y APP_MODE).
     */
    public function debeForzarAprobacion(): bool
    {
        if (! config('app.epayco.force_approved', false)) {
            return false;
        }

        $env = (string) config('app.env', 'production');
        $appMode = (string) config('app.app_mode', 'production');

        return $env !== 'production' && $appMode !== 'production';
    }

    /**
     * Autentica contra Apify (Basic Auth con PUBLIC_KEY:PRIVATE_KEY) y retorna un
     * token Bearer para crear sesiones de Smart Checkout v2. El token se cachea
     * segun su expiracion (JWT corto) para no re-loguear en cada pago.
     */
    public function obtenerTokenApify(): ?string
    {
        $publicKey = (string) config('app.epayco.public_key');
        $privateKey = (string) config('app.epayco.private_key');

        if ($publicKey === '' || $privateKey === '') {
            return null;
        }

        $cacheKey = 'epayco_apify_token_'.md5($publicKey);
        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $apifyUrl = rtrim((string) config('app.epayco.apify_url', 'https://apify.epayco.co'), '/');
        $basic = base64_encode($publicKey.':'.$privateKey);

        try {
            $http = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic '.$basic,
            ]);

            if (! config('app.epayco.verify_ssl', true)) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post($apifyUrl.'/login');
        } catch (\Throwable $e) {
            return null;
        }

        if ($response->status() !== 200) {
            return null;
        }

        $token = $response->json()['token'] ?? null;
        if (! is_string($token) || $token === '') {
            return null;
        }

        Cache::put($cacheKey, $token, $this->ttlDesdeJwt($token, 300));

        return $token;
    }

    /**
     * Crea una sesion de Smart Checkout v2 en Apify y retorna el sessionId.
     *
     * @param  array<string, mixed>  $datos  Propiedades de la sesion (name, amount, currency, response, confirmation, extras, billing, ...)
     * @return array{success: bool, sessionId?: string, errors?: string}
     */
    public function crearSesionCheckout(array $datos): array
    {
        $token = $this->obtenerTokenApify();
        if (! $token) {
            return [
                'success' => false,
                'errors' => 'No se pudo autenticar con ePayco (Apify). Verifique EPAYCO_PUBLIC_KEY/EPAYCO_PRIVATE_KEY.',
            ];
        }

        $apifyUrl = rtrim((string) config('app.epayco.apify_url', 'https://apify.epayco.co'), '/');
        $payload = array_merge(['checkout_version' => '2'], $datos);

        try {
            $http = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ]);

            if (! config('app.epayco.verify_ssl', true)) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post($apifyUrl.'/payment/session/create', $payload);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'errors' => 'Error de conexion con ePayco: '.$e->getMessage(),
            ];
        }

        if ($response->status() !== 200) {
            return [
                'success' => false,
                'errors' => "ePayco respondio con codigo HTTP: {$response->status()}",
            ];
        }

        $data = $response->json();
        $sessionId = $data['data']['sessionId'] ?? null;

        if (! ($data['success'] ?? false) || ! $sessionId) {
            return [
                'success' => false,
                'errors' => $data['textResponse'] ?? 'No se pudo crear la sesion de checkout',
            ];
        }

        return [
            'success' => true,
            'sessionId' => (string) $sessionId,
        ];
    }

    /**
     * Calcula el TTL (segundos) de cacheo a partir del claim exp del JWT.
     */
    protected function ttlDesdeJwt(string $jwt, int $default = 300): int
    {
        $parts = explode('.', $jwt);
        if (count($parts) < 2) {
            return $default;
        }

        $payload = json_decode($this->base64UrlDecode($parts[1]), true);
        $exp = (int) ($payload['exp'] ?? 0);
        if ($exp <= 0) {
            return $default;
        }

        $ttl = $exp - time() - 30; // margen de seguridad

        return $ttl > 0 ? $ttl : $default;
    }

    protected function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return (string) base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Catálogo de referencia: códigos generales de validación/parámetros de ePayco.
     * No se usa en runtime; el manejo de errores toma el mensaje de la respuesta.
     */
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
            'AED100' => 'La información ingresada no cumple con los parámetros definidos en términos y condiciones. Diligencie el campo de nuevo.',
        ];
    }
}
