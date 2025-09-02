<?php
namespace App\Models;

use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercurio35 extends ModelBase {

    protected $table = 'mercurio35';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'log',
        'nit',
        'tipdoc',
        'cedtra',
        'nomtra',
        'codest',
        'fecret',
        'nota',
        'usuario',
        'archivo',
        'estado',
        'fecest',
        'motivo',
        'motrec',
        'tipo',
        'coddoc',
        'documento',
    ];



	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $log;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $tipdoc;

	/**
	 * @var string
	 */
	protected $cedtra;

	/**
	 * @var string
	 */
	protected $nomtra;

	/**
	 * @var string
	 */
	protected $codest;


	protected $fecret;

	/**
	 * @var string
	 */
	protected $nota;

	/**
	 * @var integer
	 */
	protected $usuario;

	/**
	 * @var string
	 */
	protected $archivo;

	/**
	 * @var string
	 */
	protected $estado;


	protected $fecest;

	/**
	 * @var string
	 */
	protected $motivo;

	/**
	 * @var string
	 */
	protected $motrec;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var string
	 */
	protected $coddoc;

	/**
	 * @var string
	 */
	protected $documento;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo log
	 * @param integer $log
	 */
	public function setLog($log){
		$this->log = $log;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo tipdoc
	 * @param string $tipdoc
	 */
	public function setTipdoc($tipdoc){
		$this->tipdoc = $tipdoc;
	}

	/**
	 * Metodo para establecer el valor del campo cedtra
	 * @param string $cedtra
	 */
	public function setCedtra($cedtra){
		$this->cedtra = $cedtra;
	}

	/**
	 * Metodo para establecer el valor del campo nomtra
	 * @param string $nomtra
	 */
	public function setNomtra($nomtra){
		$this->nomtra = $nomtra;
	}

	/**
	 * Metodo para establecer el valor del campo codest
	 * @param string $codest
	 */
	public function setCodest($codest){
		$this->codest = $codest;
	}

	/**
	 * Metodo para establecer el valor del campo fecret
	 
	 */
	public function setFecret($fecret){
		$this->fecret = $fecret;
	}

	/**
	 * Metodo para establecer el valor del campo nota
	 * @param string $nota
	 */
	public function setNota($nota){
		$this->nota = $nota;
	}

	/**
	 * Metodo para establecer el valor del campo usuario
	 * @param integer $usuario
	 */
	public function setUsuario($usuario){
		$this->usuario = $usuario;
	}

	/**
	 * Metodo para establecer el valor del campo archivo
	 * @param string $archivo
	 */
	public function setArchivo($archivo){
		$this->archivo = $archivo;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Metodo para establecer el valor del campo fecest
	
	 */
	public function setFecest($fecest){
		$this->fecest = $fecest;
	}

	/**
	 * Metodo para establecer el valor del campo motivo
	 * @param string $motivo
	 */
	public function setMotivo($motivo){
		$this->motivo = $motivo;
	}

	/**
	 * Metodo para establecer el valor del campo motrec
	 * @param string $motrec
	 */
	public function setMotrec($motrec){
		$this->motrec = $motrec;
	}

	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo coddoc
	 * @param string $coddoc
	 */
	public function setCoddoc($coddoc){
		$this->coddoc = $coddoc;
	}

	/**
	 * Metodo para establecer el valor del campo documento
	 * @param string $documento
	 */
	public function setDocumento($documento){
		$this->documento = $documento;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo log
	 * @return integer
	 */
	public function getLog(){
		return $this->log;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo tipdoc
	 * @return string
	 */
	public function getTipdoc(){
		return $this->tipdoc;
	}

	/**
	 * Devuelve el valor del campo cedtra
	 * @return string
	 */
	public function getCedtra(){
		return $this->cedtra;
	}

	/**
	 * Devuelve el valor del campo nomtra
	 * @return string
	 */
	public function getNomtra(){
		return $this->nomtra;
	}

	/**
	 * Devuelve el valor del campo codest
	 * @return string
	 */
	public function getCodest(){
		return $this->codest;
	}

	/**
	 * Devuelve el valor del campo fecret
	 
	 */
	public function getFecret(){
		return Carbon::parse($this->fecret);
	}

	/**
	 * Devuelve el valor del campo nota
	 * @return string
	 */
	public function getNota(){
		return $this->nota;
	}

	/**
	 * Devuelve el valor del campo usuario
	 * @return integer
	 */
	public function getUsuario(){
		return $this->usuario;
	}

	/**
	 * Devuelve el valor del campo archivo
	 * @return string
	 */
	public function getArchivo(){
		return $this->archivo;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

    public function getEstadoDetalle(){
        $return = "";
        if($this->estado=="T")$return="TEMPORAL";
        if($this->estado=="D")$return="DEVUELTO";
        if($this->estado=="A")$return="APROBADO";
        if($this->estado=="X")$return="RECHAZADO";
        if($this->estado=="P")$return="PENDIENTE";
        return $return;
    }

	/**
	 * Devuelve el valor del campo fecest
	 
	 */
	public function getFecest(){
		return Carbon::parse($this->fecest);
	}

	/**
	 * Devuelve el valor del campo motivo
	 * @return string
	 */
	public function getMotivo(){
		return $this->motivo;
	}

	/**
	 * Devuelve el valor del campo motrec
	 * @return string
	 */
	public function getMotrec(){
		return $this->motrec;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo coddoc
	 * @return string
	 */
	public function getCoddoc(){
		return $this->coddoc;
	}

	/**
	 * Devuelve el valor del campo documento
	 * @return string
	 */
	public function getDocumento(){
		return $this->documento;
	}

}

