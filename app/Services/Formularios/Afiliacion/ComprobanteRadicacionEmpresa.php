<?php

namespace App\Services\Formularios\Afiliacion;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsEmpresa;
use App\Library\Collections\ParamsIndependiente;
use App\Library\Collections\ParamsPensionado;
use App\Services\Formularios\Documento;
use Carbon\Carbon;

class ComprobanteRadicacionEmpresa extends Documento
{
    private $solicitud;

    private string $tipo = 'empresa';

    public function main()
    {
        $this->tipo = strtolower(trim($this->request->getParam('tipo') ?? 'empresa'));
        $this->solicitud = $this->request->getParam('solicitud')
            ?? $this->request->getParam('empresa');

        if (! $this->solicitud) {
            throw new DebugException('Error la solicitud no está disponible', 501);
        }

        $this->pdf->SetTitle('Comprobante de Radicación de Solicitud de Afiliación - COMFACA');
        $this->pdf->SetAuthor($this->getAutor());
        $this->pdf->SetSubject('Comprobante de radicación de solicitud de afiliación');
        $this->pdf->SetCreator('Plataforma Web: comfacaenlinea.com.co, COMFACA');

        match ($this->tipo) {
            'empresa' => $this->renderEmpresa($this->solicitud),
            'independiente' => $this->renderIndependiente($this->solicitud),
            'pensionado' => $this->renderPensionado($this->solicitud),
            default => throw new DebugException("Tipo de comprobante no soportado: {$this->tipo}", 501),
        };

        return $this;
    }

    private function getAutor(): string
    {
        return match ($this->tipo) {
            'empresa' => $this->solicitud->getRepleg() ?? 'COMFACA',
            default => trim($this->solicitud->getNombreCompleto()) ?: 'COMFACA',
        };
    }

    private function renderEncabezado(string $intro): void
    {
        $this->pdf->SetFont('helvetica', 'B', 14);
        $this->pdf->Cell(0, 12, 'Comprobante de Radicación de Solicitud de Afiliación', 0, 1, 'C');
        $this->pdf->Ln(4);

        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->MultiCell(0, 5, $intro, 0, 'J');
        $this->pdf->Ln(6);
    }

    private function renderDatosSolicitud($solicitud): void
    {
        $this->addSectionTitle('Datos de la solicitud');
        $this->addRow('Radicado', $solicitud->ruuid ?? '');
        $this->addRow('Fecha de radicación', $this->formatFecha($solicitud->getFecsol()));
        $this->addRow('Estado', solicitud_estado_detalle($solicitud->getEstado()));
    }

    private function renderPieLegal(): void
    {
        $this->pdf->Ln(8);
        $this->pdf->SetFont('helvetica', 'I', 8);
        $this->pdf->MultiCell(
            0,
            4,
            'Este documento es una constancia de radicación de la solicitud. No constituye aprobación de la afiliación. La solicitud quedará sujeta a verificación por parte de COMFACA.',
            0,
            'J'
        );

        $this->pdf->Ln(4);
        $this->pdf->SetFont('helvetica', '', 8);
        $this->pdf->Cell(0, 4, 'Generado el '.Carbon::now()->format('d/m/Y H:i'), 0, 1, 'R');
    }

    private function renderEmpresa($empresa): void
    {
        $ciudades = ParamsEmpresa::getCiudades();
        $tipoSociedades = ParamsEmpresa::getTipoSociedades();
        $tiposEmpresa = ParamsEmpresa::getTipoEmpresa();

        $ciudad = $ciudades[$empresa->getCodciu()] ?? $empresa->getCodciu();
        $tipoSociedad = $tipoSociedades[$empresa->getTipsoc()] ?? $empresa->getTipsoc();
        $tipoEmpresa = $tiposEmpresa[$empresa->getTipemp()] ?? $empresa->getTipemp();

        $this->renderEncabezado(
            'La Caja de Compensación Familiar del Caquetá - COMFACA, certifica que la siguiente solicitud de afiliación de empresa fue radicada exitosamente a través del sistema Comfaca en Línea.'
        );

        $this->renderDatosSolicitud($empresa);

        $this->addSectionTitle('Datos básicos de la empresa');
        $this->addRow('NIT', trim($empresa->getNit().($empresa->getDigver() ? '-'.$empresa->getDigver() : '')));
        $this->addRow('Razón social', $empresa->getRazsoc());
        $this->addRow('Sigla', $empresa->getSigla());
        $this->addRow('Dígito verificador', $empresa->getDigver());
        $this->addRow('Tipo de sociedad', $tipoSociedad);
        $this->addRow('Tipo de empresa', $tipoEmpresa);

        $this->addSectionTitle('Representante legal');
        $this->addRow('Cédula', $empresa->getCedrep());
        $this->addRow('Nombre', $empresa->getRepleg());

        $this->addSectionTitle('Ubicación y contacto');
        $this->addRow('Dirección', $empresa->getDireccion());
        $this->addRow('Ciudad', $ciudad);
        $this->addRow('Teléfono', $empresa->getTelefono() ?: $empresa->getCelular());
        $this->addRow('Correo electrónico', $empresa->getEmail());

        $this->renderPieLegal();
    }

