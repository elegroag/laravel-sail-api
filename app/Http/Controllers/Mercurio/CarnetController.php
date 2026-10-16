<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\CarnetToken;
use App\Services\Carnet\CarnetArchivoService;
use App\Services\Carnet\CarnetDigitalService;
use App\Services\Carnet\CarnetEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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
            $mensajeReal = $this->mensajeExcepcionCarnet($e, $salida);

            Log::error('Carnet index falló', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'errors' => $salida['errors'] ?? null,
                'class' => $e::class,
            ]);

            set_flashdata('error', [
                'msj' => config('app.debug')
                    ? $mensajeReal
                    : ($e instanceof DebugException
                        ? $e->getMessage()
                        : 'No fue posible cargar el carnet digital. Intente más tarde.'),
                'code' => $e instanceof DebugException ? $e->getCode() : (int) ($salida['out']['code'] ?? 501),
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

    /**
     * Mensaje útil para flash/log: el genérico de captureException oculta el detalle real.
     *
     * @param  array<string, mixed>  $salida
     */
    private function mensajeExcepcionCarnet(\Throwable $e, array $salida): string
    {
        if ($e instanceof DebugException) {
            return $e->getMessage();
        }

        $errors = $salida['errors'] ?? null;
        if (is_array($errors) && isset($errors[0]) && is_string($errors[0]) && $errors[0] !== '') {
            $archivo = isset($errors[2]) ? (string) $errors[2] : basename($e->getFile());
            $linea = isset($errors[1]) ? (string) $errors[1] : (string) $e->getLine();

            return $errors[0].' ('.$archivo.':'.$linea.')';
        }

        $msg = trim($e->getMessage());

        return $msg !== ''
            ? $msg.' ('.basename($e->getFile()).':'.$e->getLine().')'
            : (string) ($salida['msj'] ?? 'Error al cargar el carnet digital');
    }
}
