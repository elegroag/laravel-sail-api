<?php
namespace App\Models;

use App\Models\Adapter\DbBase;
use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercurio34 extends ModelBase
{

    protected $table = 'mercurio34';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'log',
        'nit',
        'cedtra',
        'cedcon',
        'numdoc',
        'tipdoc',
        'priape',
        'segape',
        'prinom',
        'segnom',
        'fecnac',
        'ciunac',
        'sexo',
        'parent',
        'huerfano',
        'tiphij',
        'nivedu',
        'captra',
        'tipdis',
        'calendario',
        'usuario',
        'estado',
        'codest',
        'motivo',
        'fecest',
        'codben',
        'tipo',
        'coddoc',
        'documento',
        'cedacu',
        'fecsol',
        'resguardo_id',
        'pub_indigena_id',
        'peretn',
        'celular',
        'codban',
        'tipcue',
        'numcue',
        'tippag',
        'biocedu',
        'biotipdoc',
        'bioprinom',
        'biosegnom',
        'biopriape',
        'biosegape',
        'bioemail',
        'biophone',
        'biocodciu',
        'biodire',
        'biourbana',
        'biodesco',
    ];



    protected $id;
    protected $log;
    protected $nit;
    protected $cedtra;
    protected $cedcon;
    protected $numdoc;
    protected $tipdoc;
    protected $priape;
    protected $segape;
    protected $prinom;
    protected $segnom;
    protected $fecnac;
    protected $ciunac;
    protected $sexo;
    protected $parent;
    protected $huerfano;
    protected $tiphij;
    protected $nivedu;
    protected $captra;
    protected $tipdis;
    protected $calendario;
    protected $usuario;
    protected $estado;
    protected $codest;
    protected $motivo;
    protected $fecest;
    protected $codben;
    protected $tipo;
    protected $coddoc;
    protected $documento;
    protected $cedacu;
    protected $fecsol;
    protected $resguardo_id;
    protected $pub_indigena_id;
    protected $peretn;
    protected $celular;
    protected $codban;
    protected $tipcue;
    protected $numcue;
    protected $tippag;

    protected $biocedu;
    protected $biotipdoc;
    protected $bioprinom;
    protected $biosegnom;
    protected $biopriape;
    protected $biosegape;
    protected $bioemail;
    protected $biophone;
    protected $biocodciu;
    protected $biodire;
    protected $biourbana;
    protected $biodesco;

    public function setBiodesco($biodesco)
    {
        $this->biodesco = $biodesco;
    }

    public function getBiodesco()
    {
        return $this->biodesco;
    }

    public function setBiocedu($biocedu)
    {
        $this->biocedu = $biocedu;
    }

    public function getBiocedu()
    {
        return $this->biocedu;
    }

    public function setBiotipdoc($biotipdoc)
    {
        $this->biotipdoc = $biotipdoc;
    }

    public function getBiotipdoc()
    {
        return $this->biotipdoc;
    }

    public function setBioprinom($bioprinom)
    {
        $this->bioprinom = $bioprinom;
    }

    public function getBioprinom()
    {
        return $this->bioprinom;
    }

    public function setBiosegnom($biosegnom)
    {
        $this->biosegnom = $biosegnom;
    }

    public function getBiosegnom()
    {
        return $this->biosegnom;
    }

    public function setBiopriape($biopriape)
    {
        $this->biopriape = $biopriape;
    }

    public function getBiopriape()
    {
        return $this->biopriape;
    }

    public function setBiosegape($biosegape)
    {
        $this->biosegape = $biosegape;
    }

    public function getBiosegape()
    {
        return $this->biosegape;
    }

    public function setBioemail($bioemail)
    {
        $this->bioemail = $bioemail;
    }

    public function getBioemail()
    {
        return $this->bioemail;
    }

    public function setBiophone($biophone)
    {
        $this->biophone = $biophone;
    }

    public function getBiophone()
    {
        return $this->biophone;
    }

    public function setBiocodciu($biocodciu)
    {
        $this->biocodciu = $biocodciu;
    }

    public function getBiocodciu()
    {
        return $this->biocodciu;
    }

    public function setBiodire($biodire)
    {
        $this->biodire = $biodire;
    }

    public function getBiodire()
    {
        return $this->biodire;
    }

    public function setBiourbana($biourbana)
    {
        $this->biourbana = $biourbana;
    }

    public function getBiourbana()
    {
        return $this->biourbana;
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

    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    public function getCelular()
    {
        return $this->celular;
    }

    public function setPeretn($peretn)
    {
        $this->peretn = $peretn;
    }

    public function getPeretn()
    {
        return $this->peretn;
    }

    public function getResguardo_id()
    {
        return $this->resguardo_id;
    }

    public function setResguardo_id($id)
    {
        $this->resguardo_id = $id;
    }

    public function getPub_indigena_id()
    {
        return $this->pub_indigena_id;
    }

    public function setPub_indigena_id($id)
    {
        $this->pub_indigena_id = $id;
    }

    public function setFecsol($fecsol)
    {
        $this->fecsol = $fecsol;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setLog($log)
    {
        $this->log = $log;
    }

    public function setNit($nit)
    {
        $this->nit = $nit;
    }

    public function setCedtra($cedtra)
    {
        $this->cedtra = $cedtra;
    }

    public function setCedcon($cedcon)
    {
        $this->cedcon = $cedcon;
    }

    public function setNumdoc($numdoc)
    {
        $this->numdoc = $numdoc;
    }

    public function setTipdoc($tipdoc)
    {
        $this->tipdoc = $tipdoc;
    }

    public function setPriape($priape)
    {
        $this->priape = $priape;
    }

    public function setSegape($segape)
    {
        $this->segape = $segape;
    }

    public function setPrinom($prinom)
    {
        $this->prinom = $prinom;
    }

    public function setSegnom($segnom)
    {
        $this->segnom = $segnom;
    }

    public function setFecnac($fecnac)
    {
        $this->fecnac = $fecnac;
    }

    public function setCiunac($ciunac)
    {
        $this->ciunac = $ciunac;
    }

    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function setHuerfano($huerfano)
    {
        $this->huerfano = $huerfano;
    }

    public function setTiphij($tiphij)
    {
        $this->tiphij = $tiphij;
    }

    public function setNivedu($nivedu)
    {
        $this->nivedu = $nivedu;
    }

    public function setCaptra($captra)
    {
        $this->captra = $captra;
    }

    public function setTipdis($tipdis)
    {
        $this->tipdis = $tipdis;
    }

    public function setCalendario($calendario)
    {
        $this->calendario = $calendario;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setCodest($codest)
    {
        $this->codest = $codest;
    }

    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }

    public function setFecest($fecest)
    {
        $this->fecest = $fecest;
    }

    public function setCodben($codben)
    {
        $this->codben = $codben;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function setCedacu($cedacu)
    {
        $this->cedacu = $cedacu;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLog()
    {
        return $this->log;
    }

    public function getNit()
    {
        return $this->nit;
    }

    public function getCedtra()
    {
        return $this->cedtra;
    }

    public function getCedcon()
    {
        return $this->cedcon;
    }

    public function getNumdoc()
    {
        return $this->numdoc;
    }

    public function getTipdoc()
    {
        return $this->tipdoc;
    }

    public function getPriape()
    {
        return $this->priape;
    }

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

    /**
     * Devuelve el valor del campo fecnac
     
     */
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

    /**
     * Devuelve el valor del campo parent
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Devuelve el valor del campo huerfano
     * @return string
     */
    public function getHuerfano()
    {
        return $this->huerfano;
    }

    /**
     * Devuelve el valor del campo tiphij
     * @return string
     */
    public function getTiphij()
    {
        return $this->tiphij;
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
     * Devuelve el valor del campo calendario
     * @return string
     */
    public function getCalendario()
    {
        return $this->calendario;
    }

    /**
     * Devuelve el valor del campo usuario
     * @return integer
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Devuelve el valor del campo estado
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoDetalle()
    {
        $return = "";
        if ($this->estado == "T") $return = "TEMPORAL";
        if ($this->estado == "D") $return = "DEVUELTO";
        if ($this->estado == "A") $return = "APROBADO";
        if ($this->estado == "X") $return = "RECHAZADO";
        if ($this->estado == "P") $return = "PENDIENTE";
        return $return;
    }

    /**
     * Devuelve el valor del campo codest
     * @return string
     */
    public function getCodest()
    {
        return $this->codest;
    }

    /**
     * Devuelve el valor del campo motivo
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Devuelve el valor del campo fecest
     
     */
    public function getFecest()
    {
        return Carbon::parse($this->fecest);
    }

    /**
     * Devuelve el valor del campo codben
     * @return integer
     */
    public function getCodben()
    {
        return $this->codben;
    }

    /**
     * Devuelve el valor del campo tipo
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Devuelve el valor del campo coddoc
     * @return string
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    /**
     * Devuelve el valor del campo documento
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    public function getCedacu()
    {
        return $this->cedacu;
    }

    public function getFecsol()
    {
        return $this->fecsol;
    }

    public function getConvive()
    {
        return array(
            "1" => "Conyuge",
            "2" => "Trabajador",
            "3" => "No aplica",
            "4" => "Otras personas"
        );
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
}
