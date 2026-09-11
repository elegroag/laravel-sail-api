<?php

namespace App\Services\Entidades;

use App\Models\Mercurio39;
use App\Services\Srequest;

class MadresComuniService
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
                $response['datos'] = Mercurio39::query()
                    ->join('mercurio10', function ($join) use ($tipopc) {
                        $join->on('mercurio39.id', '=', 'mercurio10.numero')
                            ->where('mercurio10.tipopc', '=', $tipopc);
                    })
                    ->select([
                        'mercurio39.*',
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
                $response['datos'] = Mercurio39::where('usuario', $usuario)->where('estado', 'P')->get();
                break;
            case 'count':
                $res = Mercurio39::where('mercurio39.usuario', $usuario)
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
                $response['datos'] = Mercurio39::where('id', $numero)->where('estado', 'P')->first();
                break;
            case 'info':
                $mercurio = Mercurio39::where('id', $numero)->first();
                $response['consulta'] = $mercurio ? $mercurio->toArray() : [];
                break;
            default:
                $response = false;
                break;
        }

        return $response;
    }
}
