<?php

namespace App\Services\Entidades;

use App\Exceptions\DebugException;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio12;
use App\Models\Mercurio14;
use App\Models\Mercurio37;
use App\Models\Mercurio47;
use App\Models\Mercurio10;
use App\Models\Mercurio07;
use App\Services\Utils\Comman;

class ActualizaEmpresaService
{

    private $tipopc = "5";
    private $user;
    private $db;

    /**
     * __construct function
     * @param bool $init
     * @param Services $servicios
     */
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
        return ($salida['success'] == true) ? $salida : false;
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

        $mercurio14 = (new Mercurio14)->find("tipopc='{$this->tipopc}' AND tipsoc='{$solicitud->getTipsoc()}'");
        foreach ($mercurio14 as $m14) {
            $m12 = (new Mercurio12)->findFirst("coddoc='{$m14->getCoddoc()}'");
            $mercurio37 = (new Mercurio37)->findFirst("tipopc='{$this->tipopc}' and numero='{$solicitud->getId()}' and coddoc='{$m14->getCoddoc()}'");
            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }
            $obliga = ($m14->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo = new \stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $solicitud->getId();
            $archivo->coddoc = $m14->getCoddoc();
            $archivo->detalle = $m12->getDetalle();
            $archivo->diponible = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo->corrige = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = (new Mercurio01)->findFirst();
        $html = view('partials.archivos_requeridos', [
            "load_archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        ]);
        return $html;
    }

    /**
     * dataArchivosRequeridos function
     * @param Mercurio47 $solicitud
     * @return array
     */
    public function dataArchivosRequeridos($solicitud)
    {

        $archivos = array();
        if ($solicitud == false || is_null($solicitud)) return false;
        $archivos = array();

        $mercurio10 = $this->db->fetchOne("SELECT
            item,
            estado,
            campos_corregir
            FROM mercurio10
            WHERE numero='{$solicitud->getId()}' AND
            tipopc='{$this->tipopc}'
            ORDER BY item DESC
            LIMIT 1
        ");

        $corregir = false;
        if ($mercurio10) {
            if ($mercurio10['estado'] == 'D') {
                $campos = $mercurio10['campos_corregir'];
                $corregir = explode(";", $campos);
            }
        }

        $mercurio14 = (new Mercurio14)->find("tipopc='{$this->tipopc}'", "order: auto_generado DESC");
        foreach ($mercurio14 as $m14) {
            $m12 = (new Mercurio12)->findFirst("coddoc='{$m14->getCoddoc()}'");
            $mercurio37 = (new Mercurio37)->findFirst("tipopc='{$this->tipopc}' and numero='{$solicitud->getId()}' and coddoc='{$m14->getCoddoc()}'");
            $corrige = false;

            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) $corrige = true;
            }
            $archivo = $m14->getArray();
            $archivo['obliga'] = ($m14->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo['id'] = $solicitud->getId();
            $archivo['coddoc'] = $m14->getCoddoc();
            $archivo['detalle'] = capitalize($m12->getDetalle());
            $archivo['diponible'] = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo['corrige'] = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = (new Mercurio01)->findFirst();
        $archivos_descargar = oficios_requeridos('U');
        return array(
            "disponibles" => $archivos_descargar,
            "archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        );
    }

    /**
     * loadDisplay function
     * @param Mercurio47 $solicitud
     * @return void
     */
    public function loadDisplay($solicitud)
    {
        /* Tag::displayTo("nit", $solicitud->getNit());
        Tag::displayTo("tipdoc", $solicitud->getTipdoc());
        Tag::displayTo("digver", $this->digver($solicitud->getNit()));
        Tag::displayTo("id", $solicitud->getId());
        Tag::displayTo("sigla", $solicitud->getSigla());
        Tag::displayTo("calemp", $solicitud->getCalemp());
        Tag::displayTo("cedrep", $solicitud->getCedrep());
        Tag::displayTo("repleg", $solicitud->getRepleg());
        Tag::displayTo("telefono", $solicitud->getTelefono());
        Tag::displayTo("celular", $solicitud->getCelular());
        Tag::displayTo("email", $solicitud->getEmail());
        Tag::displayTo("fecini", $solicitud->getFeciniString());
        Tag::displayTo("tottra", $solicitud->getTottra());
        Tag::displayTo("valnom", $solicitud->getValnom());
        Tag::displayTo("dirpri", $solicitud->getDirpri());
        Tag::displayTo("ciupri", $solicitud->getCiupri());
        Tag::displayTo("celpri", $solicitud->getCelpri());
        Tag::displayTo("emailpri", $solicitud->getEmailpri());
        Tag::displayTo("prinom", $solicitud->getPrinom());
        Tag::displayTo("segnom", $solicitud->getSegnom());
        Tag::displayTo("priape", $solicitud->getPriape());
        Tag::displayTo("segape", $solicitud->getSegape());
        Tag::displayTo("razsoc", $solicitud->getRazsoc());
        Tag::displayTo("tipper", $solicitud->getTipper());
        Tag::displayTo("matmer", $solicitud->getMatmer());
        Tag::displayTo("direccion", $solicitud->getDireccion());
        Tag::displayTo("tipsoc", $solicitud->getTipsoc());
        Tag::displayTo("codact", $solicitud->getCodact());
        Tag::displayTo("tipemp", $solicitud->getTipemp());
        Tag::displayTo("codcaj", $solicitud->getCodcaj());
        Tag::displayTo("coddocrepleg", $solicitud->getCoddocrepleg()); */
    }

    function loadDisplaySubsidio($empresa)
    {
        /* Tag::displayTo("tipdoc", $empresa['coddoc']);
        Tag::displayTo("digver", $empresa['digver']);
        Tag::displayTo("nit", $empresa['nit']);
        Tag::displayTo("sigla", $empresa['sigla']);
        Tag::displayTo("calemp", $empresa['calemp']);
        Tag::displayTo("cedrep", $empresa['cedrep']);
        Tag::displayTo("repleg", $empresa['repleg']);
        Tag::displayTo("telefono", $empresa['telefono']);
        Tag::displayTo("email", $empresa['email']);
        Tag::displayTo("tottra", $empresa['tottra']);
        Tag::displayTo("ciupri", $empresa['ciupri']);
        Tag::displayTo("prinom", $empresa['prinom']);
        Tag::displayTo("segnom", $empresa['segnom']);
        Tag::displayTo("priape", $empresa['priape']);
        Tag::displayTo("segape", $empresa['segape']);
        Tag::displayTo("razsoc", $empresa['razsoc']);
        Tag::displayTo("tipper", $empresa['tipper']);
        Tag::displayTo("matmer", $empresa['matmer']);
        Tag::displayTo("direccion", $empresa['direccion']);
        Tag::displayTo("tipsoc", $empresa['tipsoc']);
        Tag::displayTo("codact", $empresa['codact']);
        Tag::displayTo("tipemp", $empresa['tipemp']);
        Tag::displayTo("codcaj", $empresa['codcaj']);
        Tag::displayTo("coddocrepleg", $empresa['coddocrepleg']);
        Tag::displayTo("celular", $empresa['telr']);
        Tag::displayTo("celpri", $empresa['telt']);
        Tag::displayTo("dirpri", $empresa['dirpri']);
        Tag::displayTo("emailpri", $empresa['mailr']); */
    }

    /**
     * update function
     * @param integer $id
     * @param array $data
     * @return Mercurio47
     */
    public function update($id, $data)
    {
        $empresa = $this->findById($id);
        if ($empresa != false) {
            $empresa->createAttributes($data);
            $empresa->save();
            return $empresa;
        }
        return false;
    }

    /**
     * updateByFormData function
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateByFormData($id, $data)
    {
        $empresa = $this->findById($id);
        if ($empresa) {
            $empresa->createAttributes($data);
            return $empresa->save();
        } else {
            return false;
        }
    }

    /**
     * create function
     * @param array $data
     * @return Mercurio47
     */
    public function createByFormData($data)
    {
        $sec = (new Mercurio47)->maximum('id') + 1;
        $solicitud = new Mercurio47();
        $solicitud->createAttributes($data);
        $solicitud->setId($sec);
        $solicitud->save();
        return $solicitud;
    }

    /**
     * findById function
     * @param integer $id
     * @return Mercurio47
     */
    public function findById($id)
    {
        return (new Mercurio47)->findFirst("id='{$id}'");
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
        $solicitud = (new Mercurio47)->findFirst("id='{$id}'");

        $cm37 = (new Mercurio37)->getCount(
            "tipopc='{$this->tipopc}' AND " .
                "numero='{$id}' AND " .
                "coddoc IN(SELECT coddoc FROM mercurio14 WHERE tipopc='{$this->tipopc}' and obliga='S')"
        );

        $cm14 = (new Mercurio14)->getCount("*", "conditions: tipopc='{$this->tipopc}' and obliga='S'");
        if ($cm37 < $cm14) {
            throw new DebugException("Adjunte los archivos obligatorios", 500);
        }

        (new Mercurio47)->updateAll("usuario='{$usuario}', estado='P'", "conditions: id='{$id}'");

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


    public function consultaSeguimiento($id)
    {
        $seguimientos = $this->db->inQueryAssoc("SELECT * FROM mercurio10 WHERE numero='{$id}' AND tipopc='{$this->tipopc}' ORDER BY item DESC");
        foreach ($seguimientos as $ai => $row) {
            $campos = explode(';', $row['campos_corregir']);
            $seguimientos[$ai]['corregir'] = $campos;
        }
        return array(
            'seguimientos' => $seguimientos,
            'campos_disponibles' => (new Mercurio47)->CamposDisponibles(),
            'estados_detalles' => (new Mercurio10)->getArrayEstados()
        );
    }

    public function digver($mnit)
    {
        $arreglo = array(71, 67, 59, 53, 47, 43, 41, 37, 29, 23, 19, 17, 13, 7, 3);
        $nit = sprintf("%015s", $mnit);
        $suma = 0;
        for ($i = 1; $i <= count($arreglo); $i++) {
            $suma += (int)(substr($nit, $i - 1, 1)) * $arreglo[$i - 1];
        }
        $retorno = $suma % 11;
        if ($retorno >= 2) $retorno = 11 - $retorno;
        return $retorno;
    }
}
