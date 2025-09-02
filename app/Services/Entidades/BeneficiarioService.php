<?php
namespace App\Services\Entidades;

use App\Exceptions\DebugException;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Models\Mercurio10;
use App\Models\Mercurio12;
use App\Models\Mercurio13;
use App\Models\Mercurio34;
use App\Models\Mercurio37;
use App\Services\Utils\Comman;

class BeneficiarioService 
{

    private $tipopc = '4';
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
        //usuario empresa, unica solicitud de afiliación
        $documento = $this->user['documento'];
        $coddoc = $this->user['coddoc'];

        if ((new Mercurio34)->count(
            "*",
            "conditions: documento='{$documento}' AND coddoc='{$coddoc}'"
        ) > 0) {
            $conditions = (empty($estado)) ? " AND m34.estado NOT IN('I') " : " AND m34.estado='{$estado}' ";
            
            return $this->db->inQueryAssoc(
                "SELECT m34.*, 
                (SELECT COUNT(*) FROM mercurio10 as me10 WHERE me10.tipopc='{$this->tipopc}' and m34.id = me10.numero) as 'cantidad_eventos',
                (SELECT MAX(fecsis) FROM mercurio10 as mr10 WHERE mr10.tipopc='{$this->tipopc}' and m34.id = mr10.numero) as 'fecha_ultima_solicitud',
                (CASE 
                    WHEN m34.estado = 'T' THEN 'Temporal en edición'
                    WHEN m34.estado = 'D' THEN 'Devuelto'
                    WHEN m34.estado = 'A' THEN 'Aprobado'
                    WHEN m34.estado = 'X' THEN 'Rechazado'
                    WHEN m34.estado = 'P' THEN 'Pendiente De Validación CAJA'
                    WHEN m34.estado = 'I' THEN 'Inactiva'
                END) as estado_detalle,
                coddoc as tipo_documento
                FROM mercurio34 as m34 
                WHERE m34.documento='{$documento}' and m34.coddoc='{$coddoc}' {$conditions}
                ORDER BY m34.fecsol ASC;"
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

        $db = (object) DbBase::rawConnect();
        

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

        $mercurio13 = (new Mercurio13)->find("tipopc='{$this->tipopc}'");
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
        $html = view("partial/archivos_requeridos", [
            "load_archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        ]);
        return $html;
    }

    /**
     * loadDisplay function
     * @param Mercurio34 $solicitud
     * @return void
     */
    public function loadDisplay($solicitud)
    {
        /* Tag::displayTo("cedtra", $solicitud->getCedtra());
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
        Tag::displayTo("emailpri", $trabajador['mailr']);
        */
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
     * @return Mercurio34
     */
    public function create($data)
    {
        $id = (new Mercurio34)->maximum('id') + 1;
        $beneficiario = new Mercurio34();
        $beneficiario->createAttributes($data);
        $beneficiario->setId($id);

        (new Mercurio37)->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        (new Mercurio10)->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        return $beneficiario;
    }

    /**
     * createByFormData function
     * @param array $data
     * @return Mercurio34
     */
    public function createByFormData($data)
    {
        $beneficiario = $this->create($data);
        $beneficiario->setEstado("T");
        $beneficiario->save();
        return $beneficiario;
    }

    /**
     * findById function
     * @param integer $id
     * @return Mercurio34
     */
    public function findById($id)
    {
        return (new Mercurio34)->findFirst("id='{$id}'");
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
        $solicitud = (new Mercurio34)->findFirst("id='{$id}'");

        $cm37 = (new Mercurio37)->count(
            "tipopc='{$this->tipopc}' AND " .
                "numero='{$id}' AND " .
                "coddoc IN(SELECT coddoc FROM mercurio13 WHERE tipopc='{$this->tipopc}' AND obliga='S')"
        );

        $cm13 = (new Mercurio13)->count("*", "conditions: tipopc='{$this->tipopc}' AND obliga='S'");
        if ($cm37 < $cm13) {
            throw new DebugException("Adjunte los archivos obligatorios", 500);
        }

        (new Mercurio34)->updateAll("usuario='{$usuario}', estado='P'", "conditions: id='{$id}'");

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


    public function buscarBeneficiarioSubsidio($numdoc)
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaEmpresas",
                "metodo" => "informacion_beneficiario",
                "params" => $numdoc
            )
        );

        $datos_captura =  $procesadorComando->toArray();
        if ($datos_captura['success']) {
            return $datos_captura;
        } else {
            return false;
        }
    }

    public function dataArchivosRequeridos($solicitud)
    {

        if ($solicitud == false) return false;
        $archivos = array();

        $db =  DbBase::rawConnect();

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

        $mercurio13 = (new Mercurio13)->find("tipopc='{$this->tipopc}' ", "order: auto_generado DESC");
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
        $archivos_descargar = oficios_requeridos('B');
        return array(
            "disponibles" => $archivos_descargar,
            "archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        );
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
            'campos_disponibles' => (new Mercurio34)->CamposDisponibles(),
            'estados_detalles' => (new Mercurio10)->getArrayEstados()
        );
    }
}
