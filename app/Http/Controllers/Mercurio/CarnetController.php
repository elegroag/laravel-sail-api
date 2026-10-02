<?php

namespace App\Http\Controllers\Mercurio;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\CarnetToken;
use App\Services\Carnet\CarnetArchivoService;
use App\Services\Carnet\CarnetDigitalService;

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

            $nombre = 'carnet-comfaca-'.preg_replace('/\D/', '', $documento).'.'.$formato;

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
}
