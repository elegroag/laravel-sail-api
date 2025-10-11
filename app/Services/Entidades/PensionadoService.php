<?php

namespace App\Services\Entidades;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsPensionado;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio14;
use App\Models\Mercurio37;
use App\Models\Mercurio38;
use App\Services\Srequest;
use App\Services\Utils\Comman;

class PensionadoService
{
    private $tipopc = 9;

    private $tipsoc = '08';

    private $user;

    private $db;

    public function __construct()
    {
        $this->user = session('user');
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

        // usuario empresa, unica solicitud de afiliación
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];

        if (empty($estado)) {
            $conditions = "and solis.estado NOT IN('I') ";
        } else {
            $conditions = "and solis.estado='{$estado}' ";
        }
        $sql = "SELECT solis.*,
            CONCAT_WS(' ', solis.priape, solis.segape, solis.prinom, solis.segnom) as razsoc,
            (SELECT COUNT(*) FROM mercurio10 as me10 WHERE me10.tipopc='{$this->tipopc}' and solis.id = me10.numero) as cantidad_eventos,
            (SELECT MAX(fecsis) FROM mercurio10 as mr10 WHERE mr10.tipopc='{$this->tipopc}' and solis.id = mr10.numero) as fecha_ultima_solicitud,
            (CASE
                WHEN solis.estado = 'T' THEN 'Temporal en edición'
                WHEN solis.estado = 'D' THEN 'Devuelto'
                WHEN solis.estado = 'A' THEN 'Aprobado'
                WHEN solis.estado = 'X' THEN 'Rechazado'
                WHEN solis.estado = 'P' THEN 'Pendiente De Validación CAJA'
                WHEN solis.estado = 'I' THEN 'Inactiva'
            END) as estado_detalle,
            'NATURAL' as tipo_persona,
            solis.coddoc as tipo_documento,
            gener09.detzon as detalle_zona
            FROM mercurio38 as solis
            LEFT JOIN gener09 ON gener09.codzon = solis.codzon
            WHERE solis.documento='{$documento}' and solis.coddoc='{$coddoc}' {$conditions}
            ORDER BY solis.fecini ASC;";

        $solicitudes = $this->db->inQueryAssoc($sql);

        return $solicitudes;
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

    public function archivosRequeridos($solicitud)
    {
        if ($solicitud == false) {
            return false;
        }
        $archivos = [];

        $mercurio10 = (new Mercurio10)->where([
            ['numero', '=', $solicitud->getId()],
            ['tipopc', '=', $this->tipopc],
        ])->orderBy('item', 'desc')->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $campos = $mercurio10->campos_corregir;
            $corregir = explode(';', $campos);
        }

        $mercurio14 = Mercurio14::where('tipopc', $this->tipopc)->get();

