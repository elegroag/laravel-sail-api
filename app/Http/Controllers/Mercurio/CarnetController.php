<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\CarnetToken;
use App\Services\Carnet\CarnetArchivoService;
use App\Services\Carnet\CarnetDigitalService;
use App\Services\Carnet\CarnetEmailService;
use Illuminate\Http\JsonResponse;

class CarnetController extends ApplicationController
{
    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->user = session('user') ?? null;
        $this->tipo = session('tipo') ?? null;
    }

    public function index(CarnetDigitalService $carnetDigitalService)
    {
        try {
            if ($this->tipo !== 'T') {
                throw new DebugException('El carnet digital está disponible solo para trabajadores afiliados.', 401);
            }

            $documento = (string) $this->user['documento'];
            $coddoc = (string) $this->user['coddoc'];

            $carnet = $carnetDigitalService->datosCarnet($documento, $coddoc);
            $carnetToken = CarnetToken::obtenerParaAfiliado($coddoc, $documento);

            return view('mercurio/carnet/index', [
                'title' => 'Carnet digital',
                'carnet' => $carnet,
                'qr' => $carnetDigitalService->qrDataUri($carnetDigitalService->urlVerificacion($carnetToken)),
            ]);
        } catch (\Throwable $e) {
            $salida = $this->captureException($e);
            set_flashdata('error', [
                'msj' => $salida['msj'],
                'code' => $e->getCode(),
            ]);

            return redirect()->route('principal.index');
        }
    }

    public function descargar(string $formato, CarnetDigitalService $carnetDigitalService, CarnetArchivoService $carnetArchivoService)
    {
        try {
            [$contenido, $nombre] = $this->generarArchivo($formato, $carnetDigitalService, $carnetArchivoService);

            return response($contenido, 200, [
                'Content-Type' => $formato === 'pdf' ? 'application/pdf' : 'image/png',
                'Content-Disposition' => 'attachment; filename="'.$nombre.'"',
                'Cache-Control' => 'no-store, private',
            ]);
        } catch (\Throwable $e) {
            $salida = $this->captureException($e);
            set_flashdata('error', [
                'msj' => $salida['msj'],
                'code' => $e->getCode(),
            ]);

            return redirect()->route('carnet.index');
        }
    }

    public function enviar(
        string $formato,
        CarnetDigitalService $carnetDigitalService,
        CarnetArchivoService $carnetArchivoService,
        CarnetEmailService $carnetEmailService
    ): JsonResponse {
        try {
            [$contenido, $nombre] = $this->generarArchivo($formato, $carnetDigitalService, $carnetArchivoService);

            $resultado = $carnetEmailService->enviar(
                $this->user ?? [],
                $this->tipo,
                $contenido,
                $nombre,
                $formato === 'pdf' ? 'PDF' : 'imagen PNG'
            );

            return response()->json([
                'success' => true,
                'msj' => 'El carnet se ha enviado a tu correo registrado.',
                'email_masked' => $resultado['email_masked'],
            ]);
        } catch (\Throwable $e) {
            $salida = $this->captureException($e);

            return response()->json([
                'success' => false,
                'msj' => $salida['msj'] ?: 'No se pudo enviar el carnet por correo.',
            ], 422);
        }
    }

    /**
     * @return array{0: string, 1: string} contenido binario y nombre del archivo
     */
    private function generarArchivo(string $formato, CarnetDigitalService $carnetDigitalService, CarnetArchivoService $carnetArchivoService): array
    {
        if ($this->tipo !== 'T') {
            throw new DebugException('El carnet digital está disponible solo para trabajadores afiliados.', 401);
        }

        $documento = (string) $this->user['documento'];
        $coddoc = (string) $this->user['coddoc'];

        $carnet = $carnetDigitalService->datosCarnet($documento, $coddoc);
        $url = $carnetDigitalService->urlVerificacion(CarnetToken::obtenerParaAfiliado($coddoc, $documento));

        $contenido = $formato === 'pdf'
            ? $carnetArchivoService->pdf($carnet, $url)
            : $carnetArchivoService->png($carnet, $url);

        return [$contenido, 'carnet-comfaca-'.preg_replace('/\D/', '', $documento).'.'.$formato];
    }
}
