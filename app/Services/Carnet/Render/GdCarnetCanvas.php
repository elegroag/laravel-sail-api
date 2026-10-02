<?php

namespace App\Services\Carnet\Render;

use GdImage;
use RuntimeException;

/**
 * Dibuja el carnet como PNG con GD. Cada cara se pinta al doble de la resolución final y se reduce para suavizar bordes.
 */
final class GdCarnetCanvas implements CarnetCanvas
{
    private const OUTPUT_SCALE = 3;

    private const SUPERSAMPLE = 2;

    private const PADDING = 72;

    private const GAP = 96;

    private const BACKGROUND = '#eef2f6';

    private float $scale;

    private ?GdImage $face = null;

    private float $faceRadius = 0.0;

    /** @var array<int, GdImage> */
    private array $faces = [];

    /**
     * @param  string  $regularTtf  fuente de texto normal (métrica de Helvetica)
     * @param  string  $boldTtf  fuente de texto en negrita
     * @param  string  $iconTtf  fuente de iconos (Font Awesome solid)
     */
    public function __construct(
        private readonly string $regularTtf,
        private readonly string $boldTtf,
        private readonly string $iconTtf,
    ) {
        $this->scale = self::OUTPUT_SCALE * self::SUPERSAMPLE;
    }

    public function beginFace(float $width, float $height, float $radius): void
    {
        $this->face = imagecreatetruecolor((int) round($width * $this->scale), (int) round($height * $this->scale));
        imagealphablending($this->face, true);
        imagefill($this->face, 0, 0, imagecolorallocate($this->face, 255, 255, 255));
        $this->faceRadius = $radius;
    }

    public function endFace(): void
    {
        $face = $this->requireFace();
        $width = intdiv(imagesx($face), self::SUPERSAMPLE);
        $height = intdiv(imagesy($face), self::SUPERSAMPLE);

        $output = imagecreatetruecolor($width, $height);
        imagealphablending($output, false);
        imagesavealpha($output, true);
        imagecopyresampled($output, $face, 0, 0, 0, 0, $width, $height, imagesx($face), imagesy($face));
        $this->roundCorners($output, $this->faceRadius * self::OUTPUT_SCALE);

        $this->faces[] = $output;
        $this->face = null;
    }

    public function fillPolygon(array $points, string $color, float $opacity = 1.0): void
    {
        $face = $this->requireFace();
        imagefilledpolygon($face, $this->flatten($points), $this->color($color, $opacity));
    }

    public function fillPolygonGradient(array $points, array $stops, array $vector): void
    {
        $face = $this->requireFace();
        $scaled = array_map(fn (array $p) => [$p[0] * $this->scale, $p[1] * $this->scale], $points);
        $xs = array_column($scaled, 0);
        $ys = array_column($scaled, 1);
        $boxX = min($xs);
        $boxY = min($ys);
        $boxW = max(max($xs) - $boxX, 1e-6);
        $boxH = max(max($ys) - $boxY, 1e-6);

        $dx = $vector[2] - $vector[0];
        $dy = $vector[3] - $vector[1];
        $length = $dx * $dx + $dy * $dy ?: 1.0;
        $perColumn = $dy == 0;
        $palette = $this->gradientPalette($face, $stops);
        $last = count($palette) - 1;

        $edges = [];
        $count = count($scaled);
        for ($i = 0; $i < $count; $i++) {
            $edges[] = [$scaled[$i], $scaled[($i + 1) % $count]];
        }

        $width = imagesx($face);
        $height = imagesy($face);
        for ($x = max(0, (int) floor($boxX)); $x <= min($width - 1, (int) ceil($boxX + $boxW)); $x++) {
            $sampleX = $x + 0.5;
            $crossings = [];
            foreach ($edges as [[$x1, $y1], [$x2, $y2]]) {
                if (($x1 <= $sampleX && $x2 > $sampleX) || ($x2 <= $sampleX && $x1 > $sampleX)) {
                    $crossings[] = $y1 + ($sampleX - $x1) * ($y2 - $y1) / ($x2 - $x1);
                }
            }
            sort($crossings);

            $u = ($sampleX - $boxX) / $boxW - $vector[0];
            for ($i = 0; $i + 1 < count($crossings); $i += 2) {
                $top = max(0, (int) round($crossings[$i]));
                $bottom = min($height - 1, (int) round($crossings[$i + 1]) - 1);
                if ($bottom < $top) {
                    continue;
                }
                if ($perColumn) {
                    $t = min(1.0, max(0.0, $u * $dx / $length));
                    imageline($face, $x, $top, $x, $bottom, $palette[(int) round($t * $last)]);

                    continue;
                }
                for ($y = $top; $y <= $bottom; $y++) {
                    $v = ($y + 0.5 - $boxY) / $boxH - $vector[1];
                    $t = min(1.0, max(0.0, ($u * $dx + $v * $dy) / $length));
                    imagesetpixel($face, $x, $y, $palette[(int) round($t * $last)]);
                }
            }
        }
    }

