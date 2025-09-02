<?php
namespace App\Models;

use App\Models\Adapter\ModelBase;

class Mercurio02 extends ModelBase {

    protected $table = 'mercurio02';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'codcaj',
        'nit',
        'razsoc',
        'sigla',
        'email',
        'direccion',
        'telefono',
        'codciu',
        'pagweb',
        'pagfac',
        'pagtwi',
        'pagyou',
    ];



	/**
	 * @var string
	 */
	protected $codcaj;

	/**
	 * @var string
	 */
	protected $nit;

	/**
	 * @var string
	 */
	protected $razsoc;

	/**
	 * @var string
	 */
	protected $sigla;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $direccion;

	/**
	 * @var string
	 */
	protected $telefono;

	/**
	 * @var string
	 */
	protected $codciu;

	/**
	 * @var string
	 */
	protected $pagweb;

	/**
	 * @var string
	 */
	protected $pagfac;

	/**
	 * @var string
	 */
	protected $pagtwi;

	/**
	 * @var string
	 */
	protected $pagyou;


	/**
	 * Metodo para establecer el valor del campo codcaj
	 * @param string $codcaj
	 */
	public function setCodcaj($codcaj){
		$this->codcaj = $codcaj;
	}

	/**
	 * Metodo para establecer el valor del campo nit
	 * @param string $nit
	 */
	public function setNit($nit){
		$this->nit = $nit;
	}

	/**
	 * Metodo para establecer el valor del campo razsoc
	 * @param string $razsoc
	 */
	public function setRazsoc($razsoc){
		$this->razsoc = $razsoc;
	}

	/**
	 * Metodo para establecer el valor del campo sigla
	 * @param string $sigla
	 */
	public function setSigla($sigla){
		$this->sigla = $sigla;
	}

	/**
	 * Metodo para establecer el valor del campo email
	 * @param string $email
	 */
	public function setEmail($email){
		$this->email = $email;
	}

	/**
	 * Metodo para establecer el valor del campo direccion
	 * @param string $direccion
	 */
	public function setDireccion($direccion){
		$this->direccion = $direccion;
	}

	/**
	 * Metodo para establecer el valor del campo telefono
	 * @param string $telefono
	 */
	public function setTelefono($telefono){
		$this->telefono = $telefono;
	}

	/**
	 * Metodo para establecer el valor del campo codciu
	 * @param string $codciu
	 */
	public function setCodciu($codciu){
		$this->codciu = $codciu;
	}

	/**
	 * Metodo para establecer el valor del campo pagweb
	 * @param string $pagweb
	 */
	public function setPagweb($pagweb){
		$this->pagweb = $pagweb;
	}

	/**
	 * Metodo para establecer el valor del campo pagfac
	 * @param string $pagfac
	 */
	public function setPagfac($pagfac){
		$this->pagfac = $pagfac;
	}

	/**
	 * Metodo para establecer el valor del campo pagtwi
	 * @param string $pagtwi
	 */
	public function setPagtwi($pagtwi){
		$this->pagtwi = $pagtwi;
	}

	/**
	 * Metodo para establecer el valor del campo pagyou
	 * @param string $pagyou
	 */
	public function setPagyou($pagyou){
		$this->pagyou = $pagyou;
	}


	/**
	 * Devuelve el valor del campo codcaj
	 * @return string
	 */
	public function getCodcaj(){
		return $this->codcaj;
	}

	/**
	 * Devuelve el valor del campo nit
	 * @return string
	 */
	public function getNit(){
		return $this->nit;
	}

	/**
	 * Devuelve el valor del campo razsoc
	 * @return string
	 */
	public function getRazsoc(){
		return $this->razsoc;
	}

	/**
	 * Devuelve el valor del campo sigla
	 * @return string
	 */
	public function getSigla(){
		return $this->sigla;
	}

	/**
	 * Devuelve el valor del campo email
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * Devuelve el valor del campo direccion
	 * @return string
	 */
	public function getDireccion(){
		return $this->direccion;
	}

	/**
	 * Devuelve el valor del campo telefono
	 * @return string
	 */
	public function getTelefono(){
		return $this->telefono;
	}

	/**
	 * Devuelve el valor del campo codciu
	 * @return string
	 */
	public function getCodciu(){
		return $this->codciu;
	}

	/**
	 * Devuelve el valor del campo pagweb
	 * @return string
	 */
	public function getPagweb(){
		return $this->pagweb;
	}

	/**
	 * Devuelve el valor del campo pagfac
	 * @return string
	 */
	public function getPagfac(){
		return $this->pagfac;
	}

	/**
	 * Devuelve el valor del campo pagtwi
	 * @return string
	 */
	public function getPagtwi(){
		return $this->pagtwi;
	}

	/**
	 * Devuelve el valor del campo pagyou
	 * @return string
	 */
	public function getPagyou(){
		return $this->pagyou;
	}

}

