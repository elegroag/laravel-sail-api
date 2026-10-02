<?php

namespace App\Services\Carnet\Render;

/**
 * Diseño del carnet (frente y reverso) replicado de la vista mercurio/carnet en su versión horizontal.
 * Las posiciones se midieron en el navegador sobre la tarjeta de 640 px y se comparten entre PDF y PNG.
 */
final class CarnetLayout
{
    public const WIDTH = 640.0;

    public const HEIGHT = 640.0 * 54 / 85.6;

    private const RADIUS = 22.0;

    private const GREEN_DARK = '#1f5f36';

    private const GREEN = '#2e8b4e';

    private const LIME = '#8cc63f';

    private const MINT = '#5cc59b';

    private const TURQUOISE = '#22b7bd';

    private const TEAL_TEXT = '#138a8f';

    private const TEXT = '#344767';

    private const MUTED = '#7b8aa0';

    private const WHITE = '#ffffff';

    private const MAX_BENEFICIARY_ROWS = 4;

    private const ICONS = [
        'fa-building' => 0xF1AD,
        'fa-layer-group' => 0xF5FD,
        'fa-user-check' => 0xF4FC,
        'fa-calendar-alt' => 0xF073,
        'fa-users' => 0xF0C0,
        'fa-sync-alt' => 0xF2F1,
        'fa-shield-alt' => 0xF3ED,
        'fa-child' => 0xF1AE,
        'fa-heart' => 0xF004,
        'fa-user' => 0xF007,
    ];

    private const LEGAL = 'Este carnet es personal e intransferible. La información corresponde a la registrada en Comfaca en la fecha de consulta. '
        .'Su validez puede confirmarse escaneando el código QR del frente.';

    public function __construct(private readonly string $logoPath) {}