    public function image(string $path, float $x, float $y, float $width, float $height): void
    {
        $face = $this->requireFace();
        $source = @imagecreatefrompng($path);
        if ($source === false) {
            throw new RuntimeException("No fue posible leer la imagen {$path}.");
        }

        imagecopyresampled(
            $face,
            $source,
            (int) round($x * $this->scale),
            (int) round($y * $this->scale),
            0,
            0,
            (int) round($width * $this->scale),
            (int) round($height * $this->scale),
            imagesx($source),
            imagesy($source)
        );
    }

    public function text(string $text, float $x, float $centerY, TextStyle $style): void
    {
        if ($text === '') {
            return;
        }

        $face = $this->requireFace();
        $font = $this->fontFile($style);
        $sizePt = $style->size * $this->scale * 0.75;
        $color = $this->color($style->color, $style->opacity);
        $baseline = (int) round(($centerY + $style->baselineOffset()) * $this->scale);
        $penX = $x * $this->scale;

        if ($style->letterSpacing == 0) {
            imagettftext($face, $sizePt, 0, (int) round($penX), $baseline, $color, $font, $text);

            return;
        }

        foreach (mb_str_split($text) as $char) {
            imagettftext($face, $sizePt, 0, (int) round($penX), $baseline, $color, $font, $char);
            $penX += ($this->measure($char, $style) + $style->letterSpacing) * $this->scale;
        }
    }

    public function textWidth(string $text, TextStyle $style): float
    {
        if ($text === '') {
            return 0.0;
        }

        if ($style->letterSpacing == 0) {
            return $this->measure($text, $style);
        }

        $width = 0.0;
        foreach (mb_str_split($text) as $char) {
            $width += $this->measure($char, $style) + $style->letterSpacing;
        }

        return $width;
    }

    public function qr(array $modules, float $x, float $y, float $size, string $color): void
    {
        $face = $this->requireFace();
        $count = count($modules);
        if ($count === 0) {
            return;
        }

        $module = $size / $count * $this->scale;
        $originX = $x * $this->scale;
        $originY = $y * $this->scale;
        $fill = $this->color($color);
        foreach (array_values($modules) as $row => $cells) {
            foreach (TcpdfCarnetCanvas::runs($cells) as [$start, $length]) {
                imagefilledrectangle(
                    $face,
                    (int) round($originX + $start * $module),
                    (int) round($originY + $row * $module),
                    (int) round($originX + ($start + $length) * $module) - 1,
                    (int) round($originY + ($row + 1) * $module) - 1,
                    $fill
                );
            }
        }
    }

    /**
     * Apila las caras dibujadas sobre un fondo claro y devuelve el PNG.
     */
    public function output(): string
    {
        if ($this->faces === []) {
            throw new RuntimeException('No hay caras del carnet para exportar.');
        }

        $width = max(array_map('imagesx', $this->faces)) + self::PADDING * 2;
        $height = array_sum(array_map('imagesy', $this->faces)) + self::GAP * (count($this->faces) - 1) + self::PADDING * 2;

        $sheet = imagecreatetruecolor($width, $height);
        imagealphablending($sheet, true);
        imagefill($sheet, 0, 0, $this->color(self::BACKGROUND, 1.0, $sheet));

        $top = self::PADDING;
        foreach ($this->faces as $face) {
            imagecopy($sheet, $face, self::PADDING, $top, 0, 0, imagesx($face), imagesy($face));
            $top += imagesy($face) + self::GAP;
        }

        ob_start();
        imagepng($sheet, null, 6);

        return (string) ob_get_clean();
    }

