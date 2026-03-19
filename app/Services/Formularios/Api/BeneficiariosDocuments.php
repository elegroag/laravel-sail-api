<?php

namespace App\Services\Formularios\Api;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsBeneficiario;
use App\Models\Gener18;
use App\Services\Api\ApiPython;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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

        $nombre_trabajador = $this->trabajador->prinom . ' ' .
            $this->trabajador->segnom . ' ' .
            $this->trabajador->priape . ' ' .
            $this->trabajador->segape;

        $context_trabajador = null;

        $mtipoTrab = Gener18::where('coddoc', $this->trabajador->tipdoc)->first();
        $tipo_documento_trabajador = ($mtipoTrab) ? $mtipoTrab->detdoc : 'Cedula de ciudadania';
        $detdoc_rua_trabajador = ($mtipoTrab) ? $mtipoTrab->getCodrua() : 'CC';

        $context_trabajador = [
            'numero_documento' => $this->trabajador->cedtra ?? null,
            'tipo_documento' => $tipo_documento_trabajador,
            'nombre_trabajador' => $nombre_trabajador,
            'tipo_documento_siglas' => $detdoc_rua_trabajador,
            'nit' => $this->trabajador->nit,
            ...$this->trabajador->toArray(),
        ];

        $mtipcue = ParamsBeneficiario::getTipoCuenta();
        if (
            $this->beneficiario->tippag == 'T' ||
            $this->beneficiario->tippag  == null ||
            $this->beneficiario->tippag  == ''
        ) {
            $info_bancaria = '';
            $banco_name = '';
            $tipo_cuenta = '';
        } else {
            $mbanco = ParamsBeneficiario::getBancos();
            $banco = $this->beneficiario->codban ? $mbanco[$this->beneficiario->codban] : "";
            $tippag_detalle = $this->beneficiario->tippag ? $mtipoPago[$this->beneficiario->tippag] : "";

            $banco_name = $this->beneficiario->codban ? $mbanco[$this->beneficiario->codban] : "";
            $tipo_cuenta = $this->beneficiario->tipcue ? $mtipcue[$this->beneficiario->tipcue] : "Ahorros";

            $info_bancaria = "El afiliado {$nombre_trabajador}, con tipo documento {$tipo_documento_trabajador} y número {$this->beneficiario->cedtra},
                solicita que el pago del subsidio cuota monetaria se realice a la cuenta {$this->beneficiario->numcue} del banco {$banco},
                que corresponde al medio de pago {$tippag_detalle}.";
        }


        $context_biologico = [];
        if ($this->biologico) {
            $mtidocs = Gener18::where('coddoc', $this->biologico->tipdoc)->first();
            $detdoc = ($mtidocs) ? $mtidocs->detdoc : 'Cedula de ciudadania';
            $ciudad_residencia = ($this->biologico->ciures ?? null) ? ($ciudades[$this->biologico->ciures] ?? $this->biologico->ciures) : ' FLORENCIA';

            $context_biologico = [
                'numero_documento' => $this->biologico->cedcon,
                'tipo_documento' => $detdoc,
                'telefono' => $this->biologico->telefono,
                'email' => $this->biologico->email,
                'primer_apellido' => $this->biologico->priape,
                'segundo_apellido' => $this->biologico->segape,
                'primer_nombre' => $this->biologico->prinom,
                'segundo_nombre' => $this->biologico->segnom,
                'direccion' => $this->biologico->direccion,
                'ciudad_residencia' => $ciudad_residencia,
                'zona_urbana' => $this->biologico->zoneurbana,
                'desconoce_ubicacion' => $this->biologico->biodesco,
                'sexo' => $this->biologico->sexo,
                'nombre_completo' => $this->biologico->prinom . ' ' .
                    $this->biologico->segnom . ' ' .
                    $this->biologico->priape . ' ' .
                    $this->biologico->segape,
                ...$this->biologico->toArray(),
            ];
        }

        $nombre_beneficiario = trim(($this->beneficiario->prinom ?? '') . ' ' .
            ($this->beneficiario->segnom ?? '') . ' ' .
            ($this->beneficiario->priape ?? '') . ' ' .
            ($this->beneficiario->segape ?? ''));

        $today = Carbon::now();
        $puede_trabajar = ($this->beneficiario->captra == 'S') ? $this->beneficiario->captra : 'N';
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
            'banco_name' => $banco_name,
            'tipo_cuenta' => $tipo_cuenta,
            'numero_cuenta' => $this->beneficiario->numcue,
            'puede_trabajar' => $puede_trabajar,
            ...$this->beneficiario->toArray(),
        ];

        #dd($context);

        $ps = new ApiPython();
        $ps->send([
            'servicio' => 'Python',
            'metodo' => 'genera-consolidado-pdf',
            'params' => [
                'templates' => $this->params['templates'],
                'output' => $this->params['output'],
                'context' => $context,
            ]
        ]);

        if ($ps->isJson() == false) return false;
        $out = $ps->toArray();
        if ($out['success'] == false) {
            throw new DebugException("Error generando el PDF", 501, $out);
        }
        //el documento ahora llega en base64
        $data = $out['data'];
        $api_content = $data['api_content'];
        $api_filename = $data['api_filename'];
        //guarda el archivo en storage usar Storage Disk
        if (
            $api_content &&
            $api_filename &&
            is_string($api_content) &&
            is_string($api_filename)
        ) {
            Storage::disk('temp')->put($api_filename, base64_decode($api_content));
        } else {
            throw new DebugException("Error guardando el archivo", 501, $out);
        }
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
