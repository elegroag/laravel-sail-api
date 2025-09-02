<?php
class IndependienteService 
{
    private $tipopc = 13;
    private $tipsoc = '08';

    /**
     * __construct function
     * @param bool $init
     * @param Services $servicios
     */
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
            $conditions = "and m41.estado NOT IN('I') ";
        } else {
            $conditions = "and m41.estado='{$estado}' ";
        }
        $sql = "SELECT 
            m41.*, 
            concat_ws(' ', m41.prinom, m41.segnom, m41.priape, m41.segape) as razsoc, 
            concat_ws(' ', m41.prinom, m41.segnom, m41.priape, m41.segape) as repleg, 
            m41.codzon, 
            m41.codciu, 
            (SELECT COUNT(*) FROM mercurio10 as me10 WHERE me10.tipopc='{$this->tipopc}' and m41.id = me10.numero) as cantidad_eventos, 
            (SELECT MAX(fecsis) FROM mercurio10 as mr10 WHERE mr10.tipopc='{$this->tipopc}' and m41.id = mr10.numero) as fecha_ultima_solicitud,
            (CASE 
                WHEN m41.estado = 'T' THEN 'Temporal' 
                WHEN m41.estado = 'D' THEN 'Devuelto' 
                WHEN m41.estado = 'A' THEN 'Aprobado' 
                WHEN m41.estado = 'X' THEN 'Rechazado' 
                WHEN m41.estado = 'P' THEN 'Pendiente de validación' 
                WHEN m41.estado = 'I' THEN 'Inactiva' 
            END) as estado_detalle, 
            'NATURAL' as tipo_persona, 
            m41.coddoc as tipo_documento,
            gener09.detzon as detalle_zona 
            FROM mercurio41 as m41 
            LEFT JOIN gener09 ON gener09.codzon = m41.codzon 
            WHERE m41.documento='{$documento}' and 
            m41.coddoc='{$coddoc}' {$conditions} 
            ORDER BY m41.fecini ASC;";

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

    /**
     * archivosRequeridos function
     * @param Mercurio41 $solicitud
     * @return string
     */
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

        $mercurio14 = $this->Mercurio14->find("tipopc='{$this->tipopc}' AND tipsoc='{$this->tipsoc}'");
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
        $html = View::render("independiente/tmp/archivos_requeridos", array(
            "load_archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        ));
        return $html;
    }


    /**
     * dataArchivosRequeridos function
     * @param Mercurio41 $solicitud
     * @return array
     */
    public function dataArchivosRequeridos($solicitud)
    {
        Core::importHelper('files');

        $this->db->setFetchMode(DbBase::DB_ASSOC);

        if ($solicitud == false || is_null($solicitud)) return false;
        $archivos = array();

        $mercurio10 = $this->db->fetchOne("SELECT item, estado, campos_corregir 
        FROM mercurio10 
        WHERE numero='{$solicitud->getId()}' AND 
        tipopc='{$this->tipopc}' 
        ORDER BY item DESC LIMIT 1");

        $corregir = false;
        if ($mercurio10) {
            if ($mercurio10['estado'] == 'D') {
                $campos = $mercurio10['campos_corregir'];
                $corregir = explode(";", $campos);
            }
        }

        $mercurio14 = $this->Mercurio14->find("tipopc='{$this->tipopc}' AND tipsoc='{$this->tipsoc}' ", "order: auto_generado DESC");
        foreach ($mercurio14 as $m14) {
            $m12 = $this->Mercurio12->findFirst("coddoc='{$m14->getCoddoc()}'");
            $mercurio37 = $this->Mercurio37->findFirst("tipopc='{$this->tipopc}' and numero='{$solicitud->getId()}' and coddoc='{$m14->getCoddoc()}'");
            $corrige = false;
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) $corrige = true;
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
        $archivos_descargar = oficios_requeridos('I');
        return array(
            "disponibles" => $archivos_descargar,
            "archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        );
    }

    /**
     * loadDisplay function
     * @param Mercurio41 $solicitud
     * @return void
     */
    public function loadDisplay($solicitud)
    {
        Tag::displayTo("cedtra", $solicitud->getCedtra());
        Tag::displayTo("tipdoc", $solicitud->getTipdoc());
        Tag::displayTo("telefono", $solicitud->getTelefono());
        Tag::displayTo("celular", $solicitud->getCelular());
        Tag::displayTo("email", $solicitud->getEmail());
        Tag::displayTo("prinom", $solicitud->getPrinom());
        Tag::displayTo("segnom", $solicitud->getSegnom());
        Tag::displayTo("priape", $solicitud->getPriape());
        Tag::displayTo("segape", $solicitud->getSegape());
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
        Tag::displayTo("captra", (!$solicitud->getCaptra()) ? 'N' : $solicitud->getCaptra());
        Tag::displayTo("tipdis", (!$solicitud->getTipdis()) ? '00' : $solicitud->getTipdis());
        Tag::displayTo("nivedu", $solicitud->getNivedu());
        Tag::displayTo("rural", $solicitud->getRural());
        Tag::displayTo("vivienda", $solicitud->getVivienda());
        Tag::displayTo("tipafi", (!$solicitud->getTipafi()) ? '1' : $solicitud->getTipafi());
        Tag::displayTo("cargo", $solicitud->getCargo());
        Tag::displayTo("autoriza", (!$solicitud->getAutoriza()) ? 'S' : $solicitud->getAutoriza());
        Tag::displayTo("usuario", $solicitud->getUsuario());
        Tag::displayTo("facvul", (!$solicitud->getFacvul()) ? '12' : $solicitud->getFacvul());
        Tag::displayTo("peretn", (!$solicitud->getPeretn()) ? '7' : $solicitud->getPeretn());
        Tag::displayTo("tippag", $solicitud->getTippag());
        Tag::displayTo("numcue", $solicitud->getNumcue());
        Tag::displayTo("fecnac", ($solicitud->getFecnac() instanceof Date) ? $solicitud->getFecnac()->getUsingFormatDefault() : '');
        Tag::displayTo("fecini", ($solicitud->getFecini() instanceof Date) ? $solicitud->getFecini()->getUsingFormatDefault() : '');
        Tag::displayTo("pub_indigena_id",  $solicitud->getPub_indigena_id());
        Tag::displayTo("resguardo_id",  $solicitud->getResguardo_id());
    }

    public function loadDisplaySubsidio($trabajador)
    {
        Tag::displayTo("cedtra", $trabajador['cedtra']);
        Tag::displayTo("tipdoc", $trabajador['tipdoc']);
        Tag::displayTo("telefono", $trabajador['telefono']);
        Tag::displayTo("celular", $trabajador['celular']);
        Tag::displayTo("email", $trabajador['email']);
        Tag::displayTo("prinom", $trabajador['prinom']);
        Tag::displayTo("segnom", $trabajador['segnom']);
        Tag::displayTo("priape", $trabajador['priape']);
        Tag::displayTo("segape", $trabajador['segape']);
        Tag::displayTo("direccion", $trabajador['direccion']);
        Tag::displayTo("sexo", $trabajador['sexo']);
        Tag::displayTo("codciu", $trabajador['codciu']);
        Tag::displayTo("codzon", $trabajador['codzon']);
        Tag::displayTo("ciunac", $trabajador['ciunac']);
        Tag::displayTo("orisex", $trabajador['orisex']);
        Tag::displayTo("estciv", $trabajador['estciv']);
        Tag::displayTo("cabhog", $trabajador['cabhog']);
        Tag::displayTo("barrio", $trabajador['barrio']);
        Tag::displayTo("salario", $trabajador['salario']);
        Tag::displayTo("captra", $trabajador['captra']);
        Tag::displayTo("tipdis", $trabajador['tipdis']);
        Tag::displayTo("nivedu", $trabajador['nivedu']);
        Tag::displayTo("rural", $trabajador['rural']);
        Tag::displayTo("vivienda", $trabajador['vivienda']);
        Tag::displayTo("tipafi", $trabajador['tipafi']);
        Tag::displayTo("cargo", $trabajador['cargo']);
        Tag::displayTo("autoriza", $trabajador['autoriza']);
        Tag::displayTo("facvul", $trabajador['facvul']);
        Tag::displayTo("peretn", $trabajador['peretn']);
        Tag::displayTo("tippag", $trabajador['tippag']);
        Tag::displayTo("numcue", $trabajador['numcue']);
        Tag::displayTo("fecnac", $trabajador['fecnac']);
    }

    /**
     * update function
     * @param integer $id
     * @param array $data
     * @return Mercurio41
     */
    public function update($id, $data)
    {
        $independiente = $this->findById($id);
        if ($independiente != false) {
            $independiente->setTransaction(self::$transaction);
            $independiente->createAttributes($data);
            $this->salvar($independiente, __LINE__);
            return $independiente;
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
        $independiente = $this->findById($id);
        if ($independiente) {
            $independiente->setTransaction(self::$transaction);
            $independiente->createAttributes($data);
            return $this->salvar($independiente, __LINE__);
        } else {
            return false;
        }
    }

    /**
     * create function
     * @param array $data
     * @return Mercurio41
     */
    public function create($data)
    {
        $id = $this->Mercurio41->maximum('id') + 1;
        $independiente = new Mercurio41();
        $independiente->setTransaction(self::$transaction);
        $independiente->createAttributes($data);
        $independiente->setId($id);

        $this->Mercurio37->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        $this->Mercurio10->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        return $independiente;
    }


    /**
     * createByFormData function
     * @param array $data
     * @return Mercurio41
     */
    public function createByFormData($data)
    {
        $independiente = $this->create($data);
        $independiente->setEstado("T");
        $this->salvar($independiente, __LINE__);
        return $independiente;
    }

    /**
     * findById function
     * @param integer $id
     * @return Mercurio41
     */
    public function findById($id)
    {
        return $this->Mercurio41->findFirst("id='{$id}'");
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
            'campos_disponibles' => $this->Mercurio41->CamposDisponibles(),
            'estados_detalles' => $this->Mercurio10->getArrayEstados()
        );
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
        $mercurio41 = $this->Mercurio41->findFirst("id='{$id}'");
        $cm37 = $this->Mercurio37->count(
            "tipopc='{$this->tipopc}' AND " .
                "numero='{$id}' AND " .
                "coddoc IN(SELECT coddoc FROM mercurio14 WHERE tipopc='{$this->tipopc}' AND tipsoc='{$this->tipsoc}' AND obliga='S')"
        );

        $cm14 = $this->Mercurio14->count("*", "conditions: tipopc='{$this->tipopc}' and tipsoc='{$this->tipsoc}' and obliga='S'");
        if ($cm37 < $cm14) {
            throw new Exception("Adjunte los archivos obligatorios", 500);
        }

        $this->Mercurio41->updateAll("usuario='{$usuario}', estado='P'", "conditions: id='{$id}'");

        $ai = $this->Mercurio10->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;

        $entity = (object) $mercurio41->getArray();
        $entity->item = $ai;
        $solicitante = $this->Mercurio07->findFirst(" documento='{$mercurio41->getDocumento()}' and coddoc='{$mercurio41->getCoddoc()}' and tipo='{$mercurio41->getTipo()}'");
        $entity->repleg = $solicitante->getNombre();
        $entity->razsoc = $solicitante->getNombre();
        $entity->nit = $solicitante->getDocumento();
        $entity->email = $solicitante->getEmail();
        $senderValidationCaja->send($this->tipopc, $entity);
    }

    public function paramsApi()
    {
        $procesadorComando = Comman::Api();
        $procesadorComando->runCli(
            array(
                "servicio" => "ComfacaAfilia",
                "metodo" => "parametros_empresa"
            )
        );

        $paramsPensionado = new ParamsIndependiente();
        $paramsPensionado->setDatosCaptura($procesadorComando->toArray());

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
}
