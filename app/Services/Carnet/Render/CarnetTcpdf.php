<?php

namespace App\Services\Carnet\Render;

use TCPDF;

class CarnetTcpdf extends TCPDF
{
    /**
     * Rellena un polígono con un degradado lineal. TCPDF solo expone degradados rectangulares, por eso se recorta antes con el polígono.
     *
     * @param  array<int, float>  $points  coordenadas planas [x1, y1, x2, y2, ...] en mm
     * @param  array{0: float, 1: float, 2: float, 3: float}  $box  [x, y, ancho, alto] en mm
     * @param  array<int, float>  $coords  vector del degradado en el cuadrado unitario de la caja (origen abajo a la izquierda)
     * @param  array<int, array<string, mixed>>  $stops
     */
    public function polygonGradient(array $points, array $box, array $coords, array $stops): void
    {
        $this->StartTransform();
        $this->Polygon($points, 'CNZ');
        $this->Clip(...$box);
        $this->Gradient(2, $coords, $stops, [], false);
        $this->StopTransform();
    }
}