    /**
     * @param  array<string, string>  $trabajador
     * @param  array<int, array<int, bool>>  $qr
     */
    public function drawFront(CarnetCanvas $canvas, array $trabajador, int $totalBeneficiarios, string $consultado, array $qr): void
    {
        $canvas->beginFace(self::WIDTH, self::HEIGHT, self::RADIUS);
        $canvas->fillPolygon(self::rect(0, 0, self::WIDTH, self::HEIGHT), self::WHITE);

        $canvas->fillPolygonGradient(
            self::path([['M', 0, 0], ['L', 640, 0], ['L', 640, 58], ['C', 560, 88, 470, 44, 380, 62], ['C', 290, 80, 220, 96, 130, 78], ['C', 80, 68, 40, 70, 0, 80]]),
            [[0.0, self::GREEN_DARK], [0.6, self::GREEN], [1.0, self::LIME]],
            [0, 0, 1, 0]
        );
        $canvas->fillPolygon(
            self::path([['M', 380, 62], ['C', 470, 44, 560, 88, 640, 58], ['L', 640, 70], ['C', 560, 98, 470, 56, 380, 74]]),
            self::MINT,
            0.55
        );

        $this->logo($canvas, 22, 16, 96.1, 54, 10, 6);

        $this->textRight($canvas, 'CARNET DE AFILIADO', 618, 25.12, new TextStyle(15.2, self::WHITE, true, 1.216));
        $this->textRight($canvas, 'Trabajador', 618, 44.0, new TextStyle(11.52, self::WHITE, false, 0, 0.9));

        $canvas->fillPolygon(self::circle(55, 145.9, 33), self::TURQUOISE);
        $canvas->fillPolygon(self::circle(55, 145.9, 31), self::WHITE);
        $canvas->fillPolygonGradient(self::circle(55, 145.9, 28), [[0.0, self::TURQUOISE], [1.0, self::GREEN]], [0, 0, 1, 1]);
        $this->textCenter($canvas, $trabajador['iniciales'] ?? '', 55, 145.9, new TextStyle(21.6, self::WHITE, true));

        $nameStyle = new TextStyle(17.92, self::TEXT, true);
        $canvas->text($this->fit($canvas, $trabajador['nombre'] ?? '', $nameStyle, 516), 100, 134.65, $nameStyle);

        $docLabelStyle = new TextStyle(13.6, self::MUTED);
        $docLabel = trim($trabajador['tipo_documento'] ?? '');
        $docLabel = $docLabel !== '' ? $docLabel.' ' : '';
        $canvas->text($docLabel, 100, 157.6, $docLabelStyle);
        $canvas->text($trabajador['documento'] ?? '', 100 + $canvas->textWidth($docLabel, $docLabelStyle), 157.6, new TextStyle(13.6, self::TEXT, true));

        $valueStyle = new TextStyle(12.8, self::TEXT, true);

        $empresa = ($trabajador['empresa'] ?? '') ?: '-';
        if (! empty($trabajador['nit'])) {
            $empresa .= ' · NIT '.$trabajador['nit'];
        }
        $this->fieldLabel($canvas, 24, 207.8, 'fa-building', 'Empresa');
        $canvas->text($this->fit($canvas, $empresa, $valueStyle, 465.8), 24, 227.3, $valueStyle);

        $this->fieldLabel($canvas, 24, 253.8, 'fa-layer-group', 'Categoría');
        if (! empty($trabajador['categoria'])) {
            $this->badge($canvas, 'Categoría '.$trabajador['categoria'], 24, 262.9, self::blend(self::TURQUOISE, 0.14), self::TEAL_TEXT);
        } else {
            $canvas->text('-', 24, 274.3, $valueStyle);
        }

        $this->fieldLabel($canvas, 265.9, 253.8, 'fa-user-check', 'Estado');
        $activo = ($trabajador['estado'] ?? '') === 'A';
        $this->badge(
            $canvas,
            ($trabajador['estado_detalle'] ?? '') ?: '-',
            265.9,
            262.9,
            $activo ? self::blend(self::GREEN, 0.14) : self::blend('#f5365c', 0.12),
            $activo ? self::GREEN : '#c81e45'
        );

        $this->fieldLabel($canvas, 24, 301.8, 'fa-calendar-alt', 'Afiliado desde');
        $canvas->text(($trabajador['fecha_afiliacion'] ?? '') ?: '-', 24, 321.2, $valueStyle);

        $this->fieldLabel($canvas, 265.9, 301.8, 'fa-users', 'Beneficiarios');
        $canvas->text($totalBeneficiarios.' '.($totalBeneficiarios === 1 ? 'registrado' : 'registrados'), 265.9, 321.2, $valueStyle);

        $canvas->qr($qr, 516.9, 238.7, 92, '#000000');
        $this->textCenter($canvas, 'Escanea para verificar', 562.9, 349.6, new TextStyle(9.6, self::MUTED));

        $footerStyle = new TextStyle(10.56, self::MUTED);
        $footerIcon = TextStyle::icon(10.56, self::GREEN);
        $this->iconText($canvas, 'fa-sync-alt', 'Consultado el '.$consultado, 24, 383.1, $footerIcon, $footerStyle);
        $rightText = 'Válido con documento de identidad';
        $rightWidth = $canvas->textWidth(self::glyph('fa-shield-alt'), $footerIcon)
            + $canvas->textWidth(' ', $footerStyle)
            + $canvas->textWidth($rightText, $footerStyle);
        $this->iconText($canvas, 'fa-shield-alt', $rightText, 616 - $rightWidth, 383.1, $footerIcon, $footerStyle);

        $canvas->endFace();
    }

    /**
     * @param  array<int, array<string, string>>  $beneficiarios
     */
    public function drawBack(CarnetCanvas $canvas, array $beneficiarios): void
    {
        $canvas->beginFace(self::WIDTH, self::HEIGHT, self::RADIUS);
        $canvas->fillPolygon(self::rect(0, 0, self::WIDTH, self::HEIGHT), self::WHITE);

        $canvas->fillPolygonGradient(
            self::path([['M', 0, 0], ['L', 640, 0], ['L', 640, 60], ['C', 560, 80, 470, 46, 380, 60], ['C', 290, 74, 200, 82, 110, 70], ['C', 70, 64, 30, 66, 0, 72]]),
            [[0.0, self::LIME], [0.45, self::GREEN], [1.0, self::GREEN_DARK]],
            [0, 0, 1, 0]
        );

        $canvas->text('NÚCLEO FAMILIAR', 22, 25.12, new TextStyle(15.2, self::WHITE, true, 1.216));
        $canvas->text('Beneficiarios a cargo del afiliado', 22, 44.0, new TextStyle(11.52, self::WHITE, false, 0, 0.9));
        $this->logo($canvas, 543.6, 16, 74.4, 42, 10, 6);

        $total = count($beneficiarios);
        $canvas->text(mb_strtoupper("Beneficiarios ({$total})"), 24, 95.36, new TextStyle(12.48, self::GREEN, true, 0.7488));

        $mutedStyle = new TextStyle(11.2, self::MUTED);
        if ($total === 0) {
            $this->textCenter($canvas, 'No registras beneficiarios activos.', 320, 147.8, new TextStyle(13.6, self::MUTED));
        }

        $visibles = $total > self::MAX_BENEFICIARY_ROWS ? array_slice($beneficiarios, 0, self::MAX_BENEFICIARY_ROWS - 1) : $beneficiarios;
        foreach (array_values($visibles) as $index => $beneficiario) {
            $this->beneficiaryRow($canvas, $beneficiario, 112.7 + $index * 54.2, $mutedStyle);
        }
        if ($total > self::MAX_BENEFICIARY_ROWS) {
            $restantes = $total - count($visibles);
            $canvas->text("y {$restantes} beneficiarios más", 68, 112.7 + count($visibles) * 54.2 + 27, $mutedStyle);
        }

        $legalStyle = new TextStyle(9.6, self::MUTED);
        foreach ($this->wrap($canvas, self::LEGAL, $legalStyle, 592) as $line => $texto) {
            $canvas->text($texto, 24, 369.1 + $line * 14.4, $legalStyle);
        }

        $canvas->endFace();
    }

