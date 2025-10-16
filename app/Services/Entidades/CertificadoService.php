<?php

namespace App\Services\Entidades;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio13;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio37;
use App\Models\Mercurio45;
use App\Models\Mercurio47;
use App\Services\Srequest;
use App\Services\Utils\Comman;
use Illuminate\Support\Facades\DB;

class CertificadoService
{
    private $tipopc = '8';

    private $user;

    private $db;

    private $tipo;

    public function __construct()
    {
        $this->user = session('user');
        $this->tipo = session('tipo');
        $this->db = DbBase::rawConnect();
    }

    /**
     * findAllByEstado function
     *
     * @param  string  $estado
     * @return array
     */
    public function findAllByEstado($estado = '')
    {
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];

        if ((new Mercurio45)->getCount(
            '*',
            "conditions: documento='{$documento}' and coddoc='{$coddoc}'"
        ) == 0) {
            return [];
        }

        if (empty($estado)) {
            $conditions = "and m45.estado NOT IN('I') ";
        } else {
            $conditions = "and m45.estado='{$estado}' ";
        }

        $sql = "SELECT m45.*,
            (SELECT COUNT(*) FROM mercurio10 as me10 WHERE me10.tipopc='13' and m45.id = me10.numero) as cantidad_eventos,
            (SELECT MAX(fecsis) FROM mercurio10 as mr10 WHERE mr10.tipopc='13' and m45.id = mr10.numero) as fecha_ultima_solicitud,
            (CASE
                WHEN m45.estado = 'T' THEN 'Temporal en edición'
                WHEN m45.estado = 'D' THEN 'Devuelto'
                WHEN m45.estado = 'A' THEN 'Aprobado'
                WHEN m45.estado = 'X' THEN 'Rechazado'
                WHEN m45.estado = 'P' THEN 'Pendiente De Validación CAJA'
                WHEN m45.estado = 'I' THEN 'Inactiva'
            END) as estado_detalle,
            coddoc as tipo_documento
            FROM Mercurio45 as m45
            WHERE m45.documento='{$documento}' and m45.coddoc='{$coddoc}' {$conditions}
            ORDER BY m45.fecsol ASC";

