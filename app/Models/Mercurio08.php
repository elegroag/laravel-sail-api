<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Thiagoprz\CompositeKey\HasCompositeKey;
use App\Models\Adapter\ValidateWithRules;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;

class Mercurio08 extends ModelBase
{
    use HasCompositeKey;
    use ValidateWithRules;

    protected $table = 'mercurio08';

    public $timestamps = false;

    protected $primaryKey = ['codofi', 'tipopc', 'usuario'];

    protected $fillable = [
        'codofi',
        'tipopc',
        'usuario',
        'orden',
    ];

    protected function rules()
    {
        return [
            'codofi' => 'required|string|min:2',
            'tipopc' => 'required|numeric|min:1',
            'usuario' => 'required|numeric|min:1',
            '_id' => [
                'required|string',
                Rule::unique('mercurio08')->where(function ($query) {
                    return $query->where('codofi', $this->codofi)
                        ->where('tipopc', $this->tipopc)
                        ->where('usuario', $this->usuario);
                }),
            ],
        ];
    }

    /**
     * Metodo para establecer el valor del campo codofi
     *
     * @param  string  $codofi
     */
    public function setCodofi($codofi)
    {
        $this->codofi = $codofi;
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
     * Metodo para establecer el valor del campo usuario
     *
     * @param  int  $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
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
     * Devuelve el valor del campo codofi
     *
     * @return string
     */
    public function getCodofi()
    {
        return $this->codofi;
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
     * Devuelve el valor del campo usuario
     *
     * @return int
     */
    public function getUsuario()
    {
        return $this->usuario;
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

    public function gener02(): BelongsTo
    {
        return $this->belongsTo(Gener02::class);
    }

    public function mercurio04(): BelongsTo
    {
        return $this->belongsTo(Mercurio04::class);
    }

    public function mercurio09(): BelongsTo
    {
        return $this->belongsTo(Mercurio09::class);
    }
}