        foreach ($mercurio14 as $m14) {
            $m12 = Mercurio12::where('coddoc', $m14->getCoddoc())->first();

            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $solicitud->getId())
                ->where('coddoc', $m14->getCoddoc())
                ->first();

            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }
            $obliga = ($m14->getObliga() == 'S') ? "<br><small class='text-danger'>Obligatorio</small>" : '';
            $archivo = new \stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $solicitud->getId();
            $archivo->coddoc = $m14->getCoddoc();
            $archivo->detalle = $m12->getDetalle();
            $archivo->diponible = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo->corrige = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $html = view('pensionado/tmp/archivos_requeridos', [
            'load_archivos' => $archivos,
            'path' => $mercurio01->getPath(),
            'puede_borrar' => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true,
        ])->render();

        return $html;
    }

    /**
     * update function
     *
     * @param  int  $id
     * @param  array  $data
     * @return Mercurio38
     */
    public function update($id, $data)
    {
        $empresa = $this->findById($id);
        if ($empresa != false) {
            $empresa->fill($data);
            $empresa->save();

            return $empresa;
        }

        return false;
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
            $solicitud->save();

            return $solicitud;
        } else {
            return false;
        }
    }

    /**
     * create function
     *
     * @param  array  $data
     * @return Mercurio38
     */
    public function create($data)
    {
        $pensionado = new Mercurio38($data);
        $pensionado->save();
        $id = $pensionado->getId();

        Mercurio37::where('tipopc', $this->tipopc)->where('numero', $id)->delete();
        Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->delete();

        return $pensionado;
    }

    /**
     * create function
     *
     * @param  array  $data
     * @return Mercurio38
     */
    public function createByFormData($data)
    {
        $data['estado'] = 'T';
        $data['log'] = '0';
        $data['id'] = null;
        $pensionado = $this->create($data);

        return $pensionado;
    }

    /**
     * findById function
     *
     * @param  int  $id
     * @return Mercurio38
     */
    public function findById($id)
    {
        return Mercurio38::where('id', $id)->first();
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

        $cm37 = (new Mercurio37)->getCount(
            '*',
            "conditions: tipopc='{$this->tipopc}' AND " .
                "numero='{$id}' AND " .
                "coddoc IN(SELECT coddoc FROM mercurio14 WHERE tipopc='{$this->tipopc}' AND tipsoc='{$this->tipsoc}' AND obliga='S')"
        );

        $cm14 = (new Mercurio14)->getCount(
            '*',
            "conditions: tipopc='{$this->tipopc}' and tipsoc='{$this->tipsoc}' and obliga='S'"
        );
        if ($cm37 < $cm14) {
            throw new DebugException('Adjunte los archivos obligatorios', 500);
        }

        Mercurio38::where('id', $id)->update([
            'usuario' => $usuario,
            'estado' => 'P',
        ]);

        $ai = Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->max('item') + 1;

        $solicitud->item = $ai;
        $solicitante = Mercurio07::where([
            ['documento', '=', $solicitud->getDocumento()],
            ['coddoc', '=', $solicitud->getCoddoc()],
            ['tipo', '=', $solicitud->getTipo()],
        ])->first();

        $solicitud->repleg = $solicitante->getNombre();
        $solicitud->razsoc = $solicitante->getNombre();
        $solicitud->nit = $solicitante->getDocumento();
        $solicitud->email = $solicitante->getEmail();

        $senderValidationCaja->send($this->tipopc, $solicitud);
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
            'campos_disponibles' => (new Mercurio38)->CamposDisponibles(),
            'estados_detalles' => (new Mercurio10)->getArrayEstados(),
        ];
    }

    public function dataArchivosRequeridos($solicitud)
    {
        if ($solicitud == false) {
            return false;
        }
        $archivos = [];

        $mercurio10 = Mercurio10::where([
            ['numero', '=', $solicitud->getId()],
            ['tipopc', '=', $this->tipopc],
        ])->orderBy('item', 'desc')->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $campos = $mercurio10->campos_corregir;
            $corregir = explode(';', $campos);
        }

        $mercurio14 = Mercurio14::where('tipopc', $this->tipopc)->orderBy('auto_generado', 'desc')->get();

        foreach ($mercurio14 as $m14) {

            $m12 = Mercurio12::where('coddoc', $m14->getCoddoc())->first();

            $mercurio37 = Mercurio37::where([
                ['tipopc', '=', $this->tipopc],
                ['numero', '=', $solicitud->getId()],
                ['coddoc', '=', $m14->getCoddoc()],
            ])->first();

            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }

            $archivo = $m14->toArray();
            $archivo['obliga'] = ($m14->getObliga() == 'S') ? "<br><small class='text-danger'>Obligatorio</small>" : '';
            $archivo['id'] = $solicitud->getId();
            $archivo['detalle'] = capitalize($m12->getDetalle());
            $archivo['diponible'] = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo['corrige'] = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $archivos_descargar = oficios_requeridos('O');

        return [
            'disponibles' => $archivos_descargar,
            'archivos' => $archivos,
            'path' => $mercurio01->getPath(),
            'puede_borrar' => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true,
        ];
    }

    public function paramsApi()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_empresa',
            ]
        );

        $paramsPensionado = new ParamsPensionado;
        $paramsPensionado->setDatosCaptura($procesadorComando->toArray());

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            [
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'parametros_trabajadores',
            ]
        );

        $paramsTrabajador = new ParamsTrabajador;
        $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());
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
                $response["datos"] = Mercurio38::query()
                    ->join('mercurio10', function ($join) use ($tipopc) {
                        $join->on('mercurio38.id', '=', 'mercurio10.numero')
                            ->where('mercurio10.tipopc', '=', $tipopc);
                    })
                    ->select([
                        'mercurio38.*',
                        'mercurio10.estado as estado',
                        'mercurio10.fecsis as fecest',
                    ])
                    ->when($condi_extra, function ($q) use ($condi_extra) {
                        $q->whereRaw($condi_extra);
                    })
                    ->get();
                break;
            case 'alluser':
                $response["datos"] = Mercurio38::where("usuario='{$usuario}' and estado='P'")->get();
                break;
            case 'count':
                $response["count"] = Mercurio38::whereRaw("mercurio38.usuario='$usuario' $condi_extra ")
                    ->join('mercurio20', 'mercurio38.log', 'mercurio20.log')
                    ->getId();

                $response["all"] = Mercurio38::whereRaw("mercurio38.usuario='$usuario' $condi_extra")
                    ->join('mercurio20', 'mercurio38.log', 'mercurio20.log')
                    ->get();
                break;
            case 'one':
                $response["datos"] = Mercurio38::where("id='$numero' and estado='P'")->get();
                break;
            case 'info':
                $mercurio = Mercurio38::where("id='$numero' ")->get();
                //$response["consulta"] = $this->buscarTrabajadorSubsidio($mercurio->getCedtra());
                break;
            default:
                $response = false;
                break;
        }
        return $response;
    }
}
