<?php

namespace Tests\Unit\Services;

use App\Services\Carnet\CarnetArchivoService;
use Tests\TestCase;

class CarnetArchivoServiceTest extends TestCase
{
    private const URL = 'https://comfacaenlinea.com.co/web/carnet/verificar/AbCdEfGhIjKlMnOpQrStUvWx';

    public function test_genera_pdf_de_dos_paginas(): void
    {
        $pdf = (new CarnetArchivoService)->pdf($this->carnet(2), self::URL);

        $this->assertStringStartsWith('%PDF-', $pdf);
        $this->assertSame(2, preg_match_all('/\/Type\s*\/Page[^s]/', $pdf));
    }

    public function test_genera_png_con_ambas_caras(): void
    {
        $png = (new CarnetArchivoService)->png($this->carnet(6), self::URL);

        $this->assertStringStartsWith("\x89PNG\r\n\x1a\n", $png);
        [$width, $height] = getimagesizefromstring($png);
        $this->assertSame(2064, $width);
        $this->assertGreaterThan(2 * 1211, $height);
    }

    public function test_genera_archivos_sin_beneficiarios_ni_categoria(): void
    {
        $carnet = $this->carnet(0);
        $carnet['trabajador']['categoria'] = '';
        $carnet['trabajador']['estado'] = 'I';
        $service = new CarnetArchivoService;

        $this->assertStringStartsWith('%PDF-', $service->pdf($carnet, self::URL));
        $this->assertStringStartsWith("\x89PNG", $service->png($carnet, self::URL));
    }

    public function test_matriz_qr_es_cuadrada(): void
    {
        $matriz = CarnetArchivoService::qrMatrix(self::URL);

        $this->assertNotEmpty($matriz);
        $this->assertCount(count($matriz), $matriz[0]);
    }

    /**
     * @return array{trabajador: array<string, string>, beneficiarios: array<int, array<string, string>>, consultado: string}
     */
    private function carnet(int $beneficiarios): array
    {
        return [
            'trabajador' => [
                'nombre' => 'EDWIN ANDRÉS MUÑOZ CASTAÑEDA',
                'iniciales' => 'EM',
                'tipo_documento' => 'CC',
                'documento' => '1.117.504.321',
                'empresa' => 'CAJA DE COMPENSACION FAMILIAR DEL CAQUETA COMFACA',
                'nit' => '891190047',
                'categoria' => 'B',
                'estado' => 'A',
                'estado_detalle' => 'Activo',
                'fecha_afiliacion' => '15/03/2019',
            ],
            'beneficiarios' => array_map(fn (int $i) => [
                'nombre' => 'MARÍA JOSÉ PEÑA GÓMEZ '.$i,
                'tipo_documento' => 'TI',
                'documento' => '1.117.000.11'.$i,
                'parentesco' => $i === 0 ? 'Cónyuge' : 'Hijo',
                'icono' => $i === 0 ? 'fa-heart' : 'fa-child',
            ], $beneficiarios > 0 ? range(0, $beneficiarios - 1) : []),
            'consultado' => '02/10/2026 10:15 am',
        ];
    }
}
