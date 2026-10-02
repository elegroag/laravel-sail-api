<?php

namespace App\Services\Carnet\Render;

/**
 * Superficie de dibujo del carnet. Las coordenadas van en píxeles del diseño y cada implementación las escala.
 */
interface CarnetCanvas
{
    public function beginFace(float $width, float $height, float $radius): void;

    public function endFace(): void;

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     */
    public function fillPolygon(array $points, string $color, float $opacity = 1.0): void;

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @param  array<int, array{0: float, 1: string}>  $stops  pares [offset 0..1, color]
     * @param  array{0: float, 1: float, 2: float, 3: float}  $vector  [x1, y1, x2, y2] relativo a la caja del polígono
     */
    public function fillPolygonGradient(array $points, array $stops, array $vector): void;

    public function image(string $path, float $x, float $y, float $width, float $height): void;

    public function text(string $text, float $x, float $centerY, TextStyle $style): void;

    public function textWidth(string $text, TextStyle $style): float;

    /**
     * @param  array<int, array<int, bool>>  $modules  matriz [fila][columna]
     */
    public function qr(array $modules, float $x, float $y, float $size, string $color): void;
}
