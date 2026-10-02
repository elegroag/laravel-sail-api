<?php

namespace App\Services\Carnet\Render;

/**
 * Dibuja el carnet en un PDF de tamaño tarjeta (85,6 × 54 mm), una cara por página.
 */
final class TcpdfCarnetCanvas implements CarnetCanvas
{
    public const CARD_WIDTH_MM = 85.6;

    public const CARD_HEIGHT_MM = 54.0;

    private const MM_PER_PT = 25.4 / 72;

    private const TEXT_FONT = 'helvetica';

    private CarnetTcpdf $pdf;

    private float $scale;

    private string $iconFont;

    /**
     * @param  string  $iconFontDefinition  definición TCPDF (.php) de Font Awesome solid, convertida previamente porque
     *                                      TCPDF_FONTS::addTTFfont falla con fuentes sin el glifo «x»
     */
    public function __construct(string $iconFontDefinition)
    {
        $this->scale = self::CARD_WIDTH_MM / CarnetLayout::WIDTH;

        $this->pdf = new CarnetTcpdf('L', 'mm', [self::CARD_WIDTH_MM, self::CARD_HEIGHT_MM], true, 'UTF-8', false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->SetMargins(0, 0, 0);
        $this->pdf->SetAutoPageBreak(false, 0);
        $this->pdf->setCellPaddings(0, 0, 0, 0);
        $this->pdf->setCellMargins(0, 0, 0, 0);
        $this->pdf->SetCreator('Comfaca En Línea');
        $this->pdf->SetAuthor('Comfaca');
        $this->pdf->SetTitle('Carnet de afiliado');

        $this->iconFont = basename($iconFontDefinition, '.php');
        $this->pdf->AddFont($this->iconFont, '', $iconFontDefinition);
    }

    public function beginFace(float $width, float $height, float $radius): void
    {
        $this->pdf->AddPage();
        $this->pdf->StartTransform();
        $this->pdf->RoundedRect(0, 0, $width * $this->scale, $height * $this->scale, $radius * $this->scale, '1111', 'CNZ');
    }

    public function endFace(): void
    {
        $this->pdf->StopTransform();
    }

    public function fillPolygon(array $points, string $color, float $opacity = 1.0): void
    {
        $this->pdf->SetFillColorArray(self::rgb($color));
        $this->withAlpha($opacity, fn () => $this->pdf->Polygon($this->flatten($points), 'F'));
    }

    public function fillPolygonGradient(array $points, array $stops, array $vector): void
    {
        $xs = array_column($points, 0);
        $ys = array_column($points, 1);
        $box = [
            min($xs) * $this->scale,
            min($ys) * $this->scale,
            (max($xs) - min($xs)) * $this->scale,
            (max($ys) - min($ys)) * $this->scale,
        ];

        $this->pdf->polygonGradient(
            $this->flatten($points),
            $box,
            [$vector[0], 1 - $vector[1], $vector[2], 1 - $vector[3]],
            array_map(fn (array $stop) => ['color' => self::rgb($stop[1]), 'offset' => $stop[0], 'exponent' => 1], $stops)
        );
    }

    public function image(string $path, float $x, float $y, float $width, float $height): void
    {
        $this->pdf->Image($path, $x * $this->scale, $y * $this->scale, $width * $this->scale, $height * $this->scale, 'PNG', '', '', false, 300);
    }

    public function text(string $text, float $x, float $centerY, TextStyle $style): void
    {
        if ($text === '') {
            return;
        }

        $this->useFont($style);
        $this->pdf->SetTextColorArray(self::rgb($style->color));
        $this->pdf->setFontSpacing($style->letterSpacing * $this->scale);
        $this->withAlpha($style->opacity, fn () => $this->pdf->Text(
            $x * $this->scale,
            ($centerY + $style->baselineOffset()) * $this->scale,
            $text,
            0, false, true, 0, 0, '', false, '', 0, false, 'L', 'T'
        ));
        $this->pdf->setFontSpacing(0);
    }

    public function textWidth(string $text, TextStyle $style): float
    {
        if ($text === '') {
            return 0.0;
        }

        $this->useFont($style);
        $this->pdf->setFontSpacing(0);

        return $this->pdf->GetStringWidth($text) / $this->scale + mb_strlen($text) * $style->letterSpacing;
    }

    public function qr(array $modules, float $x, float $y, float $size, string $color): void
    {
        $count = count($modules);
        if ($count === 0) {
            return;
        }

        $module = $size / $count * $this->scale;
        $this->pdf->SetFillColorArray(self::rgb($color));
        foreach (array_values($modules) as $row => $cells) {
            foreach (self::runs($cells) as [$start, $length]) {
                $this->pdf->Rect(
                    $x * $this->scale + $start * $module,
                    $y * $this->scale + $row * $module,
                    $length * $module,
                    $module * 1.02,
                    'F'
                );
            }
        }
    }

    public function output(): string
    {
        return $this->pdf->Output('carnet.pdf', 'S');
    }

    /**
     * Tramos consecutivos de módulos oscuros en una fila, como pares [inicio, longitud].
     *
     * @param  array<int, bool>  $cells
     * @return array<int, array{0: int, 1: int}>
     */
    public static function runs(array $cells): array
    {
        $runs = [];
        $start = null;
        $cells = array_values($cells);
        foreach ($cells as $index => $dark) {
            if ($dark && $start === null) {
                $start = $index;
            } elseif (! $dark && $start !== null) {
                $runs[] = [$start, $index - $start];
                $start = null;
            }
        }
        if ($start !== null) {
            $runs[] = [$start, count($cells) - $start];
        }

        return $runs;
    }

    private function useFont(TextStyle $style): void
    {
        $sizePt = $style->size * $this->scale / self::MM_PER_PT;
        if ($style->font === TextStyle::FONT_ICON) {
            $this->pdf->SetFont($this->iconFont, '', $sizePt);
        } else {
            $this->pdf->SetFont(self::TEXT_FONT, $style->bold ? 'B' : '', $sizePt);
        }
    }

    private function withAlpha(float $opacity, callable $draw): void
    {
        if ($opacity < 1) {
            $this->pdf->setAlpha($opacity);
        }
        $draw();
        if ($opacity < 1) {
            $this->pdf->setAlpha(1);
        }
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @return array<int, float>
     */
    private function flatten(array $points): array
    {
        $flat = [];
        foreach ($points as [$px, $py]) {
            $flat[] = $px * $this->scale;
            $flat[] = $py * $this->scale;
        }

        return $flat;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function rgb(string $color): array
    {
        return sscanf(ltrim($color, '#'), '%02x%02x%02x');
    }
}
