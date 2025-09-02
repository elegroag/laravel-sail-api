<?php
class PensionadoService 
{
    private $tipopc = 9;
    private $tipsoc = '08';

    public function __construct()
    {
    }

    /**
     * findAllByEstado function
     * @param string $estado
     * @return array
     */
    public function findAllByEstado($estado = '')
    {
        $user = Auth::getActiveIdentity();

        //usuario empresa, unica solicitud de afiliación
        $documento = $user['documento'];
        $coddoc = $user['coddoc'];

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
        $db->setFetchMode(DbBase::DB_ASSOC);

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

        $mercurio14 = $this->Mercurio14->find("tipopc='{$this->tipopc}'");
        foreach ($mercurio14 as $m14) {
            $m12 = $this->Mercurio12->findFirst("coddoc='{$m14->getCoddoc()}'");
            $mercurio37 = $this->Mercurio37->findFirst("tipopc='{$this->tipopc}' and numero='{$solicitud->getId()}' and coddoc='{$m14->getCoddoc()}'");
            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }
            $obliga = ($m14->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo = new stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $solicitud->getId();
            $archivo->coddoc = $m14->getCoddoc();
            $archivo->detalle = $m12->getDetalle();
            $archivo->diponible = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo->corrige = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = $this->Mercurio01->findFirst();
        $html = View::render("pensionado/tmp/archivos_requeridos",  array(
            "load_archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        ));
        return $html;
    }

    /**
     * loadDisplay function
     * @param Mercurio38 $solicitud
     * @return void
     */
    public function loadDisplay($solicitud)
    {
        Tag::displayTo("calemp", "P");
        Tag::displayTo("codact", "201010");
        Tag::displayTo("id", $solicitud->getId());
        Tag::displayTo("calemp", $solicitud->getCalemp());
        Tag::displayTo("codact", $solicitud->getCodact());
        Tag::displayTo("codcaj", $solicitud->getCodcaj());
        Tag::displayTo("coddocrepleg", $solicitud->getCoddocrepleg());
    }

    public function loadDisplaySubsidio($empresa)
    {
        Tag::displayTo("calemp", "P");
        Tag::displayTo("codact", "201010");
        Tag::displayTo("calemp", 'P');
        Tag::displayTo("cedtra", $empresa['cedrep']);
        Tag::displayTo("codact", $empresa['codact']);
        Tag::displayTo("codcaj", $empresa['codcaj']);
        Tag::displayTo("coddocrepleg", $empresa['coddocrepleg']);
    }

    /**
     * update function
     * @param integer $id
     * @param array $data
     * @return Mercurio38
     */
    public function update($id, $data)
    {
        $empresa = $this->findById($id);
        if ($empresa != false) {
            $empresa->setTransaction(self::$transaction);
            $empresa->createAttributes($data);
            $this->salvar($empresa, __LINE__);
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
        $solicitud = $this->findById($id);
        if ($solicitud) {
            $solicitud->setTransaction(self::$transaction);
            $solicitud->createAttributes($data);
            return $this->salvar($solicitud, __LINE__);
        } else {
            return false;
        }
    }

    /**
     * create function
     * @param array $data
     * @return Mercurio38
     */
    public function create($data)
    {
        $id = $this->Mercurio38->maximum('id') + 1;
        $pensionado = new Mercurio38();
        $pensionado->setTransaction(self::$transaction);
        $pensionado->createAttributes($data);
        $pensionado->setId($id);

        $this->Mercurio37->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        $this->Mercurio10->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        return $pensionado;
    }

    /**
     * create function
     * @param array $data
     * @return Mercurio38
     */
    public function createByFormData($data)
    {
        $pensionado = $this->create($data);
        $pensionado->setEstado("T");
        $this->salvar($pensionado, __LINE__);
        return $pensionado;
    }

    /**
     * findById function
     * @param integer $id
     * @return Mercurio38
     */
    public function findById($id)
    {
        return $this->Mercurio38->findFirst("id='{$id}'");
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
        $solicitud = $this->Mercurio38->findFirst("id='{$id}'");

        $cm37 = $this->Mercurio37->count(
            "tipopc='{$this->tipopc}' AND " .
                "numero='{$id}' AND " .
                "coddoc IN(SELECT coddoc FROM mercurio14 WHERE tipopc='{$this->tipopc}' AND tipsoc='{$this->tipsoc}' AND obliga='S')"
        );

        $cm14 = $this->Mercurio14->count("*", "conditions: tipopc='{$this->tipopc}' and tipsoc='{$this->tipsoc}' and obliga='S'");
        if ($cm37 < $cm14) {
            throw new Exception("Adjunte los archivos obligatorios", 500);
        }

        $this->Mercurio38->updateAll("usuario='{$usuario}', estado='P'", "conditions: id='{$id}'");

        $ai = $this->Mercurio10->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;

        $entity = (object) $solicitud->getArray();
        $entity->item = $ai;
        $solicitante = $this->Mercurio07->findFirst(" documento='{$solicitud->getDocumento()}' and coddoc='{$solicitud->getCoddoc()}' and tipo='{$solicitud->getTipo()}'");
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
            'campos_disponibles' => $this->Mercurio38->CamposDisponibles(),
            'estados_detalles' => $this->Mercurio10->getArrayEstados()
        );
    }

    public function dataArchivosRequeridos($solicitud)
    {
        Core::importHelper('files');
        if ($solicitud == false) return false;
        $archivos = array();

        $db = (object) DbBase::rawConnect();
        $db->setFetchMode(DbBase::DB_ASSOC);

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

        $mercurio14 = $this->Mercurio14->find("tipopc='{$this->tipopc}'", "order: auto_generado DESC");
        foreach ($mercurio14 as $m14) {
            $m12 = $this->Mercurio12->findFirst("coddoc='{$m14->getCoddoc()}'");
            $mercurio37 = $this->Mercurio37->findFirst("tipopc='{$this->tipopc}' and numero='{$solicitud->getId()}' and coddoc='{$m14->getCoddoc()}'");
            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $corrige = true;
                }
            }

            $archivo = $m14->getArray();
            $archivo['obliga'] = ($m14->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo['id'] = $solicitud->getId();
            $archivo['detalle'] = capitalize($m12->getDetalle());
            $archivo['diponible'] = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivo['corrige'] = $corrige;
            $archivos[] = $archivo;
        }

        $mercurio01 = $this->Mercurio01->findFirst();
        $archivos_descargar = oficios_requeridos('O');
        return array(
            "disponibles" => $archivos_descargar,
            "archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        );
    }

    public function paramsApi()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_empresa"
            ),
            false
        );

        $paramsPensionado = new ParamsPensionado();
        $paramsPensionado->setDatosCaptura($procesadorComando->toArray());

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
}