    private function renderIndependiente($solicitud): void
    {
        $ciudades = ParamsIndependiente::getCiudades();
        $ciudad = $ciudades[$solicitud->getCodciu()] ?? $solicitud->getCodciu();

        $this->renderEncabezado(
            'La Caja de Compensación Familiar del Caquetá - COMFACA, certifica que la siguiente solicitud de afiliación de trabajador independiente fue radicada exitosamente a través del sistema Comfaca en Línea.'
        );

        $this->renderDatosSolicitud($solicitud);

        $this->addSectionTitle('Datos del trabajador independiente');
        $this->addRow('Documento', $solicitud->getDocumento());
        $this->addRow('Cédula', $solicitud->getCedtra());
        $this->addRow('Nombre completo', trim($solicitud->getNombreCompleto()));

        $this->addSectionTitle('Ubicación y contacto');
        $this->addRow('Dirección', $solicitud->getDireccion());
        $this->addRow('Ciudad', $ciudad);
        $this->addRow('Teléfono', $solicitud->getTelefono() ?: $solicitud->getCelular());
        $this->addRow('Correo electrónico', $solicitud->getEmail());

        $this->renderPieLegal();
    }

    private function renderPensionado($solicitud): void
    {
        $ciudades = ParamsPensionado::getCiudades();
        $ciudad = $ciudades[$solicitud->getCodciu()] ?? $solicitud->getCodciu();
        $salario = $solicitud->getSalario();
        $salarioFormateado = is_numeric($salario) ? number_format((float) $salario, 0, ',', '.') : $salario;

        $this->renderEncabezado(
            'La Caja de Compensación Familiar del Caquetá - COMFACA, certifica que la siguiente solicitud de afiliación de pensionado fue radicada exitosamente a través del sistema Comfaca en Línea.'
        );

        $this->renderDatosSolicitud($solicitud);

        $this->addSectionTitle('Datos del pensionado');
        $this->addRow('Cédula', $solicitud->getCedtra());
        $this->addRow('Nombre completo', trim($solicitud->getNombreCompleto()));

        $this->addSectionTitle('Información laboral');
        $this->addRow('Fecha de ingreso', $this->formatFecha($solicitud->getFecing()));
        $this->addRow('Salario', $salarioFormateado);

        $this->addSectionTitle('Ubicación y contacto');
        $this->addRow('Dirección', $solicitud->getDireccion());
        $this->addRow('Ciudad', $ciudad);
        $this->addRow('Teléfono', $solicitud->getTelefono() ?: $solicitud->getCelular());
        $this->addRow('Correo electrónico', $solicitud->getEmail());

        $this->renderPieLegal();
    }

    private function formatFecha(mixed $fecha): string
    {
        if (empty($fecha)) {
            return Carbon::now()->format('d/m/Y');
        }

        if ($fecha instanceof Carbon) {
            return $fecha->format('d/m/Y');
        }

        return Carbon::parse($fecha)->format('d/m/Y');
    }

    private function addSectionTitle(string $title): void
    {
        $this->pdf->Ln(3);
        $this->pdf->SetFont('helvetica', 'B', 10);
        $this->pdf->SetFillColor(230, 230, 230);
        $this->pdf->Cell(0, 7, $title, 0, 1, 'L', true);
        $this->pdf->Ln(1);
    }

    private function addRow(string $label, ?string $value): void
    {
        $this->pdf->SetFont('helvetica', 'B', 9);
        $this->pdf->Cell(55, 6, $label.':', 0, 0, 'L');
        $this->pdf->SetFont('helvetica', '', 9);
        $this->pdf->Cell(0, 6, $value ?? '', 0, 1, 'L');
    }
}
