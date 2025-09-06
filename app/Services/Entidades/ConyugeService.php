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
use App\Models\Mercurio32;
use App\Models\Mercurio37;
use App\Services\Utils\Comman;
use Illuminate\Support\Facades\DB;

class ConyugeService
{

    private $tipopc = '3';
    private $user;
    private $tipo;
    private $db;

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
        //usuario empresa, unica solicitud de afiliación
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];

        if ((new Mercurio32)->getCount(
            "*",
            "conditions: documento='{$documento}' and coddoc='{$coddoc}'"
        ) == 0) {
            return [];
        }

        $conditions = (empty($estado)) ? " AND m32.estado NOT IN('I') " : " AND m32.estado='{$estado}' ";

        $sql =  "SELECT m32.*,
            (SELECT COUNT(*) FROM mercurio10 as me10 WHERE me10.tipopc='{$this->tipopc}' and m32.id = me10.numero) as 'cantidad_eventos',
            (SELECT MAX(fecsis) FROM mercurio10 as mr10 WHERE mr10.tipopc='{$this->tipopc}' and m32.id = mr10.numero) as 'fecha_ultima_solicitud',
            (CASE
                WHEN m32.estado = 'T' THEN 'Temporal en edición'
                WHEN m32.estado = 'D' THEN 'Devuelto'
                WHEN m32.estado = 'A' THEN 'Aprobado'
                WHEN m32.estado = 'X' THEN 'Rechazado'
                WHEN m32.estado = 'P' THEN 'Pendiente De Validación CAJA'
                WHEN m32.estado = 'I' THEN 'Inactiva'
            END) as estado_detalle,
            coddoc as tipo_documento
            FROM mercurio32 as m32
            WHERE m32.documento='{$documento}' AND m32.coddoc='{$coddoc}' {$conditions}
            ORDER BY m32.fecsol ASC;";

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
        $archivos = array();
        $mercurio13 = Mercurio13::where('tipopc', $this->tipopc)->orderBy('auto_generado', 'desc')->get();

        $mercurio10 = Mercurio10::where('numero', $solicitud->getId())
            ->where('tipopc', $this->tipopc)
            ->orderBy('item', 'desc')
            ->first();

        $corregir = false;
        if ($mercurio10 && $mercurio10->estado == 'D') {
            $campos = $mercurio10->campos_corregir;
            $corregir = explode(';', $campos);
        }

        foreach ($mercurio13 as $m13) {

            $m12 = Mercurio12::where('coddoc', $m13->getCoddoc())->first();

            $mercurio37 = Mercurio37::where('tipopc', $this->tipopc)
                ->where('numero', $solicitud->getId())
                ->where('coddoc', $m13->getCoddoc())
                ->first();

            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    Mercurio37::where('tipopc', $this->tipopc)
                        ->where('numero', $solicitud->getId())
                        ->where('coddoc', $m13->getCoddoc())
                        ->delete();

                    $mercurio37 = false;
                }
            }

            $obliga = ($m13->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo = new \stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $solicitud->getId();
            $archivo->coddoc = $m13->getCoddoc();
            $archivo->detalle = $m12->getDetalle();
            $archivo->diponible = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $html = view("conyuge/tmp/archivos_requeridos", array(
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
            $campos = $mercurio10->campos_corregir;
            $corregir = explode(";", $campos);
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
            $archivo = $m13->getArray();
            $archivo['obliga'] = ($m13->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo['id'] = $solicitud->getId();
            $archivo['detalle'] = capitalize($m12->getDetalle());
            $archivo['diponible'] = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo['corrige'] = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = Mercurio01::first();
        $archivos_descargar = oficios_requeridos('C');
        return array(
            "disponibles" => $archivos_descargar,
            "archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        );
    }

    /**
     * loadDisplay function
     * @param Mercurio32 $solicitud
     * @return void
     */
    public function loadDisplay($solicitud)
    {
        /*  Tag::displayTo("cedtra", $solicitud->getCedtra());
        Tag::displayTo("tipdoc", $solicitud->getTipdoc());
        Tag::displayTo("nit", $solicitud->getNit());
        Tag::displayTo("telefono", $solicitud->getTelefono());
        Tag::displayTo("celular", $solicitud->getCelular());
        Tag::displayTo("email", $solicitud->getEmail());
        Tag::displayTo("prinom", $solicitud->getPrinom());
        Tag::displayTo("segnom", $solicitud->getSegnom());
        Tag::displayTo("priape", $solicitud->getPriape());
        Tag::displayTo("segape", $solicitud->getSegape());
        Tag::displayTo("razsoc", $solicitud->getRazsoc());
        Tag::displayTo("direccion", $solicitud->getDireccion());
        Tag::displayTo("sexo", $solicitud->getSexo());
        Tag::displayTo("codciu", $solicitud->getCodciu());
        Tag::displayTo("codzon", $solicitud->getCodzon());
        Tag::displayTo("ciunac", $solicitud->getCiunac());
        Tag::displayTo("orisex", (!$solicitud->getOrisex()) ? '1' : $solicitud->getOrisex());
        Tag::displayTo("estciv", (!$solicitud->getEstciv()) ? '1' : $solicitud->getEstciv());
        Tag::displayTo("cabhog", (!$solicitud->getcabhog()) ? 'N' :  $solicitud->getcabhog());
        Tag::displayTo("barrio", $solicitud->getBarrio());
        Tag::displayTo("salario", $solicitud->getSalario());
        Tag::displayTo("tipsal", $solicitud->getTipsal());
        Tag::displayTo("captra", (!$solicitud->getCaptra()) ? 'N' : $solicitud->getCaptra());
        Tag::displayTo("tipdis", (!$solicitud->getTipdis()) ? '00' : $solicitud->getTipdis());
        Tag::displayTo("nivedu", $solicitud->getNivedu());
        Tag::displayTo("rural", $solicitud->getRural());
        Tag::displayTo("horas", (!$solicitud->getHoras()) ? '240' : $solicitud->getHoras());
        Tag::displayTo("tipcon", $solicitud->getTipcon());
        Tag::displayTo("trasin", (!$solicitud->getTrasin()) ? 'N' : $solicitud->getTrasin());
        Tag::displayTo("vivienda", $solicitud->getVivienda());
        Tag::displayTo("tipafi", (!$solicitud->getTipafi()) ? '1' : $solicitud->getTipafi());
        Tag::displayTo("profesion", $solicitud->getProfesion());
        Tag::displayTo("cargo", $solicitud->getCargo());
        Tag::displayTo("autoriza", (!$solicitud->getAutoriza()) ? 'S' : $solicitud->getAutoriza());
        Tag::displayTo("usuario", $solicitud->getUsuario());
        Tag::displayTo("facvul", (!$solicitud->getFacvul()) ? '12' : $solicitud->getFacvul());
        Tag::displayTo("peretn", (!$solicitud->getPeretn()) ? '7' : $solicitud->getPeretn());
        Tag::displayTo("dirlab", $solicitud->getDirlab());
        Tag::displayTo("ciulab", $solicitud->getCiulab());
        Tag::displayTo("ruralt", $solicitud->getRuralt());
        Tag::displayTo("comision", (!$solicitud->getComision()) ? 'N' : $solicitud->getComision());
        Tag::displayTo("tipjor", (!$solicitud->getTipjor()) ? 'C' : $solicitud->getTipjor());
        Tag::displayTo("codsuc", $solicitud->getCodsuc());
        Tag::displayTo("tippag", $solicitud->getTippag());
        Tag::displayTo("numcue", $solicitud->getNumcue());
        Tag::displayTo("fecnac", ($solicitud->getFecnac() instanceof Date) ? $solicitud->getFecnac()->getUsingFormatDefault() : '');
        Tag::displayTo("fecing", ($solicitud->getFecing() instanceof Date) ? $solicitud->getFecing()->getUsingFormatDefault() : '');
        Tag::displayTo("labora_otra_empresa",  $solicitud->getOtraEmpresa());
        Tag::displayTo("pub_indigena_id",  $solicitud->getPubIndigenaId());
        Tag::displayTo("resguardo_id",  $solicitud->getResguardoId()); */
    }


    /**
     * loadDisplaySubsidio function
     * @param array $trabajador
     * @return void
     */
    public function loadDisplaySubsidio($trabajador)
    {
        /* Tag::displayTo("nit", $trabajador['nit']);
        Tag::displayTo("telefono", $trabajador['telefono']);
        Tag::displayTo("email", $trabajador['email']);
        Tag::displayTo("segnom", $trabajador['segnom']);
        Tag::displayTo("priape", $trabajador['priape']);
        Tag::displayTo("segape", $trabajador['segape']);
        Tag::displayTo("matmer", $trabajador['matmer']);
        Tag::displayTo("direccion", $trabajador['direccion']);
        Tag::displayTo("celular", $trabajador['telr']);
        Tag::displayTo("celpri", $trabajador['telt']);
        Tag::displayTo("dirpri", $trabajador['dirpri']);
        Tag::displayTo("emailpri", $trabajador['mailr']); */
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
     * @return Mercurio32
     */
    public function create($data)
    {
        $conyuge = new Mercurio32($data);
        $conyuge->save();
        $id = $conyuge->getId();

        Mercurio37::where('tipopc', $this->tipopc)->where('numero', $id)->delete();
        Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->delete();

        return $conyuge;
    }

    /**
     * createByFormData function
     * @param array $data
     * @return Mercurio32
     */
    public function createByFormData($data)
    {
        $data['estado'] = 'T';
        $conyuge = $this->create($data);
        return $conyuge;
    }

    /**
     * findById function
     * @param integer $id
     * @return Mercurio32
     */
    public function findById($id)
    {
        return Mercurio32::where('id', $id)->first();
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
                "coddoc IN(SELECT coddoc FROM mercurio13 WHERE tipopc='{$this->tipopc}' and obliga='S')"
        );

        $cm13 = (new Mercurio13)->getCount(
            "*",
            "conditions: tipopc='{$this->tipopc}' and obliga='S'"
        );
        if ($cm37 < $cm13) {
            throw new DebugException("Adjunte los archivos obligatorios", 500);
        }

        Mercurio32::where('id', $id)->update([
            'usuario' => $usuario,
            'estado' => 'P',
        ]);

        $ai = Mercurio10::where('tipopc', $this->tipopc)->where('numero', $id)->maximum("item") + 1;

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


    public function buscarConyugeSubsidio($cedcon)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_conyuge",
                "params" => $cedcon
            )
        );
        $salida =  $procesadorComando->toArray();
        if ($salida['success']) {
            return ($salida['data']) ? $salida['data'] : false;
        } else {
            return false;
        }
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
            'campos_disponibles' => (new Mercurio32)->CamposDisponibles(),
            'estados_detalles' => (new Mercurio10)->getArrayEstados()
        );
    }

    public function paramsApi()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_conyuges"
            )
        );
        $paramsTrabajador = new ParamsTrabajador();
        $paramsTrabajador->setDatosCaptura($procesadorComando->toArray());
    }

    public function findRequestByDocumentoCoddoc($documento, $coddoc)
    {
        $datos = Mercurio32::where('documento', $documento)
            ->where('coddoc', $coddoc)
            ->whereNotIn('estado', ['X', 'I'])
            ->select('cedcon as cedula', DB::raw("CONCAT_WS('', prinom, segnom, priape, segape) as nombre_completo"))
            ->get();

        return $datos->toArray();
    }

    public function findRequestByCedtra($cedtra)
    {
        $datos = Mercurio32::where('cedtra', $cedtra)
            ->whereNotIn('estado', ['X', 'I'])
            ->select('cedcon as cedula', DB::raw("CONCAT_WS('', prinom, segnom, priape, segape) as nombre_completo"))
            ->get();

        return $datos->toArray();
    }

    public function findApiConyugesByNit($nit)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "listar_conyuges",
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
