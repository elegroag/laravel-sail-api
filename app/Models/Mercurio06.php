<?php
namespace App\Models;

use App\Models\Adapter\ModelBase;
class Mercurio06 extends ModelBase {

    protected $table = 'mercurio06';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipo',
        'detalle',
    ];



	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var string
	 */
	protected $detalle;


	/**
	 * Metodo para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Metodo para establecer el valor del campo detalle
	 * @param string $detalle
	 */
	public function setDetalle($detalle){
		$this->detalle = $detalle;
	}


	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo detalle
	 * @return string
	 */
	public function getDetalle(){
		return $this->detalle;
	}

}