    /**
     * @param  array<string, string>  $beneficiario
     */
    private function beneficiaryRow(CarnetCanvas $canvas, array $beneficiario, float $top, TextStyle $mutedStyle): void
    {
        $canvas->fillPolygon(self::circle(41, $top + 26.6, 17), self::blend(self::LIME, 0.18));
        $this->textCenter($canvas, self::glyph($beneficiario['icono'] ?? 'fa-user'), 41, $top + 26.6, TextStyle::icon(13.6, self::GREEN));

        $pillStyle = new TextStyle(10.88, self::TEAL_TEXT, true);
        $parentesco = $beneficiario['parentesco'] ?? '';
        $pillWidth = $canvas->textWidth($parentesco, $pillStyle) + 16;
        $pillX = 616 - $pillWidth;
        if ($parentesco !== '') {
            $canvas->fillPolygon(self::roundedRect($pillX, $top + 15.7, $pillWidth, 21.7, 10.85), self::blend(self::TURQUOISE, 0.12));
            $canvas->text($parentesco, $pillX + 8, $top + 26.55, $pillStyle);
        }

        $nameStyle = new TextStyle(12.8, self::TEXT, true);
        $canvas->text($this->fit($canvas, $beneficiario['nombre'] ?? '', $nameStyle, $pillX - 10 - 68), 68, $top + 17.43, $nameStyle);
        $documento = trim(($beneficiario['tipo_documento'] ?? '').' '.($beneficiario['documento'] ?? ''));
        $canvas->text($documento, 68, $top + 37.0, $mutedStyle);
    }

    private function logo(CarnetCanvas $canvas, float $x, float $y, float $width, float $height, float $padX, float $padY): void
    {
        foreach ([[6, 0.025], [4, 0.035], [2, 0.045]] as [$spread, $alpha]) {
            $canvas->fillPolygon(self::roundedRect($x - $spread, $y + 4 - $spread, $width + $spread * 2, $height + $spread * 2, 12 + $spread), '#000000', $alpha);
        }
        $canvas->fillPolygon(self::roundedRect($x, $y, $width, $height, 12), self::WHITE);
        $canvas->image($this->logoPath, $x + $padX, $y + $padY, $width - $padX * 2, $height - $padY * 2);
    }

    private function fieldLabel(CarnetCanvas $canvas, float $x, float $centerY, string $icon, string $label): void
    {
        $style = new TextStyle(9.92, self::MUTED, false, 0.5952);
        $canvas->text(self::glyph($icon), $x, $centerY, TextStyle::icon(9.92, self::GREEN));
        $canvas->text(mb_strtoupper($label), $x + 14 + $canvas->textWidth(' ', $style), $centerY, $style);
    }

    private function badge(CarnetCanvas $canvas, string $text, float $x, float $top, string $background, string $color): void
    {
        $style = new TextStyle(11.52, $color, true);
        $width = $canvas->textWidth($text, $style) + 20;
        $canvas->fillPolygon(self::roundedRect($x, $top, $width, 22.8, 11.4), $background);
        $canvas->text($text, $x + 10, $top + 11.4, $style);
    }

    private function iconText(CarnetCanvas $canvas, string $icon, string $text, float $x, float $centerY, TextStyle $iconStyle, TextStyle $style): void
    {
        $glyph = self::glyph($icon);
        $canvas->text($glyph, $x, $centerY, $iconStyle);
        $canvas->text($text, $x + $canvas->textWidth($glyph, $iconStyle) + $canvas->textWidth(' ', $style), $centerY, $style);
    }

