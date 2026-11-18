<?php

namespace App\Services\Cajas\Solicitudes;

use Illuminate\Database\Eloquent\Model;

/**
 * Aprobación de solicitudes de afiliación de personas (tablas mercurio36, mercurio38 y mercurio41).
 */
abstract class AprobacionPersonaService extends SolicitudAprobacionService
{
    protected function identificacion(Model $row): string
    {
        return (string) $row->getAttribute('cedtra');
    }

    protected function nombre(Model $row): string
    {
        return trim((string) preg_replace('/\s+/', ' ', implode(' ', [
            $row->getAttribute('prinom'),
            $row->getAttribute('segnom'),
            $row->getAttribute('priape'),
            $row->getAttribute('segape'),
        ])));
    }

    /**
     * @return list<string>
     */
    protected function searchColumns(): array
    {
        return ['cedtra', 'prinom', 'segnom', 'priape', 'segape'];
    }

    protected function aprobarFijos(Model $solicitud, array $data): array
    {
        return [
            'tipemp' => 'P',
            'ofiafi' => '01',
            'colegio' => 'N',
            'contratista' => 'N',
            'pymes' => 'N',
        ];
    }

    public function secciones(Model $solicitud): array
    {
        $secciones = [
            [
                'titulo' => 'Información del solicitante',
                'campos' => $this->camposDe($solicitud, [
                    'Radicado (RUUID)' => ['ruuid'],
                    'Documento' => ['cedtra'],
                    'Tipo de documento' => ['tipdoc', 'coddoc'],
                    'Nombre completo' => null,
                    'Fecha de nacimiento' => ['fecnac', null, true],
                    'Sexo' => ['sexo', 'sexo'],
                    'Estado civil' => ['estciv', 'estciv'],
                    'Estado' => ['__estado'],
                    'Tipo de aportante' => ['__tipo'],
                    'Calidad' => ['calemp', 'calemp'],
                    'Fecha de solicitud' => ['fecsol', null, true],
                    'Fecha aprobación resolución' => ['fecapr', null, true],
                ]),
            ],
            [
                'titulo' => 'Ubicación y contacto',
                'campos' => $this->camposDe($solicitud, [
                    'Dirección' => ['direccion'],
                    'Dirección laboral' => ['dirlab'],
                    'Barrio' => ['barrio'],
                    'Ciudad' => ['codciu', 'codciu'],
                    'Zona' => ['codzon', 'codzon'],
                    'Teléfono' => ['telefono'],
                    'Celular' => ['celular'],
                    'Email' => ['email'],
                ]),
            ],
            [
                'titulo' => 'Información laboral',
                'campos' => $this->camposDe($solicitud, [
                    'Actividad' => ['codact', 'codact'],
                    'Fecha inicial' => ['fecini', null, true],
                    'Salario' => ['salario'],
                    'Cargo' => ['cargo'],
                ]),
            ],
            [
                'titulo' => 'Medio de pago',
                'campos' => $this->camposDe($solicitud, [
                    'Tipo medio pago cuota' => ['tippag', 'tippag'],
                    'Banco' => ['codban', 'codban'],
                    'Tipo de cuenta' => ['tipcue', 'tipcue'],
                    'Número de cuenta' => ['numcue'],
                ]),
            ],
        ];

        return array_values(array_filter($secciones, fn (array $s) => $s['campos'] !== []));
    }
}
