<?php

namespace App\Services\Entidades;

use App\Exceptions\DebugException;
use App\Library\Collections\ParamsTrabajador;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio31;
use App\Models\Mercurio10;
use App\Models\Mercurio07;
use App\Models\Mercurio12;
use App\Models\Mercurio13;
use App\Models\Mercurio37;
use App\Models\Mercurio01;
use App\Services\Utils\Comman;
use Illuminate\Support\Facades\DB;

class TrabajadorService
{

    private $tipopc = '1';
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
     * @param string $estado
     * @return array
     */
    public function findAllByEstado($estado = '')
    {
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];

        if ((new Mercurio31)->getCount(
            "*",
            "conditions: documento='{$documento}' and coddoc='{$coddoc}'"
        ) == 0) {
            return [];
        }

        if (empty($estado)) {
            $conditions = "and m31.estado NOT IN('I') ";
        } else {
            $conditions = "and m31.estado='{$estado}' ";
        }

        $sql  = "SELECT m31.*,
            (SELECT COUNT(*) FROM mercurio10 as me10 WHERE me10.tipopc='13' and m31.id = me10.numero) as cantidad_eventos,
            (SELECT MAX(fecsis) FROM mercurio10 as mr10 WHERE mr10.tipopc='13' and m31.id = mr10.numero) as fecha_ultima_solicitud,
            (CASE
                WHEN m31.estado = 'T' THEN 'Temporal en edición'
                WHEN m31.estado = 'D' THEN 'Devuelto'
                WHEN m31.estado = 'A' THEN 'Aprobado'
                WHEN m31.estado = 'X' THEN 'Rechazado'
                WHEN m31.estado = 'P' THEN 'Pendiente De Validación CAJA'
                WHEN m31.estado = 'I' THEN 'Inactiva'
            END) as estado_detalle,
            coddoc as tipo_documento
            FROM mercurio31 as m31
            WHERE m31.documento='{$documento}' and m31.coddoc='{$coddoc}' {$conditions}
            ORDER BY m31.fecsol ASC";

        return $this->db->inQueryAssoc($sql);
    }

    /**
     * buscarEmpresaSubsidio function
     * buscar empresa en subsidio sin importar el estado
     * @param [type] $nit
     * @return void
     */
    public function buscarEmpresaSubsidio($nit)
    {

        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_empresa",
                "params" => array(
                    "nit" => $nit
                )
            )
        );
        $salida =  $procesadorComando->toArray();
        if ($salida['success']) {
            return $salida;
        } else {
            return false;
        }
    }

    public function archivosRequeridos($solicitud)
    {
        if ($solicitud == false) return false;
        $archivos = array();

        $db = DbBase::rawConnect();

        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $corregir = explode(';', $mercurio10->campos_corregir);
        }

        $mercurio13 = Mercurio13::where('tipopc', $this->tipopc)->orderBy('auto_generado', 'desc')->get();

        foreach ($mercurio13 as $m13) {

            $m12 = Mercurio12::where('coddoc', $m13->getCoddoc())->first();

            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $solicitud->getId())
                ->where('coddoc', $m13->getCoddoc())
                ->first();

            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }

            $obliga = ($m13->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo = new \stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $solicitud->getId();
            $archivo->coddoc = $m13->getCoddoc();
            $archivo->detalle = $m12->getDetalle();
            $archivo->diponible = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo->corrige = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $html = view("empresa/tmp/archivos_requeridos", array(
            "load_archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        ))->render();

        return $html;
    }

    public function dataArchivosRequeridos($solicitud)
    {

        if ($solicitud == false) return false;
        $archivos = array();

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
            $archivo['obliga'] = ($m13->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo['id'] = $solicitud->getId();
            $archivo['detalle'] = capitalize($m12->getDetalle());
            $archivo['diponible'] = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo['corrige'] = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $archivos_descargar = oficios_requeridos('T');
        return [
            "disponibles" => $archivos_descargar,
            "archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        ];
    }


    /**
     * updateByFormData function
     * @param int $id
     * @param array $data
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
     * @param array $data
     * @return Mercurio31
     */
    public function create($data)
    {
        $trabajador = new Mercurio31($data);
        $trabajador->save();
        $id = $trabajador->getId();

        Mercurio37::where('tipopc', $this->tipopc)->where('numero', $id)->delete();
        Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->delete();

        return $trabajador;
    }

    /**
     * createByFormData function
     * @param array $data
     * @return Mercurio31
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
     * @param integer $id
     * @return Mercurio31
     */
    public function findById($id)
    {
        return Mercurio31::where('id', $id)->first();
    }

    /**
     * enviarCaja function
     * @param SenderValidationCaja $senderValidationCaja
     * @param integer $id
     * @param integer $documento
     * @param integer $coddoc
     * @return void
     */
    public function enviarCaja($senderValidationCaja, $id, $usuario)
    {
        $solicitud = $this->findById($id);

        $cm37 = (new Mercurio37)->getCount(
            "*",
            "conditions: tipopc='{$this->tipopc}' AND " .
                "numero='{$id}' AND " .
                "coddoc IN(SELECT coddoc FROM mercurio13 WHERE tipopc='{$this->tipopc}' AND obliga='S')"
        );

        $cm13 = (new Mercurio13)->getCount(
            "*",
            "conditions: tipopc='{$this->tipopc}' and obliga='S'"
        );
        if ($cm37 < $cm13) {
            throw new DebugException("Adjunte los archivos obligatorios", 500);
        }

        Mercurio31::where('id', $id)->update([
            'usuario' => $usuario,
            'estado' => 'P'
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
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $cedtra
     * @return array|boolean
     */
    public function buscarTrabajadorSubsidio($cedtra)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_trabajador",
                "params" => array(
                    "cedtra" => $cedtra,
                )
            )
        );
        $out =  $procesadorComando->toArray();
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

        return array(
            'seguimientos' => $seguimientos,
            'campos_disponibles' => (new Mercurio31)->CamposDisponibles(),
            'estados_detalles' => (new Mercurio10)->getArrayEstados()
        );
    }

    public function paramsApi()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_trabajadores"
            ),
            false
        );
        $paramsTrabajador = new ParamsTrabajador();
        $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());
    }

    /**
     * findRequestByDocuCod function
     * @changed [2023-12-00]
     * buscar solicitudes por trabajadores que se tengan registradas por la empresa x
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $documento
     * @param [type] $coddoc
     * @return void
     */
    public function findRequestByDocumentoCoddoc($documento, $coddoc)
    {
        $datos = Mercurio31::select('cedtra as cedula', DB::raw("CONCAT_WS('', prinom, segnom, priape, segape) as nombre_completo"))
            ->where([
                ['documento', $documento],
                ['coddoc', $coddoc],
                ['estado', '<>', 'X'],
                ['estado', '<>', 'I']
            ])
            ->get()
            ->toArray();

        return (is_array($datos) === True) ? $datos : false;
    }

    public function findApiTrabajadoresByNit($nit)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "listar_trabajadores",
                "params" => array(
                    'nit' => $nit
                )
            )
        );
        if ($procesadorComando->isJson() == false) throw new DebugException("Error resultado de API", 501);
        $out = $procesadorComando->getObject();
        return ($out->success == True) ? $out->data : False;
    }
}
