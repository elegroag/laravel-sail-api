<?php
class ConyugeService 
{

    private $tipopc = '3';
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

        if ($this->Mercurio32->count(
            "*",
            "conditions: documento='{$documento}' and coddoc='{$coddoc}'"
        ) > 0) {
            $conditions = (empty($estado)) ? " AND m32.estado NOT IN('I') " : " AND m32.estado='{$estado}' ";

            $this->db->setFetchMode(DbBase::DB_ASSOC);
            return $this->db->inQueryAssoc(
                "SELECT m32.*, 
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
                ORDER BY m32.fecsol ASC;"
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
        $archivos = array();
        $mercurio13 = $this->Mercurio13->find("tipopc = '{$this->tipopc}'");

        $db = (object) DbBase::rawConnect();
        $db->setFetchMode(DbBase::DB_ASSOC);

        $mercurio10 = $db->fetchOne("SELECT item, estado, campos_corregir 
        FROM mercurio10 
        WHERE numero='{$solicitud->getId()}' AND tipopc='{$this->tipopc}' ORDER BY item DESC LIMIT 1");

        $corregir = false;
        if ($mercurio10) {
            if ($mercurio10['estado'] == 'D') {
                $campos = $mercurio10['campos_corregir'];
                $corregir = explode(";", $campos);
            }
        }
        foreach ($mercurio13 as $m13) {
            $m12 = $this->Mercurio12->findFirst("coddoc='{$m13->getCoddoc()}'");
            $mercurio37 = $this->Mercurio37->findFirst("tipopc='{$this->tipopc}' and numero='{$solicitud->getId()}' and coddoc='{$m13->getCoddoc()}'");
            if ($corregir) {
                if (in_array($m12->getCoddoc(), $corregir)) {
                    $this->Mercurio37->deleteAll("tipopc='{$this->tipopc}' AND numero='{$solicitud->getId()}' AND coddoc='{$m13->getCoddoc()}'");
                    $mercurio37 = false;
                }
            }
            $obliga = ($m13->getObliga() == "S") ? "<br><small class='text-danger'>Obligatorio</small>" : "";
            $archivo = new stdClass;
            $archivo->obliga = $obliga;
            $archivo->id = $solicitud->getId();
            $archivo->coddoc = $m13->getCoddoc();
            $archivo->detalle = $m12->getDetalle();
            $archivo->diponible = ($mercurio37) ? $mercurio37->getArchivo() : false;
            $archivos[] = $archivo;
        }
        $mercurio01 = $this->Mercurio01->findFirst();
        $html = View::render("conyuge/tmp/archivos_requeridos", array(
            "load_archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        ));
        return $html;
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

        $mercurio13 = $this->Mercurio13->find("tipopc='{$this->tipopc}'", "order: auto_generado DESC");
        foreach ($mercurio13 as $m13) {
            $m12 = $this->Mercurio12->findFirst("coddoc='{$m13->getCoddoc()}'");
            $mercurio37 = $this->Mercurio37->findFirst("tipopc='{$this->tipopc}' and numero='{$solicitud->getId()}' and coddoc='{$m13->getCoddoc()}'");
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

        $mercurio01 = $this->Mercurio01->findFirst();
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
        Tag::displayTo("cedtra", $solicitud->getCedtra());
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
        Tag::displayTo("resguardo_id",  $solicitud->getResguardoId());
    }


    /**
     * loadDisplaySubsidio function
     * @param array $trabajador
     * @return void
     */
    public function loadDisplaySubsidio($trabajador)
    {
        Tag::displayTo("nit", $trabajador['nit']);
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
     * @return Mercurio32
     */
    public function create($data)
    {
        $id = $this->Mercurio32->maximum('id') + 1;
        $conyuge = new Mercurio32();
        $conyuge->setTransaction(self::$transaction);
        $conyuge->createAttributes($data);
        $conyuge->setId($id);

        $this->Mercurio37->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        $this->Mercurio10->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        return $conyuge;
    }

    /**
     * createByFormData function
     * @param array $data
     * @return Mercurio32
     */
    public function createByFormData($data)
    {
        $conyuge = $this->create($data);
        $conyuge->setEstado("T");
        $this->salvar($conyuge, __LINE__);
        return $conyuge;
    }

    /**
     * findById function
     * @param integer $id
     * @return Mercurio32
     */
    public function findById($id)
    {
        return $this->Mercurio32->findFirst("id='{$id}'");
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
        $solicitud = $this->Mercurio32->findFirst("id='{$id}'");

        $cm37 = $this->Mercurio37->count(
            "tipopc='{$this->tipopc}' AND " .
                "numero='{$id}' AND " .
                "coddoc IN(SELECT coddoc FROM mercurio13 WHERE tipopc='{$this->tipopc}' and obliga='S')"
        );

        $cm13 = $this->Mercurio13->count("*", "conditions: tipopc='{$this->tipopc}' and obliga='S'");
        if ($cm37 < $cm13) {
            throw new Exception("Adjunte los archivos obligatorios", 500);
        }

        $this->Mercurio32->updateAll("usuario='{$usuario}', estado='P'", "conditions: id='{$id}'");

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
        $seguimientos = $this->db->inQueryAssoc("SELECT * FROM mercurio10 WHERE numero='{$id}' AND tipopc='{$this->tipopc}' ORDER BY item DESC");
        foreach ($seguimientos as $ai => $row) {
            $campos = explode(';', $row['campos_corregir']);
            $seguimientos[$ai]['corregir'] = $campos;
        }
        return array(
            'seguimientos' => $seguimientos,
            'campos_disponibles' => $this->Mercurio32->CamposDisponibles(),
            'estados_detalles' => $this->Mercurio10->getArrayEstados()
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
        $datos = $this->db->inQueryAssoc("SELECT cedcon as 'cedula', CONCAT_WS('', prinom, segnom, priape, segape) as 'nombre_completo' 
        FROM mercurio32  
        WHERE 
        documento='{$documento}' and 
        coddoc='{$coddoc}' and 
        estado NOT IN('X','I')");

        if (is_array($datos) === True) {
            return $datos;
        } else {
            return false;
        }
    }

    public function findRequestByCedtra($cedtra)
    {
        $this->db->setFetchMode(DbBase::DB_ASSOC);
        $datos = $this->db->inQueryAssoc("SELECT cedcon as 'cedula', CONCAT_WS('', prinom, segnom, priape, segape) as 'nombre_completo' 
        FROM mercurio32  
        WHERE 
        cedtra='{$cedtra}' AND estado NOT IN('X','I')");
        return (is_array($datos) === True) ? $datos :  false;
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
