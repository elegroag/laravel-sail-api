<?php
class EmpresaService 
{

    private $tipopc = "2";

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
            $conditions = "and solis.estado NOT IN('I') ";
        } else {
            $conditions = "and solis.estado='{$estado}' ";
        }
        $sql = "SELECT solis.*, 
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
            IF(tipper = 'N', 'NATURAL', 'JURIDICA') as tipo_persona,
            solis.coddoc as tipo_documento,
            gener09.detzon as detalle_zona 
            FROM mercurio30 as solis 
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

        $mercurio14 = $this->Mercurio14->find("tipopc='{$this->tipopc}' AND tipsoc='{$solicitud->getTipsoc()}'");
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
        $html = View::render("empresa/tmp/archivos_requeridos", array(
            "load_archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        ));
        return $html;
    }

    /**
     * dataArchivosRequeridos function
     * @param Mercurio30 $solicitud
     * @return array
     */
    public function dataArchivosRequeridos($solicitud)
    {
        Core::importHelper('files');

        $this->db->setFetchMode(DbBase::DB_ASSOC);

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
        LIMIT 1");

        $corregir = false;
        if ($mercurio10) {
            if ($mercurio10['estado'] == 'D') {
                $campos = $mercurio10['campos_corregir'];
                $corregir = explode(";", $campos);
            }
        }

        $mercurio14 = $this->Mercurio14->find("tipopc='{$this->tipopc}' AND tipsoc='{$solicitud->getTipsoc()}'", "order: auto_generado DESC");
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
        $archivos_descargar = oficios_requeridos('E');
        return array(
            "disponibles" => $archivos_descargar,
            "archivos" => $archivos,
            "path" => $mercurio01->getPath(),
            "puede_borrar" => ($solicitud->getEstado() == 'P' || $solicitud->getEstado() == 'A') ? false : true
        );
    }

    /**
     * loadDisplay function
     * @param Mercurio30 $solicitud
     * @return void
     */
    public function loadDisplay($solicitud)
    {
        Tag::displayTo("nit", $solicitud->getNit());
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
        Tag::displayTo("coddocrepleg", $solicitud->getCoddocrepleg());
    }

    function loadDisplaySubsidio($empresa)
    {
        Tag::displayTo("tipdoc", $empresa['coddoc']);
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
        Tag::displayTo("emailpri", $empresa['mailr']);
    }

    /**
     * update function
     * @param integer $id
     * @param array $data
     * @return Mercurio30
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
        $empresa = $this->findById($id);
        if ($empresa != false) {
            $empresa->setTransaction(self::$transaction);
            $empresa->createAttributes($data);
            $empresa->setRepleg($data['priape'] . ' ' . $data['segape'] . ' ' . $data['prinom'] . ' ' . $data['segnom']);

            if ($data['direccion'] == $data['dirpri']) {
                $empresa->setDirpri($data['direccion']);
            } else {
                $empresa->setDirpri($data['dirpri']);
            }
            $empresa->setCiupri($data['ciupri']);
            if ($data['telefono'] == $data['telpri']) {
                $empresa->setTelpri($data['telefono']);
            } else {
                $empresa->setTelpri($data['telpri']);
            }
            if ($data['celular'] == $data['celpri']) {
                $empresa->setCelpri($data['celular']);
            } else {
                $empresa->setCelpri($data['celpri']);
            }
            if ($data['email'] == $data['emailpri']) {
                $empresa->setEmailpri(null);
            } else {
                $empresa->setEmailpri($data['emailpri']);
            }
            if ($data['codciu'] == $data['ciupri']) {
                $empresa->setCiupri(null);
            } else {
                $empresa->setCiupri($data['ciupri']);
            }
            $empresa->setEstado("T");
            $empresa->setFecsol(date('Y-m-d'));
            $this->salvar($empresa, __LINE__);
            return true;
        } else {
            return false;
        }
    }

    /**
     * create function
     * @param array $data
     * @return Mercurio30
     */
    public function create($data)
    {
        $id = $this->Mercurio30->maximum('id') + 1;
        $empresa = new Mercurio30();
        $empresa->setTransaction(self::$transaction);
        $empresa->createAttributes($data);
        $empresa->setId($id);
        $empresa->setRepleg($data['priape'] . ' ' . $data['segape'] . ' ' . $data['prinom'] . ' ' . $data['segnom']);

        if ($data['direccion'] == $data['dirpri']) {
            $empresa->setDirpri(null);
        } else {
            $empresa->setDirpri($data['dirpri']);
        }
        $empresa->setCiupri($data['ciupri']);
        if ($data['telefono'] == $data['telpri']) {
            $empresa->setTelpri(null);
        } else {
            $empresa->setTelpri($data['telpri']);
        }
        if ($data['celular'] == $data['celpri']) {
            $empresa->setCelpri(null);
        } else {
            $empresa->setCelpri($data['celpri']);
        }
        if ($data['email'] == $data['emailpri']) {
            $empresa->setEmailpri(null);
        } else {
            $empresa->setEmailpri($data['emailpri']);
        }
        if ($data['codciu'] == $data['ciupri']) {
            $empresa->setCiupri(null);
        } else {
            $empresa->setCiupri($data['ciupri']);
        }

        $this->Mercurio37->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        $this->Mercurio10->deleteAll(" tipopc='{$this->tipopc}' and numero='{$id}'");
        $this->Tranoms->deleteAll(" request='{$id}'");
        return $empresa;
    }

    /**
     * create function
     * @param array $data
     * @return Mercurio30
     */
    public function createByFormData($data)
    {
        $empresa = $this->create($data);
        $empresa->setEstado("T");
        $this->salvar($empresa, __LINE__);
        return $empresa;
    }

    /**
     * findById function
     * @param integer $id
     * @return Mercurio30
     */
    public function findById($id)
    {
        return $this->Mercurio30->findFirst("id='{$id}'");
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
        $solicitud = $this->Mercurio30->findFirst("id='{$id}'");

        $cm37 = $this->Mercurio37->count(
            "tipopc='{$this->tipopc}' AND " .
                "numero='{$id}' AND " .
                "coddoc IN(SELECT coddoc FROM mercurio14 WHERE tipopc='{$this->tipopc}' AND tipsoc='{$solicitud->getTipsoc()}' AND obliga='S')"
        );

        $cm14 = $this->Mercurio14->count("*", "conditions: tipopc='{$this->tipopc}' and tipsoc='{$solicitud->getTipsoc()}' and obliga='S'");
        if ($cm37 < $cm14) {
            throw new Exception("Adjunte los archivos obligatorios", 500);
        }

        $this->Mercurio30->updateAll("usuario='{$usuario}', estado='P'", "conditions: id='{$id}'");

        $ai = $this->Mercurio10->maximum("item", "conditions: tipopc='{$this->tipopc}' and numero='{$id}'") + 1;

        $entity = (object) $solicitud->getArray();
        $entity->item = $ai;
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
            'campos_disponibles' => $this->Mercurio30->CamposDisponibles(),
            'estados_detalles' => $this->Mercurio10->getArrayEstados()
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


    public function addTrabajadoresNomina($tranoms, $id)
    {
        if (!$tranoms) {
            throw new Exception("Error no hay trabajadores en nomina", 301);
        }
        $cedtras = array();
        foreach ($tranoms as $row) {
            $cedtras[] = $row['cedtra'];

            $tranom = $this->Tranoms->findFirst(" request='{$id}' AND cedtra='{$row['cedtra']}'");
            if (!$tranom) {
                $trabajadorNomina =  new Tranoms();
                $trabajadorNomina->createAttributes(
                    array(
                        'cedtra' => sanetizar($row['cedtra']),
                        'nomtra' => sanetizar_input($row['nomtra']),
                        'apetra' => sanetizar_input($row['apetra']),
                        'saltra' => sanetizar($row['saltra']),
                        'fectra' => sanetizar_date($row['fectra']),
                        'cartra' => sanetizar_input($row['cartra']),
                        'request' => $id
                    )
                );
                $trabajadorNomina->save();
            } else {
                $tranom->createAttributes(
                    array(
                        'cedtra' => sanetizar($row['cedtra']),
                        'nomtra' => sanetizar_input($row['nomtra']),
                        'apetra' => sanetizar_input($row['apetra']),
                        'saltra' => sanetizar($row['saltra']),
                        'fectra' => sanetizar_date($row['fectra']),
                        'cartra' => sanetizar_input($row['cartra'])
                    )
                );
                $tranom->save();
            }
        }

        $tranoms = $this->db->inQueryAssoc("SELECT cedtra FROM tranoms WHERE request='{$id}'");
        if ($tranoms && count($cedtras) > 0) {
            foreach ($tranoms as $row) {
                if (in_array($row['cedtra'], $cedtras) === false) {
                    $this->Tranoms->deleteAll(" request='{$id}' AND cedtra='{$row['cedtra']}'");
                }
            }
        }
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

        $paramsEmpresa = new ParamsEmpresa();
        $paramsEmpresa->setDatosCaptura($procesadorComando->toArray());
    }
}
