<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class WebController extends Controller
{
    public function about()
    {
        return Inertia::render('Web/About');
    }

    public function contact()
    {
        return Inertia::render('Web/Contact');
    }

    public function products()
    {
        return Inertia::render('Web/Products');
    }

    public function documentation()
    {
        $videos = [
            [
                'id' => 'afiliacion-empresa',
                'title' => 'Cómo afiliar una empresa',
                'description' => 'Guía paso a paso para registrar su empresa y afiliar trabajadores en el portal COMFACA En Línea.',
                'video' => '/videos/afiliacion-empresa.mp4',
                'duration' => '5:30',
            ],
            [
                'id' => 'afiliacion-trabajador',
                'title' => 'Afiliación de trabajador dependiente',
                'description' => 'Aprenda cómo afiliar a sus empleados de forma rápida y sencilla.',
                'video' => '/videos/afiliacion-trabajador.mp4',
                'duration' => '4:15',
            ],
            [
                'id' => 'novedades',
                'title' => 'Reportar novedades',
                'description' => 'Tutorial para reportar novedades de suspension, retiro o reinicio de empleados.',
                'video' => '/videos/novedades.mp4',
                'duration' => '3:45',
            ],
            [
                'id' => 'consultas',
                'title' => 'Consultar información de afiliados',
                'description' => 'Cómo consultar el estado de sus afiliados, novedad reportadas y pagos.',
                'video' => '/videos/consultas.mp4',
                'duration' => '3:00',
            ],
        ];

        $manuales = [
            [
                'id' => 'manual-empresa',
                'title' => 'Manual para Empleadores',
                'description' => 'Guía completa para la gestión de empresa, afiliación de trabajadores y novedades.',
                'file' => '/manuales/manual-empresa.pdf',
                'size' => '2.4 MB',
            ],
            [
                'id' => 'manual-trabajador',
                'title' => 'Manual para Trabajadores',
                'description' => 'Información sobre afiliados, consulta de estado y servicios disponibles.',
                'file' => '/manuales/manual-trabajador.pdf',
                'size' => '1.8 MB',
            ],
            [
                'id' => 'manual-pensionado',
                'title' => 'Manual para Pensionados',
                'description' => 'Guía para pensionados afiliados a COMFACA y acceso a servicios.',
                'file' => '/manuales/manual-pensionado.pdf',
                'size' => '1.5 MB',
            ],
            [
                'id' => 'guia-rapida',
                'title' => 'Guía Rápida de Uso',
                'description' => 'Resumen de las funciones principales del portal COMFACA En Línea.',
                'file' => '/manuales/guia-rapida.pdf',
                'size' => '950 KB',
            ],
        ];

        return Inertia::render('Web/Documentation', [
            'videos' => $videos,
            'manuales' => $manuales,
        ]);
    }
}
