<?php

namespace App\Services\Formularios\Api;

use App\Library\Collections\ParamsBeneficiario;
use App\Models\Gener18;
use App\Services\Api\ApiPython;
use App\Services\Api\ApiSubsidio;
use Carbon\Carbon;

class BeneficiariosDocuments
{
    private $params;

    private $beneficiario;

    private $trabajador; // opcional, para enriquecer contexto

    private $biologico; // opcional, para enriquecer contexto

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
        $detdoc_rua_beneficiario = ($mtipoBenef) ? $mtipoBenef->codrua : 'CC';

        $mtipdisca = ParamsBeneficiario::getTipoDiscapacidad();
        $discapacidad_name = ($this->beneficiario->tipdis ?? null) ? ($mtipdisca[$this->beneficiario->tipdis] ?? 'No tiene') : 'No tiene';

        $mtipoPago = ParamsBeneficiario::getTipoPago();
        $tippag_detalle = ($this->beneficiario->tippag ?? null) ? ($mtipoPago[$this->beneficiario->tippag] ?? '') : '';

        $parentescos = ParamsBeneficiario::getParentesco();
        $parentesco = ($this->beneficiario->parent ?? null) ? ($parentescos[$this->beneficiario->parent] ?? '') : '';

        $mtihij = ParamsBeneficiario::getTipoHijo();
        $tipo_hijo = $this->beneficiario->tiphij ? $mtihij[$this->beneficiario->tiphij] : 'Hijo normal';

        $nombre_trabajador = $this->trabajador->prinom . ' ' . $this->trabajador->segnom . ' ' . $this->trabajador->priape . ' ' . $this->trabajador->segape;

        $context_trabajador = null;

        $mtipoTrab = Gener18::where('coddoc', $this->trabajador->tipdoc)->first();
        $tipo_documento_trabajador = ($mtipoTrab) ? $mtipoTrab->detdoc : 'Cedula de ciudadania';
        $detdoc_rua_trabajador = ($mtipoTrab) ? $mtipoTrab->getCodrua() : 'CC';

        $context_trabajador = [
            'cedtra' => $this->trabajador->cedtra ?? null,
            'tipo_documento' => $tipo_documento_trabajador,
            'nombre_trabajador' => $nombre_trabajador,
            'nit' => $this->trabajador->nit ?? null,
            'detdoc_rua_trabajador' => $detdoc_rua_trabajador,
            ...$this->trabajador->toArray(),
        ];


        if (
            $this->beneficiario->tippag == 'T' ||
            $this->beneficiario->tippag  == null ||
            $this->beneficiario->tippag  == ''
        ) {
            $info_bancaria = '';
        } else {
            $mbanco = ParamsBeneficiario::getBancos();
            $banco = $this->beneficiario->codban ? $mbanco[$this->beneficiario->codban] : "";
            $tippag_detalle = $this->beneficiario->tippag ? $mtipoPago[$this->beneficiario->tippag] : "";

            $info_bancaria = "El afiliado {$nombre_trabajador}, con tipo documento {$tipo_documento_trabajador} y número {$this->beneficiario->cedtra},
                solicita que el pago del subsidio cuota monetaria se realice a la cuenta {$this->beneficiario->numcue} del banco {$banco},
                que corresponde al medio de pago {$tippag_detalle}.";
        }


        $context_biologico = null;
        if ($this->biologico) {
            $mtidocs = Gener18::where('coddoc', $this->biologico->tipdoc)->first();
            $detdoc = ($mtidocs) ? $mtidocs->detdoc : 'Cedula de ciudadania';

            $ciudad_residencia = ($this->biologico->ciures ?? null) ? ($ciudades[$this->biologico->ciures] ?? $this->biologico->ciures) : ' FLORENCIA';

            $context_biologico = [
                'cedcon' => $this->biologico->cedcon,
                'tipo_documento' => substr(capitalize($detdoc), 0, 44),
                'telefono' => $this->biologico->telefono,
                'email' => $this->biologico->email,
                'priape' => $this->biologico->priape,
                'segape' => $this->biologico->segape,
                'prinom' => $this->biologico->prinom,
                'segnom' => $this->biologico->segnom,
                'direccion' => $this->biologico->direccion,
                'ciudad_residencia' => $ciudad_residencia,
                'zoneurbana' => $this->biologico->zoneurbana,
                'desconoce_ubicacion' => $this->biologico->biodesco,
            ];
        }

        $nombre_beneficiario = trim(($this->beneficiario->prinom ?? '') . ' ' . ($this->beneficiario->segnom ?? '') . ' ' . ($this->beneficiario->priape ?? '') . ' ' . ($this->beneficiario->segape ?? ''));
        $today = Carbon::now();
        $context = [
            'year' => $today->format('Y'),
            'month' => $today->format('m'),
            'day' => $today->format('d'),
            'ciudad_name' => $ciudad_name,
            'resguardo_name' => $resguardo_name,
            'etnica_name' => $etnica_name,
            'pueblo_name' => $pueblo_name,
            'ocupaciones' => $ocupaciones,
            'tipo_documento' => $detdoc_detalle_beneficiario,
            'discapacidad_name' => $discapacidad_name,
            'tippag_detalle' => $tippag_detalle,
            'detdoc_rua_beneficiario' => $detdoc_rua_beneficiario,
            'nombre_beneficiario' => $nombre_beneficiario,
            'trabajador' => $context_trabajador,
            'biologico' => $context_biologico,
            'parentesco' => $parentesco,
            'tipo_hijo' => $tipo_hijo,
            'info_bancaria' => $info_bancaria,
            ...$this->beneficiario->toArray(),
        ];

        $ps = new ApiPython();
        $ps->send([
            'servicio' => 'Python',
            'metodo' => 'generate-pdf',
            'params' => [
                'template' => $this->params['template'],
                'output' => $this->params['output'],
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

        sleep(2);
        return true;
    }

    public function setParamsInit($params)
    {
        $this->params = $params;
        $this->beneficiario = $params['beneficiario'];
        $this->trabajador = $params['trabajador'] ?? null; // opcional
        $this->biologico = $params['biologico'] ?? null; // opcional
    }
}
