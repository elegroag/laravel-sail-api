<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsBeneficiario;
use App\Models\Gener18;
use App\Services\Api\ApiSubsidio;

class BeneficiariosDocuments
{
    private $params;

    private $beneficiario;

    private $trabajador; // opcional, para enriquecer contexto

    public function main()
    {
        $ciudades = ParamsBeneficiario::getCiudades();
        $ciudad_name = ($this->beneficiario->codzon ?? null) ? ($ciudades[$this->beneficiario->codzon] ?? $this->beneficiario->codzon) : ' FLORENCIA';

        $mresguardos = ParamsBeneficiario::getResguardos();
        $resguardo_name = ($this->beneficiario->resguardo_id ?? null) ? ($mresguardos[$this->beneficiario->resguardo_id] ?? 'NO APLICA') : 'NO APLICA';

        $metnica = ParamsBeneficiario::getPertenenciaEtnicas();
        $etnica_name = ($this->beneficiario->peretn ?? null) ? ($metnica[$this->beneficiario->peretn] ?? 'NO APLICA') : 'NO APLICA';

        $mpueblos = ParamsBeneficiario::getPueblosIndigenas();
        $pueblo_name = ($this->beneficiario->pub_indigena_id ?? null) ? ($mpueblos[$this->beneficiario->pub_indigena_id] ?? 'NO APLICA') : 'NO APLICA';

        $ocupaciones = ParamsBeneficiario::getOcupaciones();

        $mtipoBenef = Gener18::where('coddoc', $this->beneficiario->tipdoc)->first();
        $detdoc_detalle_beneficiario = ($mtipoBenef) ? $mtipoBenef->detdoc : 'Cedula de ciudadania';
        $detdoc_rua_beneficiario = ($mtipoBenef) ? $mtipoBenef->getCodrua() : 'CC';

        $mtipdisca = ParamsBeneficiario::getTipoDiscapacidad();
        $discapacidad_name = ($this->beneficiario->tipdis ?? null) ? ($mtipdisca[$this->beneficiario->tipdis] ?? 'No tiene') : 'No tiene';

        $mtipoPago = ParamsBeneficiario::getTipoPago();
        $tippag_detalle = ($this->beneficiario->tippag ?? null) ? ($mtipoPago[$this->beneficiario->tippag] ?? '') : '';

        $context_trabajador = null;
        if (!empty($this->trabajador)) {
            $mtipoTrab = Gener18::where('coddoc', $this->trabajador->tipdoc)->first();
            $detdoc_detalle_trabajador = ($mtipoTrab) ? $mtipoTrab->detdoc : 'Cedula de ciudadania';
            $detdoc_rua_trabajador = ($mtipoTrab) ? $mtipoTrab->getCodrua() : 'CC';
            $context_trabajador = [
                'cedtra' => $this->trabajador->cedtra ?? null,
                'detdoc_detalle' => $detdoc_detalle_trabajador,
                'nombre_trabajador' => trim(($this->trabajador->prinom ?? '') . ' ' . ($this->trabajador->segnom ?? '') . ' ' . ($this->trabajador->priape ?? '') . ' ' . ($this->trabajador->segape ?? '')),
                'nit' => $this->trabajador->nit ?? null,
                'detdoc_rua_trabajador' => $detdoc_rua_trabajador,
                ...$this->trabajador->toArray(),
            ];
        }

        $context = [
            'ciudad_name' => $ciudad_name,
            'resguardo_name' => $resguardo_name,
            'etnica_name' => $etnica_name,
            'pueblo_name' => $pueblo_name,
            'ocupaciones' => $ocupaciones,
            'detdoc_detalle' => $detdoc_detalle_beneficiario,
            'discapacidad_name' => $discapacidad_name,
            'tippag_detalle' => $tippag_detalle,
            'detdoc_rua_beneficiario' => $detdoc_rua_beneficiario,
            'nombre_beneficiario' => trim(($this->beneficiario->prinom ?? '') . ' ' . ($this->beneficiario->segnom ?? '') . ' ' . ($this->beneficiario->priape ?? '') . ' ' . ($this->beneficiario->segape ?? '')),
            'trabajador' => $context_trabajador,
            ...$this->beneficiario->toArray(),
        ];

        foreach ($this->params['oficios'] as $oficio) {
            $ps = new ApiSubsidio();
            $ps->send([
                'servicio' => 'Python',
                'metodo' => 'generate-pdf',
                'params' => [
                    'template' => $oficio['template'],
                    'output' => $oficio['output'],
                    'context' => $context,
                ]
            ]);
            if ($ps->isJson() == false) {
                return false;
            }
            $out = $ps->toArray();
            if ($out['success'] == false) {
                return false;
            }
        }

        sleep(2);
        return true;
    }

    public function setParamsInit($params)
    {
        $this->params = $params;
        $this->beneficiario = $params['beneficiario'];
        $this->trabajador = $params['trabajador'] ?? null; // opcional
    }
}
