<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Ecommerce\EpaycoConfirmationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Webhook público de confirmación ePayco (URL confirmation).
 * Sin autenticación JWT: la seguridad es la validación de x_signature.
 */
class EpaycoWebhookController extends Controller
{
    public function __construct(
        protected EpaycoConfirmationService $confirmationService,
    ) {}

    /**
     * POST /api/epayco/confirmation
     */
    public function confirmation(Request $request): Response
    {
        $payload = $request->all();

        if ($payload === []) {
            Log::warning('ePayco confirmation: payload vacio');

            return response('Empty payload', 400);
        }

        try {
            $result = $this->confirmationService->handle($payload);

            return response($result['message'], $result['http']);
        } catch (\Throwable $e) {
            Log::error('ePayco confirmation: excepcion', [
                'error' => $e->getMessage(),
            ]);

            return response('Internal Server Error', 500);
        }
    }
}