        return $this->db->inQueryAssoc($sql);
    }

    /**
     * buscarEmpresaSubsidio function
     * buscar empresa en subsidio sin importar el estado
     *
     * @param [type] $nit
     * @return void
     */
    public function buscarEmpresaSubsidio($nit)
    {

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_empresa',
                'params' => [
                    'nit' => $nit,
                ],
            ]
        );
        $salida = $procesadorComando->toArray();
        if ($salida['success']) {
            return $salida;
        } else {
            return false;
        }
    }


    public function dataArchivosRequeridos($solicitud)
    {

        if ($solicitud == false) {
            return false;
        }
        $archivos = [];
        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $corregir = explode(';', $mercurio10->campos_corregir);
        }

        $mercurio13 = (new Mercurio13)->where('tipopc', $this->tipopc)
            ->orderBy('auto_generado', 'DESC')
            ->get();

        foreach ($mercurio13 as $m13) {

            $m12 = Mercurio12::where('coddoc', $m13->getCoddoc())->first();

            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $solicitud->getId())
                ->where('coddoc', $m13->getCoddoc())
                ->first();

            $corrige = false;
            if ($corregir && in_array($m12->coddoc, $corregir)) {
                $corrige = true;
            }

            $archivo = $m13->getArray();
            $archivo['obliga'] = ($m13->getObliga() == 'S') ? "<br><small class='text-danger'>Obligatorio</small>" : '';
            $archivo['id'] = $solicitud->getId();
            $archivo['detalle'] = capitalize($m12->getDetalle());
            $archivo['diponible'] = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo['corrige'] = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $archivos_descargar = [];
        return [
            'disponibles' => $archivos_descargar,
            'archivos' => $archivos,
            'path' => $mercurio01->getPath(),
            'puede_borrar' => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true,
        ];
    }

    /**
     * updateByFormData function
     *
     * @param  int  $id
     * @param  array  $data
     * @return bool
     */
    public function updateByFormData($id, $data)
    {
        $solicitud = $this->findById($id);
        if ($solicitud) {
            $solicitud->fill($data);

            return $solicitud->save();
        } else {
            return false;
        }
    }

    /**
     * create function
     *
     * @param  array  $data
     * @return Mercurio45
     */
    public function create($data)
    {
        $trabajador = new Mercurio45($data);
        $trabajador->save();
        $id = $trabajador->getId();

        Mercurio37::where('tipopc', $this->tipopc)->where('numero', $id)->delete();
        Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->delete();

        return $trabajador;
    }

    /**
     * createByFormData function
     *
     * @param  array  $data
     * @return Mercurio45
     */
    public function createByFormData($data)
    {
        $data['log'] = 0;
        $data['estado'] = 'T';
        $trabajador = $this->create($data);

        return $trabajador;
    }

    /**
     * findById function
     *
     * @param  int  $id
     * @return Mercurio45
     */
    public function findById($id)
    {
        return Mercurio45::where('id', $id)->first();
    }

    /**
     * enviarCaja function
     *
     * @param  SenderValidationCaja  $senderValidationCaja
     * @param  int  $id
     * @param  int  $documento
     * @param  int  $coddoc
     * @return void
     */
    public function enviarCaja($senderValidationCaja, $id, $usuario)
    {
        $solicitud = $this->findById($id);

        $cm37 = Mercurio37::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->whereIn('coddoc', function ($q) {
                $q->from('mercurio13')
                    ->select('coddoc')
                    ->where('tipopc', $this->tipopc)
                    ->where('obliga', 'S');
            })
            ->count();

        $cm13 = Mercurio13::where('tipopc', $this->tipopc)
            ->where('obliga', 'S')
            ->count();

        if ($cm37 < $cm13) {
            throw new DebugException('Adjunte los archivos obligatorios', 500);
        }

        Mercurio45::where('id', $id)->update([
            'usuario' => $usuario,
            'estado' => 'P',
        ]);

        $ai = Mercurio10::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->max('item') + 1;

        $solicitud->item = $ai;
        $solicitante = Mercurio07::where('documento', $solicitud->getDocumento())
            ->where('coddoc', $solicitud->getCoddoc())
            ->where('tipo', $solicitud->getTipo())
            ->first();

        $solicitud->repleg = $solicitante->getNombre();
        $solicitud->razsoc = $solicitante->getNombre();
        $solicitud->nit = $solicitante->getDocumento();
        $solicitud->email = $solicitante->getEmail();
        $senderValidationCaja->send($this->tipopc, $solicitud);
    }

    /**
     * buscarTrabajadorSubsidio function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $cedtra
     * @return array|bool
     */
    public function buscarTrabajadorSubsidio($cedtra)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaEmpresas',
                'metodo' => 'informacion_trabajador',
                'params' => [
                    'cedtra' => $cedtra,
                ],
            ]
        );
        $out = $procesadorComando->toArray();

        return ($out['success'] == true) ? $out['data'] : false;
    }

    public function consultaSeguimiento($id)
    {
        $seguimientos = Mercurio10::where('numero', $id)
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->get()
            ->map(function ($row) {
                $campos = explode(';', $row->campos_corregir);
                $row->corregir = $campos;

                return $row;
            })
            ->toArray();

        return [
            'seguimientos' => $seguimientos,
            'campos_disponibles' => (new Mercurio45)->CamposDisponibles(),
            'estados_detalles' => (new Mercurio10)->getArrayEstados(),
        ];
    }

    public function paramsApi()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_trabajadores',
            ],
            false
        );
        $paramsTrabajador = new ParamsTrabajador;
        $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());
    }

    /**
     * findRequestByDocuCod function
     *
     * @changed [2023-12-00]
     * buscar solicitudes por trabajadores que se tengan registradas por la empresa x
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @param [type] $documento
     * @param [type] $coddoc
     * @return void
     */
    public function findRequestByDocumentoCoddoc($documento, $coddoc)
    {
        $datos = Mercurio45::select('cedtra as cedula', DB::raw("CONCAT_WS('', prinom, segnom, priape, segape) as nombre_completo"))
            ->where([
                ['documento', $documento],
                ['coddoc', $coddoc],
                ['estado', '<>', 'X'],
                ['estado', '<>', 'I'],
            ])
            ->get()
            ->toArray();

        return (is_array($datos) === true) ? $datos : false;
    }

    public function findApiTrabajadoresByNit($nit)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'listar_trabajadores',
                'params' => [
                    'nit' => $nit,
                ],
            ]
        );
        if ($procesadorComando->isJson() == false) {
            throw new DebugException('Error resultado de API', 501);
        }
        $out = $procesadorComando->getObject();

        return ($out->success == true) ? $out->data : false;
    }


    public function consultaTipopc(Srequest $request): array|bool
    {
        $tipo_consulta = $request->getParam('tipo_consulta');
        $tipopc = $request->getParam('tipopc');
        $condi_extra = $request->getParam('condi_extra');
        $usuario = $request->getParam('usuario');
        $numero = $request->getParam('numero');

        switch ($tipo_consulta) {
            case 'all':
                $response["datos"] = Mercurio45::query()
                    ->join('mercurio10', function ($join) use ($tipopc) {
                        $join->on('Mercurio45.id', '=', 'mercurio10.numero')
                            ->where('mercurio10.tipopc', '=', $tipopc);
                    })
                    ->select([
                        'Mercurio45.*',
                        'mercurio10.estado as estado',
                        'mercurio10.fecsis as fecest',
                    ])
                    ->when($condi_extra, function ($q) use ($condi_extra) {
                        if (is_array($condi_extra)) $q->where($condi_extra);
                        if (is_string($condi_extra) && strlen($condi_extra) > 0) $q->whereRaw($condi_extra);
                    })
                    ->get();
                break;
            case 'alluser':
                $response["datos"] = Mercurio45::whereRaw("usuario='{$usuario}' and estado='P'")->get();
                break;
            case 'count':
                $res = Mercurio45::where("mercurio45.usuario", $usuario)
                    ->when($condi_extra, function ($q) use ($condi_extra) {
                        if (is_array($condi_extra)) $q->where($condi_extra);
                        if (is_string($condi_extra) && strlen($condi_extra) > 0) $q->whereRaw($condi_extra);
                    })
                    ->get();

                $response["all"] = $res;
                $response["count"] = $res->count();
                break;
            case 'one':
                $response["datos"] = Mercurio45::whereRaw("id='$numero' and estado='P'")->first();
                break;
            case 'info':
                $mercurio = Mercurio45::where("id", $numero)->first();
                $response["consulta"] = $this->buscarTrabajadorSubsidio($mercurio->getCedtra());
                break;
            default:
                $response = false;
                break;
        }
        return $response;
    }
}