    private function textRight(CarnetCanvas $canvas, string $text, float $right, float $centerY, TextStyle $style): void
    {
        $canvas->text($text, $right - $canvas->textWidth($text, $style), $centerY, $style);
    }

    private function textCenter(CarnetCanvas $canvas, string $text, float $centerX, float $centerY, TextStyle $style): void
    {
        $canvas->text($text, $centerX - $canvas->textWidth($text, $style) / 2, $centerY, $style);
    }

    private function fit(CarnetCanvas $canvas, string $text, TextStyle $style, float $maxWidth): string
    {
        if ($canvas->textWidth($text, $style) <= $maxWidth) {
            return $text;
        }
        while (mb_strlen($text) > 1 && $canvas->textWidth($text.'…', $style) > $maxWidth) {
            $text = mb_substr($text, 0, -1);
        }

        return rtrim($text).'…';
    }

    /**
     * @return array<int, string>
     */
    private function wrap(CarnetCanvas $canvas, string $text, TextStyle $style, float $maxWidth): array
    {
        $lines = [];
        $current = '';
        foreach (preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;
            if ($current !== '' && $canvas->textWidth($candidate, $style) > $maxWidth) {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private static function glyph(string $icon): string
    {
        return mb_chr(self::ICONS[$icon] ?? self::ICONS['fa-user'], 'UTF-8');
    }

    /**
     * Color opaco equivalente a superponer $color con la opacidad dada sobre blanco.
     */
    private static function blend(string $color, float $alpha): string
    {
        $rgb = sscanf(ltrim($color, '#'), '%02x%02x%02x');

        return sprintf('#%02x%02x%02x', ...array_map(fn (int $c) => (int) round($c * $alpha + 255 * (1 - $alpha)), $rgb));
    }

    /**
     * @return array<int, array{0: float, 1: float}>
     */
    private static function rect(float $x, float $y, float $width, float $height): array
    {
        return [[$x, $y], [$x + $width, $y], [$x + $width, $y + $height], [$x, $y + $height]];
    }

    /**
     * @return array<int, array{0: float, 1: float}>
     */
    private static function roundedRect(float $x, float $y, float $width, float $height, float $radius): array
    {
        $radius = min($radius, $width / 2, $height / 2);
        $corners = [
            [$x + $width - $radius, $y + $radius, -90],
            [$x + $width - $radius, $y + $height - $radius, 0],
            [$x + $radius, $y + $height - $radius, 90],
            [$x + $radius, $y + $radius, 180],
        ];
        $points = [];
        foreach ($corners as [$cx, $cy, $start]) {
            for ($step = 0; $step <= 12; $step++) {
                $angle = deg2rad($start + $step * 7.5);
                $points[] = [$cx + $radius * cos($angle), $cy + $radius * sin($angle)];
            }
        }

        return $points;
    }

    /**
     * @return array<int, array{0: float, 1: float}>
     */
    private static function circle(float $cx, float $cy, float $radius): array
    {
        $points = [];
        for ($step = 0; $step < 72; $step++) {
            $angle = deg2rad($step * 5);
            $points[] = [$cx + $radius * cos($angle), $cy + $radius * sin($angle)];
        }

        return $points;
    }

    /**
     * Aplana un trazo SVG (M, L y C absolutos) en un polígono.
     *
     * @param  array<int, array<int, string|float|int>>  $commands
     * @return array<int, array{0: float, 1: float}>
     */
    private static function path(array $commands): array
    {
        $points = [];
        $current = [0.0, 0.0];
        foreach ($commands as $command) {
            $type = array_shift($command);
            if ($type === 'C') {
                [$x1, $y1, $x2, $y2, $x, $y] = $command;
                for ($step = 1; $step <= 24; $step++) {
                    $t = $step / 24;
                    $u = 1 - $t;
                    $points[] = [
                        $u ** 3 * $current[0] + 3 * $u ** 2 * $t * $x1 + 3 * $u * $t ** 2 * $x2 + $t ** 3 * $x,
                        $u ** 3 * $current[1] + 3 * $u ** 2 * $t * $y1 + 3 * $u * $t ** 2 * $y2 + $t ** 3 * $y,
                    ];
                }
                $current = [(float) $x, (float) $y];

                continue;
            }
            $current = [(float) $command[0], (float) $command[1]];
            $points[] = $current;
        }

        return $points;
    }
}
