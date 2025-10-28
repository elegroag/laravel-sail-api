<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use App\Models\Adapter\ValidateWithRules;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Mercurio10 extends ModelBase
{
    use HasCompositeKey;
    use ValidateWithRules;

    protected $table = 'mercurio10';

    public $timestamps = false;

    // PK compuesta según migración
    protected $primaryKey = ['tipopc', 'numero', 'item'];

    public $incrementing = false;

    protected $fillable = [
        'tipopc',
        'numero',
        'item',
        'estado',
        'nota',
        'fecsis',
        'codest',
        'campos_corregir',
    ];

    protected function rules()
    {
        return [
            'tipopc' => 'required|string|min:1',
            'numero' => 'required|numeric|min:1',
            'item' => 'required|numeric|min:1',
            '_id' => [
                'required|string',
                Rule::unique('mercurio10')->where(function ($query) {
                    return $query->where('tipopc', $this->tipopc)
                        ->where('numero', $this->numero)
                        ->where('item', $this->item);
                }),
            ],
        ];
    }

    public function setCamposCorregir($campos_corregir)
    {
        $this->campos_corregir = $campos_corregir;
    }

    public function getCamposCorregir()
    {
        return $this->campos_corregir;
    }

    /**
     * Metodo para establecer el valor del campo numero
     *
     * @param  int  $numero
     */
    public function setTipopc($tipopc)
    {
        $this->tipopc = $tipopc;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * Metodo para establecer el valor del campo item
     *
     * @param  int  $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * Metodo para establecer el valor del campo estado
     *
     * @param  string  $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * Metodo para establecer el valor del campo nota
     *
     * @param  string  $nota
     */
    public function setNota($nota)
    {
        $this->nota = $nota;
    }

    /**
     * Metodo para establecer el valor del campo fecsis
     */
    public function setFecsis($fecsis)
    {
        $this->fecsis = $fecsis;
    }

    /**
     * Metodo para establecer el valor del campo codest
     *
     * @param  string  $codest
     */
    public function setCodest($codest)
    {
        $this->codest = $codest;
    }

    /**
     * Devuelve el valor del campo numero
     *
     * @return int
     */
    public function getTipopc()
    {
        return $this->tipopc;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Devuelve el valor del campo item
     *
     * @return int
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Devuelve el valor del campo estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Devuelve el valor del campo nota
     *
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Devuelve el valor del campo fecsis
     */
    public function getFecsis()
    {
        return Carbon::parse($this->fecsis);
    }

    /**
     * Devuelve el valor del campo codest
     *
     * @return string
     */
    public function getCodest()
    {
        return $this->codest;
    }

    public function getDetalleEstado()
    {
        switch ($this->estado) {
            case 'T':
                return 'Temporal';
                break;
            case 'D':
                return 'Devuelto';
                break;
            case 'A':
                return 'Aprobado';
                break;
            case 'X':
                return 'Rechazado';
                break;
            case 'P':
                return 'Pendiente de verificación';
                break;
            default:
                return false;
                break;
        }
    }

    public function getArrayEstados()
    {
        return [
            'T' => 'Temporal',
            'D' => 'Devuelto',
            'A' => 'Aprobado',
            'X' => 'Rechazado',
            'P' => 'Pendiente de verificación',
        ];
    }
}
