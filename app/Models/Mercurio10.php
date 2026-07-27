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
        'ruuid',
        'cerrada',
        'feccie',
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

    public function setRuuid(?string $ruuid): void
    {
        $this->ruuid = $ruuid;
    }

    public function getRuuid(): ?string
    {
        return $this->ruuid;
    }

    public function setCerrada(?string $cerrada): void
    {
        $this->cerrada = $cerrada;
    }

    public function getCerrada(): ?string
    {
        return $this->cerrada;
    }

    public function setFeccie(?string $feccie): void
    {
        $this->feccie = $feccie;
    }

    public function getFeccie(): ?string
    {
        return $this->feccie;
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

    public function getDetalleEstado(): string|bool
    {
        switch ($this->estado) {
            case 'T':
                return 'Temporal';
            case 'D':
                return 'Devuelto';
            case 'A':
                return 'Aprobado';
            case 'X':
                return 'Rechazado';
            case 'P':
                return 'Pendiente de verificación';
            default:
                return false;
        }
    }

    public function getArrayEstados(): array
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
