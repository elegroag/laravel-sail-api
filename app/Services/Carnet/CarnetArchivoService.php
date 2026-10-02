<?php

namespace App\Services\Carnet;

use App\Services\Carnet\Render\CarnetCanvas;
use App\Services\Carnet\Render\CarnetLayout;
use App\Services\Carnet\Render\GdCarnetCanvas;
use App\Services\Carnet\Render\TcpdfCarnetCanvas;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Genera en el servidor los archivos descargables del carnet (PDF y PNG) con el mismo diseño de la vista.
 */
class CarnetArchivoService
{
    /**
     * @param  array{trabajador: array<string, string>, beneficiarios: array<int, array<string, string>>, consultado: string}  $carnet
     */
    public function pdf(array $carnet, string $urlVerificacion): string
    {
        $canvas = new TcpdfCarnetCanvas($this->font('tcpdf/fasolid900.php'));
        $this->draw($canvas, $carnet, $urlVerificacion);

        return $canvas->output();
    }

    /**
     * @param  array{trabajador: array<string, string>, beneficiarios: array<int, array<string, string>>, consultado: string}  $carnet
     */
    public function png(array $carnet, string $urlVerificacion): string
    {
        $canvas = new GdCarnetCanvas(
            $this->font('LiberationSans-Regular.ttf'),
            $this->font('LiberationSans-Bold.ttf'),
            $this->font('fa-solid-900.ttf')
        );
        $this->draw($canvas, $carnet, $urlVerificacion);

        return $canvas->output();
    }

    /**
     * Matriz de módulos del QR con los mismos parámetros que el QR de la vista.
     *
     * @return array<int, array<int, bool>>
     */
    public static function qrMatrix(string $contenido): array
    {
        $options = new QROptions([
            'eccLevel' => QRCode::ECC_M,
            'addQuietzone' => false,
        ]);

        return (new QRCode($options))->addByteSegment($contenido)->getQRMatrix()->getMatrix(true);
    }

    /**
     * @param  array{trabajador: array<string, string>, beneficiarios: array<int, array<string, string>>, consultado: string}  $carnet
     */
    private function draw(CarnetCanvas $canvas, array $carnet, string $urlVerificacion): void
    {
        $layout = new CarnetLayout($this->logoPath());
        $layout->drawFront($canvas, $carnet['trabajador'], count($carnet['beneficiarios']), $carnet['consultado'], self::qrMatrix($urlVerificacion));
        $layout->drawBack($canvas, $carnet['beneficiarios']);
    }

    private function logoPath(): string
    {
        $retina = public_path('img/comfaca-logo@2x.png');

        return is_file($retina) ? $retina : public_path('img/comfaca-logo.png');
    }

    private function font(string $archivo): string
    {
        return resource_path('fonts/carnet/'.$archivo);
    }
}
