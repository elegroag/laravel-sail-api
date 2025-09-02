<?php
namespace App\Models;

use App\Models\Adapter\ModelBase;
class Mercurio18 extends ModelBase {

    protected $table = 'mercurio18';
    public $timestamps = false;
    protected $primaryKey = 'id';

	protected $fillable = [
        'codigo',
        'detalle',
    ];



	/**
	 * @var string
	 */
	protected $codigo;

	/**
	 * @var string
	 */
	protected $detalle;


	/**
	 * Metodo para establecer el valor del campo codigo
	 * @param string $codigo
	 */
	public function setCodigo($codigo){
		$this->codigo = $codigo;
	}

	/**
	 * Metodo para establecer el valor del campo detalle
	 * @param string $detalle
	 */
	public function setDetalle($detalle){
		$this->detalle = $detalle;
	}


	/**
	 * Devuelve el valor del campo codigo
	 * @return string
	 */
	public function getCodigo(){
		return $this->codigo;
	}

	/**
	 * Devuelve el valor del campo detalle
	 * @return string
	 */
	public function getDetalle(){
		return $this->detalle;
	}

}

