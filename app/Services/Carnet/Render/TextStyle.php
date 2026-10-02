<?php

namespace App\Services\Carnet\Render;

/**
 * Estilo de un texto del carnet. Las medidas van en píxeles del diseño (tarjeta de 640 px de ancho).
 */
final class TextStyle
{
    public const FONT_TEXT = 'text';

    public const FONT_ICON = 'icon';

    public function __construct(
        public readonly float $size,
        public readonly string $color,
        public readonly bool $bold = false,
        public readonly float $letterSpacing = 0.0,
        public readonly float $opacity = 1.0,
        public readonly string $font = self::FONT_TEXT,
    ) {}

    public static function icon(float $size, string $color): self
    {
        return new self($size, $color, false, 0.0, 1.0, self::FONT_ICON);
    }

    /**
     * Distancia del centro vertical de la línea a la línea base, según las métricas de cada fuente.
     */
    public function baselineOffset(): float
    {
        return $this->size * ($this->font === self::FONT_ICON ? 0.375 : 0.3465);
    }
}
