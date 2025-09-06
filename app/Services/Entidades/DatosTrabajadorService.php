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
use App\Models\Mercurio37;
use App\Models\Mercurio47;
use App\Services\Utils\Comman;
use Illuminate\Support\Facades\DB;

class DatosTrabajadorService
{

    private $tipopc = 14;
    private $user;
    private $db;
    public function __construct()
    {
        $this->user = session('user');
        $this->db = DbBase::rawConnect();
    }

    /**
     * findAllByEstado function
     * @param string $estado
     * @return array
     */
    public function findAllByEstado($estado = '')
    {
        //usuario empresa, unica solicitud de afiliación
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];

        if (is_null($estado) || $estado == '') {
            $where = " solis.documento='{$documento}' AND solis.coddoc='{$coddoc}' ";
        } else {
            $where = "solis.documento='{$documento}' AND solis.coddoc='{$coddoc}' AND solis.estado='{$estado}' ";
        }

        $mercurio47 = $this->db->inQueryAssoc("SELECT solis.*,
            (CASE
                WHEN solis.estado = 'T' THEN 'Temporal en edición'
                WHEN solis.estado = 'D' THEN 'Devuelto'
                WHEN solis.estado = 'A' THEN 'Aprobado'
                WHEN solis.estado = 'X' THEN 'Rechazado'
                WHEN solis.estado = 'P' THEN 'Pendiente De Validación CAJA'
                WHEN solis.estado = 'I' THEN 'Inactiva'
            END) as estado_detalle
            FROM mercurio47 as solis
            WHERE {$where}
            ORDER BY solis.id DESC;
        ");

        foreach ($mercurio47 as $ai => $row) {
            $rqs = $this->db->fetchOne("SELECT count(mercurio10.numero) as cantidad
                FROM mercurio10
                LEFT JOIN mercurio47 ON mercurio47.id = mercurio10.numero
                WHERE mercurio10.tipopc='{$this->tipopc}' AND
                mercurio47.id ='{$row['id']}'
            ");

            $trayecto = $this->db->fetchOne("SELECT max(mercurio10.item), mercurio10.*
                FROM mercurio10
                LEFT JOIN mercurio47 ON mercurio47.id=mercurio10.numero
                WHERE mercurio10.tipopc='{$this->tipopc}' AND
                mercurio47.id ='{$row['id']}' LIMIT 1
            ");

            $mercurio47[$ai] = $row;
            $mercurio47[$ai]["cantidad_eventos"] = $rqs['cantidad'];
            $mercurio47[$ai]["fecha_ultima_solicitud"] = $trayecto['fecsis'];
            $mercurio47[$ai]["estado_detalle"] = (new Mercurio47)->getEstadoInArray($row['estado']);
            $mercurio47[$ai]["tipo_actualizacion_detalle"] = (new Mercurio47)->getTipoActualizacionInArray($row['tipo_actualizacion']);
        }
        return $mercurio47;
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

        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $corregir = explode(";", $mercurio10->campos_corregir);
        }

        $mercurio13 = Mercurio13::where('tipopc', $this->tipopc)
            ->orderBy('auto_generado', 'desc')
            ->get();

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

    /**
     * dataArchivosRequeridos function
     * @param Mercurio47 $solicitud
     * @return array
     */
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
            $corregir = explode(";", $mercurio10->campos_corregir);
        }

        $mercurio13 = Mercurio13::where('tipopc', $this->tipopc)
            ->orderBy('auto_generado', 'desc')
            ->get();

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
        return array(
            "disponibles" => $archivos_descargar,
            "archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        );
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
     * @return Mercurio47
     */
    public function create($data)
    {
        $trabajador = new Mercurio47($data);
        $trabajador->save();
        $id = $trabajador->getId();

        Mercurio37::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->delete();

        Mercurio10::where('tipopc', $this->tipopc)
            ->where('numero', $id)
            ->delete();
        return $trabajador;
    }


    /**
     * createByFormData function
     * @param array $data
     * @return Mercurio47
     */
    public function createByFormData($data)
    {
        $data['estado'] = "T";
        $trabajador = $this->create($data);
        return $trabajador;
    }

    /**
     * findById function
     * @param integer $id
     * @return Mercurio47
     */
    public function findById($id)
    {
        return Mercurio47::where('id', $id)->first();
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

        Mercurio47::where('id', $id)->update([
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

    public function buscarTrabajadorSubsidio($cedtra)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_trabajador",
                "params" => array(
                    'cedtra' => $cedtra
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
            });

        return array(
            'seguimientos' => $seguimientos,
            'campos_disponibles' => (new Mercurio47)->CamposDisponibles(),
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
            )
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
        $datos = Mercurio47::where('documento', $documento)
            ->where('coddoc', $coddoc)
            ->whereNotIn('estado', ['X', 'I'])
            ->select('cedtra as cedula', DB::raw("CONCAT_WS('', prinom, segnom, priape, segape) as nombre_completo"))
            ->get()
            ->toArray();

        return $datos;
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
        $out = $procesadorComando->toArray();
        if ($out['success'] == False) {
            return false;
        } else {
            return $out['data'];
        }
    }
}