    /**
     * Ancho en píxeles del diseño. Se mide entre dos barras para incluir los espacios iniciales o finales.
     */
    private function measure(string $text, TextStyle $style): float
    {
        $font = $this->fontFile($style);
        $sizePt = $style->size * $this->scale * 0.75;
        if ($style->font === TextStyle::FONT_ICON) {
            $box = imagettfbbox($sizePt, 0, $font, $text);

            return ($box[2] - $box[0]) / $this->scale;
        }

        $wrapped = imagettfbbox($sizePt, 0, $font, '|'.$text.'|');
        $bars = imagettfbbox($sizePt, 0, $font, '||');

        return (($wrapped[2] - $wrapped[0]) - ($bars[2] - $bars[0])) / $this->scale;
    }

    private function fontFile(TextStyle $style): string
    {
        if ($style->font === TextStyle::FONT_ICON) {
            return $this->iconTtf;
        }

        return $style->bold ? $this->boldTtf : $this->regularTtf;
    }

    /**
     * Vuelve transparentes las esquinas de la cara con borde suavizado.
     */
    private function roundCorners(GdImage $image, float $radius): void
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $size = (int) ceil($radius);
        $centers = [
            [$radius, $radius, 0, 0],
            [$width - $radius, $radius, $width - $size, 0],
            [$radius, $height - $radius, 0, $height - $size],
            [$width - $radius, $height - $radius, $width - $size, $height - $size],
        ];

        foreach ($centers as [$cx, $cy, $startX, $startY]) {
            for ($y = $startY; $y < $startY + $size; $y++) {
                for ($x = $startX; $x < $startX + $size; $x++) {
                    $distance = hypot($x + 0.5 - $cx, $y + 0.5 - $cy) - $radius;
                    $isOutside = ($x + 0.5 < $radius || $x + 0.5 > $width - $radius) && ($y + 0.5 < $radius || $y + 0.5 > $height - $radius);
                    if (! $isOutside || $distance <= -0.5) {
                        continue;
                    }
                    $coverage = max(0.0, min(1.0, 0.5 - $distance));
                    $rgba = imagecolorat($image, $x, $y);
                    $alpha = 127 - (int) round((127 - (($rgba >> 24) & 0x7F)) * $coverage);
                    imagesetpixel($image, $x, $y, ($alpha << 24) | ($rgba & 0xFFFFFF));
                }
            }
        }
    }

    /**
     * @param  array<int, array{0: float, 1: string}>  $stops
     * @return array<int, int>
     */
    private function gradientPalette(GdImage $image, array $stops): array
    {
        $palette = [];
        $steps = 512;
        for ($i = 0; $i <= $steps; $i++) {
            $t = $i / $steps;
            $previous = $stops[0];
            $next = $stops[count($stops) - 1];
            foreach ($stops as $index => $stop) {
                if ($stop[0] >= $t) {
                    $next = $stop;
                    $previous = $stops[max(0, $index - 1)];
                    break;
                }
            }
            $span = $next[0] - $previous[0];
            $local = $span > 0 ? ($t - $previous[0]) / $span : 0.0;
            $from = self::rgb($previous[1]);
            $to = self::rgb($next[1]);
            $palette[] = imagecolorallocate(
                $image,
                (int) round($from[0] + ($to[0] - $from[0]) * $local),
                (int) round($from[1] + ($to[1] - $from[1]) * $local),
                (int) round($from[2] + ($to[2] - $from[2]) * $local)
            );
        }

        return $palette;
    }

    private function color(string $color, float $opacity = 1.0, ?GdImage $image = null): int
    {
        [$r, $g, $b] = self::rgb($color);

        return imagecolorallocatealpha($image ?? $this->requireFace(), $r, $g, $b, (int) round(127 * (1 - $opacity)));
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @return array<int, int>
     */
    private function flatten(array $points): array
    {
        $flat = [];
        foreach ($points as [$x, $y]) {
            $flat[] = (int) round($x * $this->scale);
            $flat[] = (int) round($y * $this->scale);
        }

        return $flat;
    }

    private function requireFace(): GdImage
    {
        if ($this->face === null) {
            throw new RuntimeException('Debe iniciarse una cara antes de dibujar.');
        }

        return $this->face;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function rgb(string $color): array
    {
        return sscanf(ltrim($color, '#'), '%02x%02x%02x');
    }
}
