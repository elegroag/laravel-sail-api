<?php

namespace App\Services\Entidades;

use App\Models\Adapter\DbBase;
use App\Models\Mercurio30;
use App\Models\Mercurio36;
use App\Models\Mercurio38;
use App\Models\Mercurio41;

class ParticularService
{
    private $user;

    private $db;

    private $tipo;

    public function __construct()
    {
        $this->user = session('user');
        $this->tipo = session('tipo');
        $this->db = DbBase::rawConnect();
    }

    public function resumenServicios()
    {
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];
        $tipo = $this->tipo;

        return [
            'afiliacion' => [
                [
                    'name' => 'Solicitudes Empresas',
                    'cantidad' => [
                        'pendientes' => Mercurio30::where(['estado' => 'P', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'aprobados' => Mercurio30::where(['estado' => 'A', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'rechazados' => Mercurio30::where(['estado' => 'R', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'devueltos' => Mercurio30::where(['estado' => 'D', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'temporales' => Mercurio30::where(['estado' => 'T', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                    ],
                    'icon' => 'E',
                    'url' => 'empresa/index',
                    'imagen' => 'empresas.jpg',
                ],
                [
                    'name' => 'Solicitud Trabajador independiente',
                    'cantidad' => [
                        'pendientes' => Mercurio41::where(['estado' => 'P', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'aprobados' => Mercurio41::where(['estado' => 'A', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'rechazados' => Mercurio41::where(['estado' => 'R', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'devueltos' => Mercurio41::where(['estado' => 'D', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'temporales' => Mercurio41::where(['estado' => 'T', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                    ],
                    'icon' => 'I',
                    'url' => 'independiente/index',
                    'imagen' => 'independiente.jpg',
                ],
                [
                    'name' => 'Solicitud Pensionado',
                    'cantidad' => [
                        'pendientes' => Mercurio38::where(['estado' => 'P', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'aprobados' => Mercurio38::where(['estado' => 'A', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'rechazados' => Mercurio38::where(['estado' => 'R', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'devueltos' => Mercurio38::where(['estado' => 'D', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'temporales' => Mercurio38::where(['estado' => 'T', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                    ],
                    'icon' => 'P',
                    'url' => 'pensionado/index',
                    'imagen' => 'pensionado.jpg',
                ],
                [
                    'name' => 'Solicitud Facultativo',
                    'cantidad' => [
                        'pendientes' => Mercurio36::where(['estado' => 'P', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'aprobados' => Mercurio36::where(['estado' => 'A', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'rechazados' => Mercurio36::where(['estado' => 'R', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'devueltos' => Mercurio36::where(['estado' => 'D', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                        'temporales' => Mercurio36::where(['estado' => 'T', 'coddoc' => $coddoc, 'tipo' => $tipo, 'documento' => $documento])->count(),
                    ],
                    'icon' => 'F',
                    'url' => 'facultativo/index',
                    'imagen' => 'facultativo.jpg',
                ],
            ],
            'productos' => [
                [
                    'name' => 'P. Complemento_nutricional',
                    'url' => 'productos/complemento_nutricional',
                    'imagen' => 'complemento.jpg',
                ],
            ],
            'consultas' => false,
        ];
    }
}
