<?php

namespace App\Services\Cajas\Solicitudes;

use App\Models\Mercurio10;
use App\Models\Mercurio11;
use App\Models\Mercurio12;
use App\Models\Mercurio37;
use Carbon\Carbon;

/**
 * Datos de apoyo de la ficha de una solicitud: seguimiento, adjuntos y catálogos de motivos.
 */
class SolicitudDetalleService
{
    /**
     * @return list<array{item: int, nota: string, estado: string, estado_detalle: string, fecha: ?string}>
     */
    public function seguimiento(string $tipopc, int $id): array
    {
        return Mercurio10::query()
            ->where('tipopc', $tipopc)
            ->where('numero', $id)
            ->orderBy('item')
            ->get()
            ->map(fn (Mercurio10 $row) => [
                'item' => (int) $row->item,
                'nota' => trim(strip_tags((string) $row->nota)),
                'estado' => (string) $row->estado,
                'estado_detalle' => (string) (estado_detalle_value((string) $row->estado) ?? $row->estado),
                'fecha' => $this->fecha($row->fecsis),
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{coddoc: string, detalle: string, archivo: string, url: string}>
     */
    public function adjuntos(string $tipopc, int $id, string $baseUrl): array
    {
        return Mercurio37::query()
            ->join('mercurio12', 'mercurio37.coddoc', '=', 'mercurio12.coddoc')
            ->where('mercurio37.tipopc', $tipopc)
            ->where('mercurio37.numero', $id)
            ->select(['mercurio37.coddoc', 'mercurio37.archivo', 'mercurio12.detalle'])
            ->get()
            ->map(fn ($row) => [
                'coddoc' => (string) $row->coddoc,
                'detalle' => (string) $row->detalle,
                'archivo' => (string) $row->archivo,
                'url' => rtrim($baseUrl, '/').'/archivos/'.rawurlencode((string) $row->coddoc),
            ])
            ->values()
            ->all();
    }

    /**
     * Ruta física y nombre del adjunto, o null si no existe.
     *
     * @return array{path: string, name: string}|null
     */
    public function archivoAdjunto(string $tipopc, int $id, string $coddoc): ?array
    {
        $archivo = Mercurio37::query()
            ->where('tipopc', $tipopc)
            ->where('numero', $id)
            ->where('coddoc', $coddoc)
            ->value('archivo');

        if (! $archivo) {
            return null;
        }

        $name = basename((string) $archivo);
        $path = storage_path('temp/'.$name);

        return is_file($path) ? ['path' => $path, 'name' => $name] : null;
    }

    /**
     * @return list<array{codest: string, detalle: string}>
     */
    public function motivos(): array
    {
        return Mercurio11::query()
            ->orderBy('codest')
            ->get()
            ->map(fn (Mercurio11 $row) => [
                'codest' => (string) $row->codest,
                'detalle' => (string) $row->detalle,
            ])
            ->values()
            ->all();
    }

    /**
     * Documentos/campos que el asesor puede marcar para corrección al devolver.
     *
     * @return list<array{coddoc: string, detalle: string}>
     */
    public function camposCorregibles(): array
    {
        return Mercurio12::query()
            ->orderBy('coddoc')
            ->get()
            ->map(fn (Mercurio12 $row) => [
                'coddoc' => (string) $row->coddoc,
                'detalle' => (string) $row->detalle,
            ])
            ->values()
            ->all();
    }

    private function fecha(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
