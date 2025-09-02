<?php
namespace App\Models;

use App\Models\Adapter\DbBase;
use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercurio32 extends ModelBase
{


    protected $table = 'mercurio32';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'log',
        'cedtra',
        'cedcon',
        'tipdoc',
        'priape',
        'segape',
        'prinom',
        'segnom',
        'fecnac',
        'ciunac',
        'sexo',
        'estciv',
        'comper',
        'tiecon',
        'ciures',
        'codzon',
        'tipviv',
        'direccion',
        'barrio',
        'telefono',
        'celular',
        'email',
        'nivedu',
        'fecing',
        'codocu',
        'salario',
        'captra',
        'usuario',
        'estado',
        'codest',
        'motivo',
        'fecest',
        'tipo',
        'coddoc',
        'documento',
        'fecsol',
        'tipsal',
        'tippag',
        'numcue',
        'empresalab',
        'resguardo_id',
        'pub_indigena_id',
        'codban',
        'tipcue',
        'tipdis',
        'peretn',
        'zoneurbana',
    ];



    protected $id;
    protected $log;
    protected $cedtra;
    protected $cedcon;
    protected $tipdoc;
    protected $priape;
    protected $segape;
    protected $prinom;
    protected $segnom;
    protected $fecnac;
    protected $ciunac;
    protected $sexo;
    protected $estciv;
    protected $comper;
    protected $tiecon;
    protected $ciures;
    protected $codzon;
    protected $tipviv;
    protected $direccion;
    protected $barrio;
    protected $telefono;
    protected $celular;
    protected $email;
    protected $nivedu;
    protected $fecing;
    protected $codocu;
    protected $salario;
    protected $captra;
    protected $usuario;
    protected $estado;
    protected $codest;
    protected $motivo;
    protected $fecest;
    protected $tipo;
    protected $coddoc;
    protected $documento;
    protected $fecsol;
    protected $tipsal;
    protected $tippag;
    protected $numcue;
    protected $empresalab;
    protected $resguardo_id;
    protected $pub_indigena_id;
    protected $codban;
    protected $tipcue;
    protected $tipdis;
    protected $peretn;
    protected $zoneurbana;

    public function setZoneurbana($zoneurbana)
    {
        $this->zoneurbana = $zoneurbana;
    }

    public function getZoneurbana()
    {
        return $this->zoneurbana;
    }

    public function getPeretn()
    {
        return $this->peretn;
    }

    public function setPeretn($peretn)
    {
        $this->peretn = $peretn;
    }

    public function setTipdis($tipdis)
    {
        $this->tipdis = $tipdis;
    }

    public function getTipdis()
    {
        return $this->tipdis;
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

    public function getEmpresalab()
    {
        return $this->empresalab;
    }

    public function setEmpresalab($empresalab)
    {
        $this->empresalab = $empresalab;
    }

    public function setTippag($tippag)
    {
        $this->tippag = $tippag;
    }

    public function getTippag()
    {
        return $this->tippag;
    }

    public function getNumcue()
    {
        return $this->numcue;
    }

    public function setNumcue($numcue)
    {
        $this->numcue = $numcue;
    }

    /**
     * Metodo para establecer el valor del campo id
     * @param integer $id
     */
    public function setFecsol($fecsol)
    {
        $this->fecsol = $fecsol;
    }
    /**
     * Metodo para establecer el valor del campo id
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Metodo para establecer el valor del campo cedtra
     * @param string $cedtra
     */
    public function setCedtra($cedtra)
    {
        $this->cedtra = $cedtra;
    }

    /**
     * Metodo para establecer el valor del campo cedcon
     * @param string $cedcon
     */
    public function setCedcon($cedcon)
    {
        $this->cedcon = $cedcon;
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

    /**
     * Metodo para establecer el valor del campo fecnac
    
     */
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

    /**
     * Metodo para establecer el valor del campo estciv
     * @param string $estciv
     */
    public function setEstciv($estciv)
    {
        $this->estciv = $estciv;
    }

    /**
     * Metodo para establecer el valor del campo comper
     * @param string $comper
     */
    public function setComper($comper)
    {
        $this->comper = $comper;
    }
    public function setTiecon($tiecon)
    {
        $this->tiecon = $tiecon;
    }

    /**
     * Metodo para establecer el valor del campo ciures
     * @param string $ciures
     */
    public function setCiures($ciures)
    {
        $this->ciures = $ciures;
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
     * Metodo para establecer el valor del campo tipviv
     * @param string $tipviv
     */
    public function setTipviv($tipviv)
    {
        $this->tipviv = $tipviv;
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
     * Metodo para establecer el valor del campo email
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * Metodo para establecer el valor del campo fecing
     */
    public function setFecing($fecing)
    {
        $this->fecing = $fecing;
    }

    /**
     * Metodo para establecer el valor del campo codocu
     * @param string $codocu
     */
    public function setCodocu($codocu)
    {
        $this->codocu = $codocu;
    }

    /**
     * Metodo para establecer el valor del campo salario
     * @param integer $salario
     */
    public function setSalario($salario)
    {
        $this->salario = $salario;
    }

    public function setCaptra($captra)
    {
        $this->captra = $captra;
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
    public function getFecsol()
    {
        return $this->fecsol;
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
     * Devuelve el valor del campo cedtra
     * @return string
     */
    public function getCedtra()
    {
        return $this->cedtra;
    }

    /**
     * Devuelve el valor del campo cedcon
     * @return string
     */
    public function getCedcon()
    {
        return $this->cedcon;
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

    /**
     * Devuelve el valor del campo fecnac
     
     */
    public function getFecnac()
    {
        return Carbon::parse($this->fecnac);
    }

    public function getFecnacc()
    {
        return $this->fecnac;
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
     * Devuelve el valor del campo estciv
     * @return string
     */
    public function getEstciv()
    {
        return $this->estciv;
    }

    /**
     * Devuelve el valor del campo comper
     * @return string
     */
    public function getComper()
    {
        return $this->comper;
    }
    public function getTiecon()
    {
        return $this->tiecon;
    }

    /**
     * Devuelve el valor del campo ciures
     * @return string
     */
    public function getCiures()
    {
        return $this->ciures;
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
     * Devuelve el valor del campo tipviv
     * @return string
     */
    public function getTipviv()
    {
        return $this->tipviv;
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
     * Devuelve el valor del campo email
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * Devuelve el valor del campo fecing
     
     */
    public function getFecing()
    {
        return Carbon::parse($this->fecing);
    }

    /**
     * Devuelve el valor del campo codocu
     * @return string
     */
    public function getCodocu()
    {
        return $this->codocu;
    }

    /**
     * Devuelve el valor del campo salario
     * @return integer
     */
    public function getSalario()
    {
        return $this->salario;
    }

    public function getCaptra()
    {
        return $this->captra;
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

    public function CamposDisponibles()
    {
        $db = DbBase::rawConnect();
        $rqs = $db->fetchAll("SELECT * FROM mercurio12");
        $data = array();
        foreach ($rqs as $ai => $row) $data[$row['coddoc']] = $row['detalle'];
        return $data;
    }

    public function createAttributes($data)
    {
        parent::setCreateAttributes($this, $data);
    }
}
