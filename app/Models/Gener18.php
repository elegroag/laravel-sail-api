<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Gener18 extends ModelBase
{

    protected $table = 'gener18';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'coddoc',
        'detdoc',
        'codrua',
    ];

    /**
     * @param string $coddoc
     */
    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    /**
     * @param string $detdoc
     */
    public function setDetdoc($detdoc)
    {
        $this->detdoc = $detdoc;
    }

    /**
     * @param string $detdoc
     */
    public function setCodrua($codrua)
    {
        $this->codrua = $codrua;
    }


    /**
     * @return string
     */
    public function getCodrua()
    {
        return $this->codrua;
    }

    /**
     * @return string
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    /**
     * @return string
     */
    public function getDetdoc()
    {
        return $this->detdoc;
    }
}
