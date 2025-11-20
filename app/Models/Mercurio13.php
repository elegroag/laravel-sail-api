<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Thiagoprz\CompositeKey\HasCompositeKey;
use App\Models\Adapter\ValidateWithRules;
use Illuminate\Validation\Rule;

class Mercurio13 extends ModelBase
{
    use HasCompositeKey;
    use ValidateWithRules;

    protected $table = 'mercurio13';

    public $timestamps = false;

    protected $primaryKey = [
        'tipopc',
        'coddoc'
    ];

    protected function rules()
    {
        return [
            'tipopc' => 'required|string|min:0',
            'coddoc' => 'required|numeric|min:1',
            '_id' => [
                'required|string',
                Rule::unique('mercurio13')->where(function ($query) {
                    return $query->where('tipopc', $this->tipopc)
                        ->where('coddoc', $this->coddoc);
                }),
            ],
        ];
    }


    protected $fillable = [
        'tipopc',
        'coddoc',
        'obliga',
        'auto_generado',
    ];

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
     * Metodo para establecer el valor del campo coddoc
     *
     * @param  int  $coddoc
     */
    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    /**
     * Metodo para establecer el valor del campo obliga
     *
     * @param  string  $obliga
     */
    public function setObliga($obliga)
    {
        $this->obliga = $obliga;
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
     * Devuelve el valor del campo coddoc
     *
     * @return int
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    /**
     * Devuelve el valor del campo obliga
     *
     * @return string
     */
    public function getObliga()
    {
        return $this->obliga;
    }

    protected $auto_generado;

    public function setAuto_generado($auto_generado)
    {
        $this->auto_generado = $auto_generado;
    }

    public function getAuto_generado()
    {
        return $this->auto_generado;
    }

    public function mercurio09()
    {
        return $this->belongsTo(Mercurio09::class, 'tipopc');
    }

    public function mercurio12()
    {
        return $this->belongsTo(Mercurio12::class, 'coddoc');
    }
}
