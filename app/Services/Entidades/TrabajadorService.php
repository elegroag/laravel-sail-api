<?php

namespace App\Services\Entidades;

use App\Exceptions\DebugException;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio31;
use App\Models\Mercurio10;
use App\Models\Mercurio07;
use App\Models\Mercurio12;
use App\Models\Mercurio13;
use App\Models\Mercurio37;
use App\Models\Mercurio01;
use App\Services\Utils\Comman;
use ParamsTrabajador;

class TrabajadorService
{

    private $tipopc = '1';
    private $user;
    private $db;
    public function __construct()
    {
        if (session()->has('documento')) {
            $this->user = session()->all();
        }
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
        ) > 0) {

            if (empty($estado)) {
                $conditions = "and m31.estado NOT IN('I') ";
            } else {
                $conditions = "and m31.estado='{$estado}' ";
            }


            return $this->db->inQueryAssoc(
                "SELECT m31.*,
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
                ORDER BY m31.fecsol ASC;"
            );
        }
        return array();
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

        $mercurio10 = $db->fetchOne("SELECT item, estado, campos_corregir
        FROM mercurio10
        WHERE numero='{$solicitud->getId()}' AND tipopc='{$this->tipopc}'
        ORDER BY item DESC LIMIT 1");

        $corregir = false;
        if ($mercurio10) {
            if ($mercurio10['estado'] == 'D') {
                $campos = $mercurio10['campos_corregir'];
                $corregir = explode(";", $campos);
            }
        }

        $mercurio13 = (new Mercurio13)->find("tipopc='{$this->tipopc}'", "order: auto_generado DESC");

        foreach ($mercurio13 as $m13) {
            $m12 = (new Mercurio12)->findFirst("coddoc='{$m13->getCoddoc()}'");
            $mercurio37 = (new Mercurio37)->findFirst("tipopc='{$this->tipopc}' and numero='{$solicitud->getId()}' and coddoc='{$m13->getCoddoc()}'");
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

        $mercurio01 = (new Mercurio01)->findFirst();
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

        $mercurio10 = $this->db->fetchOne("SELECT item, estado, campos_corregir
        FROM mercurio10
        WHERE numero='{$solicitud->getId()}' AND tipopc='{$this->tipopc}'
        ORDER BY item DESC LIMIT 1");

        $corregir = false;
        if ($mercurio10) {
            if ($mercurio10['estado'] == 'D') {
                $campos = $mercurio10['campos_corregir'];
                $corregir = explode(";", $campos);
            }
        }

        $mercurio13 = (new Mercurio13)->find("tipopc='{$this->tipopc}'", "order: auto_generado DESC");
        foreach ($mercurio13 as $m13) {
            $m12 = (new Mercurio12)->findFirst("coddoc='{$m13->getCoddoc()}'");
            $mercurio37 = (new Mercurio37)->findFirst("tipopc='{$this->tipopc}' and numero='{$solicitud->getId()}' and coddoc='{$m13->getCoddoc()}'");
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

        $mercurio01 = (new Mercurio01)->findFirst();
        $archivos_descargar = oficios_requeridos('T');
        return array(
            "disponibles" => $archivos_descargar,
            "archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        );
    }

    /**
     * loadDisplay function
     * @param Mercurio31 $solicitud
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
        /*  Tag::displayTo("nit", $trabajador['nit']);
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
            $solicitud->createAttributes($data);
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
        $id = (new Mercurio31)->maximum('id') + 1;
        $trabajador = new Mercurio31();
        $trabajador->createAttributes($data);
        $trabajador->setId($id);

        (new Mercurio37)->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        (new Mercurio10)->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        return $trabajador;
    }

    /**
     * createByFormData function
     * @param array $data
     * @return Mercurio31
     */
    public function createByFormData($data)
    {
        $trabajador = $this->create($data);
        $trabajador->setEstado("T");
        return $trabajador->save();
    }

    /**
     * findById function
     * @param integer $id
     * @return Mercurio31
     */
    public function findById($id)
    {
        return (new Mercurio31)->findFirst("id='{$id}'");
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
        $solicitud = (new Mercurio31)->findFirst("id='{$id}'");

        $cm37 = (new Mercurio37)->getCount(
            "tipopc='{$this->tipopc}' AND " .
                "numero='{$id}' AND " .
                "coddoc IN(SELECT coddoc FROM mercurio13 WHERE tipopc='{$this->tipopc}' AND obliga='S')"
        );

        $cm13 = (new Mercurio13)->getCount("*", "conditions: tipopc='{$this->tipopc}' and obliga='S'");
        if ($cm37 < $cm13) {
            throw new DebugException("Adjunte los archivos obligatorios", 500);
        }

        (new Mercurio31)->updateAll("usuario='{$usuario}', estado='P'", "conditions: id='{$id}'");

        $ai = (new Mercurio10)->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;

        $entity = (object) $solicitud->getArray();
        $entity->item = $ai;
        $solicitante = (new Mercurio07)->findFirst(" documento='{$solicitud->getDocumento()}' and coddoc='{$solicitud->getCoddoc()}' and tipo='{$solicitud->getTipo()}'");
        $entity->repleg = $solicitante->getNombre();
        $entity->razsoc = $solicitante->getNombre();
        $entity->nit = $solicitante->getDocumento();
        $entity->email = $solicitante->getEmail();
        $senderValidationCaja->send($this->tipopc, $entity);
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
        $seguimientos = $this->db->inQueryAssoc("SELECT * FROM mercurio10 WHERE numero='{$id}' AND tipopc='{$this->tipopc}' ORDER BY item DESC");
        foreach ($seguimientos as $ai => $row) {
            $campos = explode(';', $row['campos_corregir']);
            $seguimientos[$ai]['corregir'] = $campos;
        }
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

        $datos = $this->db->inQueryAssoc(
            "SELECT cedtra as 'cedula',
            CONCAT_WS('', prinom, segnom, priape, segape) as 'nombre_completo'
            FROM mercurio31
            WHERE
            documento='{$documento}' and
            coddoc='{$coddoc}' and
            estado NOT IN('X','I')"
        );
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
