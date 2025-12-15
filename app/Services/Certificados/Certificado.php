<?php

namespace App\Services\Certificados;

use TCPDF;
use Illuminate\Support\Facades\View;

class CertificadoPdf extends TCPDF
{
    /**
     * Ruta absoluta de la imagen de fondo (membrete)
     */
    public string $backgroundImagePath = '';

    /**
     * Ruta absoluta del logo
     */
    public string $logoImagePath = '';

    /**
     * Posición/tamaño del logo (mm)
     */
    public float $logoX = 15;
    public float $logoY = 15;
    public float $logoW = 28;

    public string $signatureImagePath = '';
    public string $signatureName = 'YENNY PATRICIA ESTRADA OTALORA';
    public string $signatureTitle = 'Jefe de Aportes y Subsidio';
    public float $signatureImageH = 12;
    public float $signatureLineW = 80;

    /**
     * Dibuja el membrete como imagen de fondo en todas las páginas
     */
    public function Header()
    {
        // Guardar configuración actual de salto de página
        $bMargin = $this->getBreakMargin();
        $autoPageBreak = $this->AutoPageBreak;

        // Desactivar salto automático para dibujar el fondo en toda la página
        $this->SetAutoPageBreak(false, 0);

        // Dibujar la imagen de fondo (membrete) cubriendo toda la hoja
        if (!empty($this->backgroundImagePath) && file_exists($this->backgroundImagePath)) {
            $this->Image(
                $this->backgroundImagePath,
                0,
                0,
                $this->getPageWidth(),
                $this->getPageHeight(),
                'JPG',
                '',
                '',
                false,
                300,
                '',
                false,
                false,
                0
            );
        }

        // Dibujar el logo (encima del membrete)
        if (!empty($this->logoImagePath) && file_exists($this->logoImagePath)) {
            $this->Image(
                $this->logoImagePath,
                $this->logoX,
                $this->logoY,
                $this->logoW,
                0,
                'PNG',
                '',
                '',
                false,
                300,
                '',
                false,
                false,
                0
            );
        }

        // Restaurar salto automático y marcar inicio de contenido
        $this->SetAutoPageBreak($autoPageBreak, $bMargin);
        $this->setPageMark();
    }

    public function Footer()
    {
        $footerHeight = 30;
        $this->SetY(-$footerHeight);

        $pageWidth = $this->getPageWidth();
        $y = $this->GetY();

        $imageH = $this->signatureImageH;
        $lineW = $this->signatureLineW;
        $xLine = ($pageWidth - $lineW) / 2;

        // Línea separadora (similar a signature-line en HTML)
        $yLine = $y + 1;
        $this->Line($xLine, $yLine, $xLine + $lineW, $yLine, [
            'width' => 0.3,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => '2,2',
            'color' => [221, 221, 221],
        ]);

        $this->SetFont('helvetica', 'B', 9);
        $this->SetY($yLine + 2);
        $this->Cell(0, 4, $this->signatureName, 0, 1, 'C', false, '', 0, false, 'T', 'M');

        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 4, $this->signatureTitle, 0, 1, 'C', false, '', 0, false, 'T', 'M');

        // Imagen de firma (debajo del texto, como en la plantilla)
        if (!empty($this->signatureImagePath) && file_exists($this->signatureImagePath)) {
            $this->Image(
                $this->signatureImagePath,
                ($pageWidth - 45) / 2,
                $this->GetY() + -3,
                40,
                0,
                '',
                '',
                '',
                false,
                300,
                '',
                false,
                false,
                0
            );
        }
    }
}

/**
 * Clase para generar certificados en PDF usando TCPDF
 * Recibe un proveedor de datos (CertiTrabajador, CertiEmpresa, etc.)
 */
class Certificado
{
    protected $dataProvider;
    protected $filePath;
    protected $pdf;

    /**
     * Constructor que recibe el proveedor de datos del certificado
     * @param object $dataProvider Objeto que implementa getTemplate(), getData() y getFileName()
     */
    public function __construct($dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->filePath = storage_path('app/certificados/' . $dataProvider->getFileName());
    }

    /**
     * Genera el PDF del certificado
     */
    public function generate(): void
    {
        // Asegurar que existe el directorio de certificados
        $directory = dirname($this->filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Renderizar la plantilla Blade a HTML
        $html = $this->renderTemplate();

        // Crear el PDF con TCPDF
        $this->createPdf($html);
    }

    /**
     * Renderiza la plantilla Blade con los datos del proveedor
     */
    protected function renderTemplate(): string
    {
        $template = $this->dataProvider->getTemplate();
        $data = $this->dataProvider->getData();

        return View::make($template, $data)->render();
    }

    /**
     * Crea el PDF usando TCPDF
     */
    protected function createPdf(string $html): void
    {
        // Crear instancia de TCPDF con soporte de fondo en todas las páginas
        $this->pdf = new CertificadoPdf('P', 'mm', 'LETTER', true, 'UTF-8', false);

        // Configurar el membrete de fondo (todas las páginas)
        $backgroundPath = public_path('img/membrete.jpg');
        $this->pdf->backgroundImagePath = $backgroundPath;

        // Configurar el logo general (todas las páginas)
        $logoPreferred = public_path('img/comfaca.png');
        $logoFallback = public_path('img/logo-min-comfaca.png');
        $this->pdf->logoImagePath = file_exists($logoPreferred) ? $logoPreferred : $logoFallback;

        $signaturePng = public_path('img/firmas/firma-yenny.png');
        $signatureJpg = public_path('img/firma_jefe_yenny.jpg');
        $this->pdf->signatureImagePath = file_exists($signaturePng) ? $signaturePng : $signatureJpg;

        // Configuración del documento
        $this->pdf->SetCreator('Comfaca En Línea');
        $this->pdf->SetAuthor('Comfaca');
        $this->pdf->SetTitle('Certificado');
        $this->pdf->SetSubject('Certificado de Afiliación');

        // Header se usa para dibujar el fondo (membrete)
        $this->pdf->setPrintHeader(true);
        $this->pdf->setPrintFooter(true);

        // Márgenes
        $this->pdf->SetMargins(15, 15, 15);
        $this->pdf->SetAutoPageBreak(true, 32);

        // Agregar página
        $this->pdf->AddPage();

        // Configurar fuente por defecto
        $this->pdf->SetFont('helvetica', '', 11);

        // Escribir el contenido HTML
        $this->pdf->writeHTML($html, true, false, true, false, '');

        // Guardar el archivo PDF
        $this->pdf->Output($this->filePath, 'F');
    }

    /**
     * Retorna la ruta del archivo PDF generado
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Retorna el nombre del archivo para descarga
     */
    public function getDownloadName(): string
    {
        return $this->dataProvider->getFileName();
    }
}
