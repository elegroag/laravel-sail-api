<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Thiagoprz\CompositeKey\HasCompositeKey;
use App\Models\Adapter\ValidateWithRules;
use Illuminate\Validation\Rule;

class Mercurio28 extends ModelBase
{
    use HasCompositeKey;
    use ValidateWithRules;

    protected $table = 'mercurio28';

    public $timestamps = false;

    protected $primaryKey = ['tipo', 'campo'];

    protected $fillable = [
        'tipo',
        'campo',
        'detalle',
        'orden',
    ];

    protected function rules()
    {
        return [
            'tipo' => 'required|string|min:0',
            'campo' => 'required|string|min:0',
            '_id' => [
                'required|string',
                Rule::unique('mercurio28')->where(function ($query) {
                    return $query->where('tipo', $this->tipo)
                        ->where('campo', $this->campo);
                }),
            ],
        ];
    }


    /**
     * Metodo para establecer el valor del campo tipo
     *
     * @param  string  $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Metodo para establecer el valor del campo campo
     *
     * @param  string  $campo
     */
    public function setCampo($campo)
    {
        $this->campo = $campo;
    }

    /**
     * Metodo para establecer el valor del campo detalle
     *
     * @param  string  $detalle
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }

    /**
     * Metodo para establecer el valor del campo orden
     *
     * @param  int  $orden
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;
    }

    /**
     * Devuelve el valor del campo tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Devuelve el valor del campo campo
     *
     * @return string
     */
    public function getCampo()
    {
        return $this->campo;
    }

    /**
     * Devuelve el valor del campo detalle
     *
     * @return string
     */
    public function getDetalle()
    {
        return $this->detalle;
    }

    /**
     * Devuelve el valor del campo orden
     *
     * @return int
     */
    public function getOrden()
    {
        return $this->orden;
    }
}
