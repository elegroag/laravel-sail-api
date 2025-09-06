<?php

namespace App\Models;

use App\Models\Adapter\DbBase;
use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercurio31 extends ModelBase
{

    protected $table = 'mercurio31';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'log',
        'nit',
        'razsoc',
        'cedtra',
        'tipdoc',
        'priape',
        'segape',
        'prinom',
        'segnom',
        'fecnac',
        'ciunac',
        'sexo',
        'orisex',
        'estciv',
        'cabhog',
        'codciu',
        'codzon',
        'direccion',
        'barrio',
        'telefono',
        'celular',
        'fax',
        'email',
        'fecsol',
        'fecing',
        'salario',
        'tipsal',
        'captra',
        'tipdis',
        'nivedu',
        'rural',
        'horas',
        'tipcon',
        'trasin',
        'vivienda',
        'tipafi',
        'profesion',
        'cargo',
        'autoriza',
        'usuario',
        'estado',
        'codest',
        'motivo',
        'fecest',
        'tipo',
        'coddoc',
        'documento',
        'facvul',
        'peretn',
        'dirlab',
        'ciulab',
        'ruralt',
        'comision',
        'tipjor',
        'codsuc',
        'tippag',
        'numcue',
        'otra_empresa',
        'fecha_giro',
        'resguardo_id',
        'pub_indigena_id',
        'codban',
        'tipcue',
        'fecafi',
    ];


    public function setFecafi($fecafi)
    {
        $this->fecafi = $fecafi;
    }

    public function getFecafi()
    {
        return $this->fecafi;
    }

    public function setResguardo_id($resguardo_id)
    {
        $this->resguardo_id = $resguardo_id;
    }

    public function getResguardo_id()
    {
        return $this->resguardo_id;
    }

    public function setPub_indigena_id($pub_indigena_id)
    {
        $this->pub_indigena_id = $pub_indigena_id;
    }

    public function getPub_indigena_id()
    {
        return $this->pub_indigena_id;
    }

    public function setTipcue($tipcue)
    {
        $this->tipcue = $tipcue;
    }

    public function getTipcue()
    {
        return $this->tipcue;
    }

    public function setCodban($codban)
    {
        $this->codban = $codban;
    }

    public function getCodban()
    {
        return $this->codban;
    }

    public function getResguardoId()
    {
        return $this->resguardo_id;
    }

    public function setResguardoId($id)
    {
        $this->resguardo_id = $id;
    }

    public function getPubIndigenaId()
    {
        return $this->pub_indigena_id;
    }

    public function setPubIndigenaId($id)
    {
        $this->pub_indigena_id = $id;
    }

    public function getFechaGiro()
    {
        return $this->fecha_giro;
    }

    public function setFechaGiro($fecha)
    {
        $this->fecha_giro = $fecha;
    }

    public function getOtraEmpresa()
    {
        return $this->otra_empresa;
    }

    public function setOtraEmpresa($otra)
    {
        $this->otra_empresa = $otra;
    }

    public function getTippag()
    {
        return $this->tippag;
    }

    public function setTippag($tippag)
    {
        $this->tippag = $tippag;
    }

    public function getNumcue()
    {
        return $this->numcue;
    }

    public function setNumcue($numcue)
    {
        $this->numcue = $numcue;
    }

    public function getCodsuc()
    {
        return $this->codsuc;
    }

    public function setCodsuc($codsuc)
    {
        $this->codsuc = $codsuc;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setPeretn($peretn)
    {
        $this->peretn = $peretn;
    }

    public function setCiulab($ciulab)
    {
        $this->ciulab = $ciulab;
    }

    public function setTipjor($tipjor)
    {
        $this->tipjor = $tipjor;
    }
    public function setFacvul($facvul)
    {
        $this->facvul = $facvul;
    }

    public function setDirlab($dirlab)
    {
        $this->dirlab = $dirlab;
    }
    public function setRuralt($ruralt)
    {
        $this->ruralt = $ruralt;
    }
    public function setComision($comision)
    {
        $this->comision = $comision;
    }
    /**
     * Metodo para establecer el valor del campo log
     * @param integer $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * Metodo para establecer el valor del campo nit
     * @param string $nit
     */
    public function setNit($nit)
    {
        $this->nit = $nit;
    }

    /**
     * Metodo para establecer el valor del campo razsoc
     * @param string $razsoc
     */
    public function setRazsoc($razsoc)
    {
        $this->razsoc = $razsoc;
    }

    /**
     * Metodo para establecer el valor del campo cedtra
     * @param string $cedtra
     */
    public function setCedtra($cedtra)
    {
        $this->cedtra = $cedtra;
    }

    /**
     * Metodo para establecer el valor del campo tipdoc
     * @param string $tipdoc
     */
    public function setTipdoc($tipdoc)
    {
        $this->tipdoc = $tipdoc;
    }

    /**
     * Metodo para establecer el valor del campo priape
     * @param string $priape
     */
    public function setPriape($priape)
    {
        $this->priape = $priape;
    }

    /**
     * Metodo para establecer el valor del campo segape
     * @param string $segape
     */
    public function setSegape($segape)
    {
        $this->segape = $segape;
    }

    /**
     * Metodo para establecer el valor del campo prinom
     * @param string $prinom
     */
    public function setPrinom($prinom)
    {
        $this->prinom = $prinom;
    }

    /**
     * Metodo para establecer el valor del campo segnom
     * @param string $segnom
     */
    public function setSegnom($segnom)
    {
        $this->segnom = $segnom;
    }

    public function setFecnac($fecnac)
    {
        $this->fecnac = $fecnac;
    }

    /**
     * Metodo para establecer el valor del campo ciunac
     * @param string $ciunac
     */
    public function setCiunac($ciunac)
    {
        $this->ciunac = $ciunac;
    }

    /**
     * Metodo para establecer el valor del campo sexo
     * @param string $sexo
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }
    public function setOrisex($orisex)
    {
        $this->orisex = $orisex;
    }

    /**
     * Metodo para establecer el valor del campo estciv
     * @param string $estciv
     */
    public function setEstciv($estciv)
    {
        $this->estciv = $estciv;
    }

    /**
     * Metodo para establecer el valor del campo cabhog
     * @param string $cabhog
     */
    public function setCabhog($cabhog)
    {
        $this->cabhog = $cabhog;
    }

    /**
     * Metodo para establecer el valor del campo codciu
     * @param string $codciu
     */
    public function setCodciu($codciu)
    {
        $this->codciu = $codciu;
    }

    /**
     * Metodo para establecer el valor del campo codzon
     * @param string $codzon
     */
    public function setCodzon($codzon)
    {
        $this->codzon = $codzon;
    }

    /**
     * Metodo para establecer el valor del campo direccion
     * @param string $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    /**
     * Metodo para establecer el valor del campo barrio
     * @param string $barrio
     */
    public function setBarrio($barrio)
    {
        $this->barrio = $barrio;
    }

    /**
     * Metodo para establecer el valor del campo telefono
     * @param string $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    /**
     * Metodo para establecer el valor del campo celular
     * @param string $celular
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    /**
     * Metodo para establecer el valor del campo fax
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * Metodo para establecer el valor del campo email
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setFecsol($fecsol)
    {
        $this->fecsol = $fecsol;
    }
    /**
     * Metodo para establecer el valor del campo fecing

     */
    public function setFecing($fecing)
    {
        $this->fecing = $fecing;
    }

    /**
     * Metodo para establecer el valor del campo salario
     * @param integer $salario
     */
    public function setSalario($salario)
    {
        $this->salario = $salario;
    }

    /**
     * Metodo para establecer el valor del campo captra
     * @param string $captra
     */
    public function setCaptra($captra)
    {
        $this->captra = $captra;
    }

    /**
     * Metodo para establecer el valor del campo tipdis
     * @param string $tipdis
     */
    public function setTipdis($tipdis)
    {
        $this->tipdis = $tipdis;
    }

    /**
     * Metodo para establecer el valor del campo nivedu
     * @param string $nivedu
     */
    public function setNivedu($nivedu)
    {
        $this->nivedu = $nivedu;
    }

    /**
     * Metodo para establecer el valor del campo rural
     * @param string $rural
     */
    public function setRural($rural)
    {
        $this->rural = $rural;
    }

    /**
     * Metodo para establecer el valor del campo horas
     * @param integer $horas
     */
    public function setHoras($horas)
    {
        $this->horas = $horas;
    }

    /**
     * Metodo para establecer el valor del campo tipcon
     * @param string $tipcon
     */
    public function setTipcon($tipcon)
    {
        $this->tipcon = $tipcon;
    }
    public function setTrasin($trasin)
    {
        $this->trasin = $trasin;
    }

    /**
     * Metodo para establecer el valor del campo vivienda
     * @param string $vivienda
     */
    public function setVivienda($vivienda)
    {
        $this->vivienda = $vivienda;
    }

    /**
     * Metodo para establecer el valor del campo tipafi
     * @param string $tipafi
     */
    public function setTipafi($tipafi)
    {
        $this->tipafi = $tipafi;
    }

    /**
     * Metodo para establecer el valor del campo profesion
     * @param string $profesion
     */
    public function setProfesion($profesion)
    {
        $this->profesion = $profesion;
    }

    /**
     * Metodo para establecer el valor del campo cargo
     * @param string $cargo
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
    }

    /**
     * Metodo para establecer el valor del campo autoriza
     * @param string $autoriza
     */
    public function setAutoriza($autoriza)
    {
        $this->autoriza = $autoriza;
    }

    /**
     * Metodo para establecer el valor del campo usuario
     * @param integer $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Metodo para establecer el valor del campo estado
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Metodo para establecer el valor del campo codest
     * @param string $codest
     */
    public function setCodest($codest)
    {
        $this->codest = $codest;
    }

    /**
     * Metodo para establecer el valor del campo motivo
     * @param string $motivo
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }

    /**
     * Metodo para establecer el valor del campo fecest

     */
    public function setFecest($fecest)
    {
        $this->fecest = $fecest;
    }

    /**
     * Metodo para establecer el valor del campo tipo
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Metodo para establecer el valor del campo coddoc
     * @param string $coddoc
     */
    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    /**
     * Metodo para establecer el valor del campo documento
     * @param string $documento
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }


    /**
     * Devuelve el valor del campo id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Devuelve el valor del campo log
     * @return integer
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Devuelve el valor del campo nit
     * @return string
     */
    public function getNit()
    {
        return $this->nit;
    }

    /**
     * Devuelve el valor del campo razsoc
     * @return string
     */
    public function getRazsoc()
    {
        return $this->razsoc;
    }

    /**
     * Devuelve el valor del campo cedtra
     * @return string
     */
    public function getCedtra()
    {
        return $this->cedtra;
    }

    /**
     * Devuelve el valor del campo tipdoc
     * @return string
     */
    public function getTipdoc()
    {
        return $this->tipdoc;
    }

    /**
     * Devuelve el valor del campo priape
     * @return string
     */
    public function getPriape()
    {
        return $this->priape;
    }

    /**
     * Devuelve el valor del campo segape
     * @return string
     */
    public function getSegape()
    {
        return $this->segape;
    }

    /**
     * Devuelve el valor del campo prinom
     * @return string
     */
    public function getPrinom()
    {
        return $this->prinom;
    }

    /**
     * Devuelve el valor del campo segnom
     * @return string
     */
    public function getSegnom()
    {
        return $this->segnom;
    }


    public function getFecnac()
    {
        return Carbon::parse($this->fecnac);
    }

    /**
     * Devuelve el valor del campo ciunac
     * @return string
     */
    public function getCiunac()
    {
        return $this->ciunac;
    }

    /**
     * Devuelve el valor del campo sexo
     * @return string
     */
    public function getSexo()
    {
        return $this->sexo;
    }


    public function getOrisex()
    {
        return $this->orisex;
    }

    /**
     * Devuelve el valor del campo estciv
     * @return string
     */
    public function getEstciv()
    {
        return $this->estciv;
    }

    /**
     * Devuelve el valor del campo cabhog
     * @return string
     */
    public function getCabhog()
    {
        return $this->cabhog;
    }

    /**
     * Devuelve el valor del campo codciu
     * @return string
     */
    public function getCodciu()
    {
        return $this->codciu;
    }

    /**
     * Devuelve el valor del campo codzon
     * @return string
     */
    public function getCodzon()
    {
        return $this->codzon;
    }

    /**
     * Devuelve el valor del campo direccion
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Devuelve el valor del campo barrio
     * @return string
     */
    public function getBarrio()
    {
        return $this->barrio;
    }

    /**
     * Devuelve el valor del campo telefono
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Devuelve el valor del campo celular
     * @return string
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * Devuelve el valor del campo fax
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Devuelve el valor del campo email
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Devuelve el valor del campo fecing

     */
    public function getFecsol()
    {
        return Carbon::parse($this->fecsol);
    }
    public function getFecing()
    {
        return Carbon::parse($this->fecing);
    }

    /**
     * Devuelve el valor del campo salario
     * @return integer
     */
    public function getSalario()
    {
        return $this->salario;
    }

    /**
     * Devuelve el valor del campo captra
     * @return string
     */
    public function getCaptra()
    {
        return $this->captra;
    }

    /**
     * Devuelve el valor del campo tipdis
     * @return string
     */
    public function getTipdis()
    {
        return $this->tipdis;
    }

    /**
     * Devuelve el valor del campo nivedu
     * @return string
     */
    public function getNivedu()
    {
        return $this->nivedu;
    }

    /**
     * Devuelve el valor del campo rural
     * @return string
     */
    public function getRural()
    {
        return $this->rural;
    }

    /**
     * Devuelve el valor del campo horas
     * @return integer
     */
    public function getHoras()
    {
        return $this->horas;
    }

    /**
     * Devuelve el valor del campo tipcon
     * @return string
     */
    public function getTipcon()
    {
        return $this->tipcon;
    }

    public function getTrasin()
    {
        return $this->trasin;
    }

    /**
     * Devuelve el valor del campo vivienda
     * @return string
     */
    public function getVivienda()
    {
        return $this->vivienda;
    }

    /**
     * Devuelve el valor del campo tipafi
     * @return string
     */
    public function getTipafi()
    {
        return $this->tipafi;
    }

    /**
     * Devuelve el valor del campo profesion
     * @return string
     */
    public function getProfesion()
    {
        return $this->profesion;
    }

    /**
     * Devuelve el valor del campo cargo
     * @return string
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Devuelve el valor del campo autoriza
     * @return string
     */
    public function getAutoriza()
    {
        return $this->autoriza;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoArray()
    {
        return array(
            "A" => "APROBO",
            "X" => "RECHAZO",
            "P" => "PENDIENTE",
            "D" => "DEVOLVIO",
        );
    }

    public function getEstadoDetalle()
    {
        $return = "";
        if ($this->estado == "T") $return = "TEMPORAL";
        if ($this->estado == "D") $return = "DEVUELTO";
        if ($this->estado == "A") $return = "APROBADO";
        if ($this->estado == "X") $return = "RECHAZADO";
        if ($this->estado == "P") $return = "PENDIENTE";
        if ($this->estado == "C") $return = "CANCELAR";
        return $return;
    }

    public function getCodest()
    {
        return $this->codest;
    }

    public function getMotivo()
    {
        return $this->motivo;
    }

    public function getFecest()
    {
        if ($this->fecest == '') {
            return '';
        } else {
            return Carbon::parse($this->fecest);
        }
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getCoddoc()
    {
        return $this->coddoc;
    }
    public function getFacvul()
    {
        return $this->facvul;
    }

    public function getPeretn()
    {
        return $this->peretn;
    }
    public function getRuralt()
    {
        return $this->ruralt;
    }
    public function getDirlab()
    {
        return $this->dirlab;
    }
    public function getComision()
    {
        return $this->comision;
    }
    public function getTipjor()
    {
        return $this->tipjor;
    }

    public function getCiulab()
    {
        return $this->ciulab;
    }

    //SAT
    public function getTipsal()
    {
        return trim($this->tipsal);
    }

    public function setTipsal($tipsal)
    {
        $this->tipsal = $tipsal;
    }

    public function getTipsalArray()
    {
        return array(
            'F' => 'FIJO',
            'V' => 'VARIABLE',
            'I' => 'INTEGRAL'
        );
    }

    public function getCoddocArray()
    {
        return array(
            1 => 'CC',
            10 => 'TMF',
            11 => 'CD',
            12 => 'ISE',
            13 => 'V',
            14 => 'PT',
            2 => 'TI',
            3 => 'NI',
            4 => 'CE',
            5 => 'NU',
            6 => 'PA',
            7 => 'RC',
            8 => 'PEP',
            9 => 'CB'
        );
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function getNombre()
    {
        return $this->priape . " " . $this->prinom;
    }

    public function getNombreCompleto()
    {
        return $this->priape . " " . $this->segape . " " . $this->prinom . " " . $this->segnom;
    }

    public function get_all()
    {
        return $this;
    }

    public function CamposDisponibles()
    {
        $db = DbBase::rawConnect();
        $rqs = $db->fetchAll("SELECT * FROM mercurio12");
        $data = array();
        foreach ($rqs as $ai => $row) $data[$row['coddoc']] = $row['detalle'];
        return $data;
    }

    public function CamposDisponibleDetalle($campo)
    {
        $data = $this->CamposDisponibles();
        return $data["{$campo}"];
    }

    public function createAttributes($data)
    {
        parent::setCreateAttributes($this, $data);
    }

    public function solicitante()
    {
        return $this->belongsTo(Mercurio07::class, 'documento', 'documento');
    }
}
