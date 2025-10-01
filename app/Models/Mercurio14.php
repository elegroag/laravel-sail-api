<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Mercurio14 extends ModelBase
{

    use HasCompositeKey;

    protected $table = 'mercurio14';
    public $timestamps = false;
    protected $primaryKey = [
        'tipopc',
        'tipsoc',
        'coddoc',
    ];

    protected $fillable = [
        'tipopc',
        'tipsoc',
        'coddoc',
        'obliga',
        'auto_generado',
        'nota',
    ];

    public function setAuto_generado($auto_generado)
    {
        $this->auto_generado = $auto_generado;
    }

    public function getAuto_generado()
    {
        return $this->auto_generado;
    }

    /**
     * Metodo para establecer el valor del campo tipopc
     * @param string $tipopc
     */
    public function setTipopc($tipopc)
    {
        $this->tipopc = $tipopc;
    }

    /**
     * Metodo para establecer el valor del campo tipsoc
     * @param string $tipsoc
     */
    public function setTipsoc($tipsoc)
    {
        $this->tipsoc = $tipsoc;
    }

    /**
     * Metodo para establecer el valor del campo coddoc
     * @param integer $coddoc
     */
    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    /**
     * Metodo para establecer el valor del campo nota
     * @param string $nota
     */
    public function setNota($nota)
    {
        $this->nota = $nota;
    }


    /**
     * Devuelve el valor del campo nota
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Devuelve el valor del campo tipsoc
     * @return string
     */
    public function getTipsoc()
    {
        return $this->tipsoc;
    }

    /**
     * Devuelve el valor del campo coddoc
     * @return integer
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    /**
     * Devuelve el valor del campo obliga
     * @return string
     */
    public function getObliga()
    {
        return $this->obliga;
    }
}
