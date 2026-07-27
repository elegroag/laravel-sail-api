<?php

namespace App\Services\Reports;

use App\Models\Mercurio10;
use App\Support\AfiliacionNormalizer;
use App\Support\AuditoriaSolicitudFieldsBuilder;
use App\Support\AuditoriaSolicitudResolver;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TCPDF;
use Throwable;

class InformeSolicitudPdfService
{
    public function __construct(
        protected AuditoriaSolicitudFieldsBuilder $fieldsBuilder
    ) {}

    /**
     * @return array{path: string, filename: string, payload: array<string, mixed>}
     */
    public function generate(string $tipopc, string $ruuid): array
    {
        $payload = $this->buildPayload($tipopc, $ruuid);
        $filename = 'informe_solicitud_'.$payload['cabecera']['ruuid'].'.pdf';
        $directory = storage_path('app/informes');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = $directory.DIRECTORY_SEPARATOR.$filename;
        $html = View::make('oficios.informes.tmp_solicitud_trazabilidad', $payload)->render();
        $this->writePdf($html, $path, $payload['cabecera']['ruuid']);

        return [
            'path' => $path,
            'filename' => $filename,
            'payload' => $payload,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function buildPayload(string $tipopc, string $ruuid): array
    {
        $tipopc = (string) $tipopc;
        $solicitud = AuditoriaSolicitudResolver::resolveByRuuid($tipopc, $ruuid);

        if ($solicitud === null) {
            throw new NotFoundHttpException('No se encontró la solicitud con el RUUID y tipo indicados.');
        }

        $config = $this->buildNormalizerConfig($tipopc);
        $normalized = AfiliacionNormalizer::normalize($solicitud, (int) $tipopc, $config, []);
        $label = AuditoriaSolicitudResolver::labelsInforme()[$tipopc]
            ?? ($config['label'] ?? 'Solicitud');

        $archivado = AuditoriaSolicitudResolver::isArchivado($solicitud);
        $deletedAt = $archivado
            ? $this->formatEventoFecha($solicitud->deleted_at ?? null)
            : null;

        $cabecera = [
            'tipopc' => $tipopc,
            'tipo_label' => $label,
            'ruuid' => (string) ($normalized['ruuid'] ?? $ruuid),
            'id' => $normalized['id'] ?? $solicitud->id ?? null,
            'estado' => $normalized['estado'] ?? '',
            'fecsol' => $normalized['fecsol'] ?? null,
            'fecapr' => $normalized['fecapr'] ?? null,
            'fecha_cierre' => $normalized['fecha_cierre'] ?? null,
            'nombre' => $normalized['nombre'] ?? '',
            'documento' => $normalized['documento'] ?? '',
            'tipdoc' => $normalized['tipdoc'] ?? '',
            'nit' => $normalized['nit'] ?? '',
            'razsoc' => $normalized['razsoc'] ?? '',
            'archivado' => $archivado,
            'vigencia' => $archivado ? 'Archivado' : 'Vigente',
            'vigencia_nota' => $archivado
                ? 'Registro archivado: proviene de auditoría y ya no se tiene en cuenta para procesos de afiliación.'
                    .($deletedAt ? " Fecha de archivo: {$deletedAt}." : '')
                : 'Registro vigente: la solicitud está activa en el sistema y aplica para procesos de afiliación.',
            'deleted_at' => $deletedAt,
        ];

        return [
            'fecha' => now()->format('Y-m-d H:i'),
            'cabecera' => $cabecera,
            'campos' => $this->fieldsBuilder->build($tipopc, $solicitud),
            'eventos' => $this->buildEventos($tipopc, (int) $cabecera['id'], $cabecera['fecsol']),
        ];
    }

    /**
     * @return array<int, array{fecha: string, estado: string, nota: string}>
     */
    public function buildEventos(string $tipopc, int $numero, ?string $fecsol): array
    {
        $rows = Mercurio10::query()
            ->where('tipopc', $tipopc)
            ->where('numero', $numero)
            ->orderBy('item')
            ->get();

        if ($rows->isEmpty()) {
            return [[
                'ruuid' => '',
                'fecha' => $fecsol ?: now()->format('Y-m-d'),
                'estado' => 'Radicado/enviado',
                'nota' => 'Solicitud enviada. Sin eventos de seguimiento registrados.',
            ]];
        }

        return $rows->map(function (Mercurio10 $row): array {
            $detalle = $row->getDetalleEstado();

            return [
                'ruuid' => trim((string) ($row->getRuuid() ?? '')),
                'fecha' => $this->formatEventoFecha($row->getFecsis()),
                'estado' => is_string($detalle) && $detalle !== '' ? $detalle : (string) $row->getEstado(),
                'nota' => trim(strip_tags((string) $row->getNota())),
            ];
        })->all();
    }

    private function formatEventoFecha(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return substr((string) $value, 0, 10);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildNormalizerConfig(string $tipopc): array
    {
        $label = AuditoriaSolicitudResolver::labelsInforme()[$tipopc] ?? 'SOLICITUD';
        $tipopcInt = (int) $tipopc;

        return [
            'label' => strtoupper($label),
            'doc_field' => match ($tipopcInt) {
                2 => 'nit',
                3 => 'cedcon',
                4 => 'numdoc',
                5, 6, 14 => 'documento',
                8, 13 => 'cedtra',
                default => 'cedtra',
            },
            'afiliacion_field' => 'fecapr',
            'titular_field' => in_array($tipopcInt, [3, 4], true) ? 'cedtra' : null,
        ];
    }

    private function writePdf(string $html, string $path, string $ruuid): void
    {
        $pdf = new TCPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);
        $pdf->SetCreator('Comfaca En Línea');
        $pdf->SetAuthor('Comfaca');
        $pdf->SetTitle('Informe de solicitud '.$ruuid);
        $pdf->SetSubject('Informe de solicitud con trazabilidad');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);
        $pdf->SetFooterMargin(12);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 18);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        try {
            $pdf->writeHTML($html, true, false, true, false, '');
        } catch (Throwable $e) {
            throw new \RuntimeException('Error generando el PDF del informe: '.$e->getMessage(), 0, $e);
        }

        $pdf->Output($path, 'F');
    }
}
