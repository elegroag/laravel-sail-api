<?php

namespace App\Services\Entidades;

use App\Models\Mercurio40;
use App\Services\Srequest;

class ServicioDomesticoService
{
    public function consultaTipopc(Srequest $request): array|bool
    {
        $tipo_consulta = $request->getParam('tipo_consulta');
        $tipopc = $request->getParam('tipopc');
        $condi_extra = $request->getParam('condi_extra');
        $usuario = $request->getParam('usuario');
        $numero = $request->getParam('numero');

        switch ($tipo_consulta) {
            case 'auditoria':
            case 'all':
                $response['datos'] = Mercurio40::query()
                    ->join('mercurio10', function ($join) use ($tipopc) {
                        $join->on('mercurio40.id', '=', 'mercurio10.numero')
                            ->where('mercurio10.tipopc', '=', $tipopc);
                    })
                    ->select([
                        'mercurio40.*',
                        'mercurio10.estado as estado',
                        'mercurio10.fecsis as fecest',
                    ])
                    ->when($condi_extra, function ($q) use ($condi_extra) {
                        if (is_array($condi_extra)) {
                            $q->where($condi_extra);
                        }
                        if (is_string($condi_extra) && strlen($condi_extra) > 0) {
                            $q->whereRaw($condi_extra);
                        }
                    })
                    ->get();
                break;
            case 'alluser':
                $response['datos'] = Mercurio40::whereRaw("usuario='{$usuario}' and estado='P'")->get();
                break;
            case 'count':
                $res = Mercurio40::where('mercurio40.usuario', $usuario)
                    ->when($condi_extra, function ($q) use ($condi_extra) {
                        if (is_array($condi_extra)) {
                            $q->where($condi_extra);
                        }
                        if (is_string($condi_extra) && strlen($condi_extra) > 0) {
                            $q->whereRaw($condi_extra);
                        }
                    })
                    ->get();

                $response['all'] = $res;
                $response['count'] = $res->count();
                break;
            case 'one':
                $response['datos'] = Mercurio40::whereRaw("id='{$numero}' and estado='P'")->first();
                break;
            case 'info':
                $mercurio = Mercurio40::where('id', $numero)->first();
                $response['consulta'] = $mercurio ? $mercurio->toArray() : [];
                break;
            default:
                $response = false;
                break;
        }

        return $response;
    }
}
