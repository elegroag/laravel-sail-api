<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use App\Models\Adapter\ValidateWithRules;
use Illuminate\Validation\Rule;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Mercurio37 extends ModelBase
{
    use HasCompositeKey;
    use ValidateWithRules;

    protected $table = 'mercurio37';

    public $timestamps = false;

    protected $primaryKey = ['tipopc', 'numero', 'coddoc'];

    protected $fillable = [
        'tipopc',
        'numero',
        'coddoc',
        'archivo',
        'fhash',
    ];

    protected function rules()
    {
        return [
            'tipopc' => 'required|numeric|min:1',
            'numero' => 'required|numeric|min:1',
            'coddoc' => 'required|numeric|min:1',
            '_id' => [
                'required|string',
                Rule::unique('mercurio37')->where(function ($query) {
                    return $query->where('tipopc', $this->tipopc)
                        ->where('numero', $this->numero)
                        ->where('coddoc', $this->coddoc);
                }),
            ],
        ];
    }

    public function setFhash($fhash)
    {
        $this->fhash = $fhash;
    }

    public function getFhash()
    {
        return $this->fhash;
    }

    /**
     * Metodo para establecer el valor del campo tipopc
     *
     * @param  string  $tipopc
     */
    public function setTipopc($tipopc)
    {
        $this->tipopc = $tipopc;
    }

    /**
     * Metodo para establecer el valor del campo numero
     *
     * @param  int  $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * Metodo para establecer el valor del campo coddoc
     *
     * @param  int  $coddoc
     */
    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    /**
     * Metodo para establecer el valor del campo archivo
     *
     * @param  string  $archivo
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;
    }

    /**
     * Devuelve el valor del campo tipopc
     *
     * @return string
     */
    public function getTipopc()
    {
        return $this->tipopc;
    }

    /**
     * Devuelve el valor del campo numero
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Devuelve el valor del campo coddoc
     *
     * @return int
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    /**
     * Devuelve el valor del campo archivo
     *
     * @return string
     */
    public function getArchivo()
    {
        return $this->archivo;
    }
}
