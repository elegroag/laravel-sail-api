<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Carnet\CarnetDigitalService;
use Illuminate\Support\Facades\Log;

class CarnetVerificacionController extends Controller
{
    public function show(string $token, CarnetDigitalService $carnetDigitalService)
    {
        $resultado = null;
        $disponible = true;

        try {
            $resultado = $carnetDigitalService->verificar($token);
        } catch (\Throwable $e) {
            Log::warning('[CarnetVerificacion] '.$e->getMessage());
            $disponible = false;
        }

        return view('web/carnet/verificar', [
            'resultado' => $resultado,
            'disponible' => $disponible,
        ]);
    }
}
