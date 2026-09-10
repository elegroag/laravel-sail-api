<?php

namespace App\Services\Api;

use App\Models\EpaycoCuenta;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente ePayco para validación de referencia y Smart Checkout (Apify).
 * Credenciales de comercio: EpaycoCuenta (DB) vía withCuenta().
 */
class ApiEpayco
{
    protected string $mode;

    protected string $publicKey = '';

    protected string $privateKey = '';

    public function __construct()
    {
        // Mode de comercio se fija con withCuenta(); default solo para validaciones sin cuenta.
        $this->mode = 'development';
    }

    /**
     * Fija llaves y modo desde una cuenta administrada en Cajas.
     */
    public function withCuenta(EpaycoCuenta $cuenta): self
    {
        $clone = clone $this;
        $cuenta->makeVisible(['private_key', 'p_key']);
        $clone->publicKey = (string) $cuenta->public_key;
        $clone->privateKey = (string) $cuenta->private_key;
        $clone->mode = (string) ($cuenta->env_mode ?: 'development');

        return $clone;
    }

    public function isTestMode(): bool
    {
        return $this->mode === 'development';
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Valida un pago consultando la transacción en Apify
     * (POST /payment/transaction con referencePayco).
     *
     * Requiere withCuenta() (PUBLIC/PRIVATE) para obtener el Bearer token.
     *
     * @return array{success: bool, data?: array<string, mixed>, errors?: string}
     */
    public function validarReferenciaApify(string $refPayco): array
    {
        $refPayco = trim($refPayco);
        if ($refPayco === '') {
            return [
                'success' => false,
                'errors' => 'Referencia de pago no proporcionada',
            ];
        }

        $token = $this->obtenerTokenApify();
        if (! $token) {
            return [
                'success' => false,
                'errors' => 'No se pudo autenticar con ePayco (Apify). Verifique la cuenta ePayco en Cajas.',
            ];
        }

        $apifyUrl = rtrim((string) config('app.epayco.apify_url', 'https://apify.epayco.co'), '/');
        $url = $apifyUrl.'/payment/transaction';
        $payload = ['referencePayco' => $refPayco];

        Log::info('ApiEpayco.validarReferenciaApify: request', [
            'ref_payco' => $refPayco,
            'url' => $url,
            'mode' => $this->mode,
            'verify_ssl' => (bool) config('app.epayco.verify_ssl', true),
        ]);

        try {
            $http = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ]);

            if (! config('app.epayco.verify_ssl', true)) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post($url, $payload);
        } catch (\Throwable $e) {
            Log::info('ApiEpayco.validarReferenciaApify: excepcion HTTP', [
                'ref_payco' => $refPayco,
                'url' => $url,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'errors' => 'Error de conexion con ePayco Apify: '.$e->getMessage(),
            ];
        }

        $status = $response->status();
        $bodyRaw = $response->body();
        $bodyLog = mb_strlen($bodyRaw) > 2000
            ? mb_substr($bodyRaw, 0, 2000).'…[truncated]'
            : $bodyRaw;

        Log::info('ApiEpayco.validarReferenciaApify: response', [
            'ref_payco' => $refPayco,
            'url' => $url,
            'http_status' => $status,
            'body' => $bodyLog,
        ]);

        if ($status !== 200) {
            return [
                'success' => false,
                'errors' => "ePayco Apify respondio con codigo HTTP: {$status}",
            ];
        }

        $data = $response->json();
        if (! is_array($data) || $data === []) {
            return [
                'success' => false,
                'errors' => 'Respuesta invalida de ePayco Apify',
            ];
        }

        if (($data['success'] ?? null) === false) {
            $mensaje = $data['textResponse']
                ?? $data['titleResponse']
                ?? 'Transacción no encontrada en ePayco';

            Log::info('ApiEpayco.validarReferenciaApify: transaccion no encontrada o error', [
                'ref_payco' => $refPayco,
                'errors' => $mensaje,
            ]);

            return [
                'success' => false,
                'errors' => is_string($mensaje) ? $mensaje : 'Transacción no encontrada en ePayco',
            ];
        }

        $tx = $data['data'] ?? null;
        if (! is_array($tx) || $tx === []) {
            return [
                'success' => false,
                'errors' => 'Respuesta invalida de ePayco Apify',
            ];
        }

        // Apify a veces anida la tx en data.transaction; otras responde plana en data.
        if (isset($tx['transaction']) && is_array($tx['transaction'])) {
            $tx = $tx['transaction'];
        }

        if (! isset($tx['codTransactionState']) && ! isset($tx['codeResponse']) && ! isset($tx['refPayco'])) {
            return [
                'success' => false,
                'errors' => 'Respuesta invalida de ePayco Apify (sin datos de transaccion)',
            ];
        }

        $codEstado = (int) ($tx['codTransactionState'] ?? $tx['codeResponse'] ?? 0);

        $resultado = [
            'success' => true,
            'data' => [
                'aprobado' => $codEstado === 1,
                'cod_estado' => $codEstado,
                'respuesta' => $tx['status'] ?? $tx['response'] ?? 'Sin respuesta',
                'motivo' => $tx['responseReasonText'] ?? '',
                'monto' => $tx['amount'] ?? $tx['amountOk'] ?? '0',
                'ref_payco' => $tx['refPayco'] ?? $refPayco,
                'x_id_invoice' => $tx['invoice'] ?? null,
                'x_transaction_id' => $tx['transactionId'] ?? null,
                'x_approval_code' => $tx['autorizacion'] ?? null,
                'x_bank_name' => $tx['bank'] ?? null,
                'x_franchise' => $tx['franchise'] ?? null,
                'x_card_number' => $tx['cardNumber'] ?? null,
                'x_quotas' => $tx['quotas'] ?? null,
                'x_currency_code' => $tx['currency'] ?? null,
                'x_date' => $tx['date'] ?? null,
                'x_signature' => $tx['signature'] ?? null,
                'payload_raw' => $tx,
                'origen_consulta' => 'apify',
            ],
        ];

        if ($this->debeForzarAprobacion()) {
            $resultado['data']['aprobado'] = true;
            $resultado['data']['cod_estado'] = 1;
            $resultado['data']['respuesta'] = 'Aceptada (FORCE_APPROVED)';
            $resultado['data']['motivo'] = 'Simulado por EPAYCO_FORCE_APPROVED';
        }

        Log::info('ApiEpayco.validarReferenciaApify: ok', [
            'ref_payco' => $resultado['data']['ref_payco'],
            'aprobado' => $resultado['data']['aprobado'],
            'cod_estado' => $resultado['data']['cod_estado'],
            'respuesta' => $resultado['data']['respuesta'],
        ]);

        return $resultado;
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
        $publicKey = $this->publicKey;
        $privateKey = $this->privateKey;

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
                'errors' => 'No se pudo autenticar con ePayco (Apify). Verifique la cuenta ePayco en Cajas.',
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
