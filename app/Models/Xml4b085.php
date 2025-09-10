<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Xml4b085 extends ModelBase
{
    protected $table = 'xml4b085';
    public $timestamps = false;
    protected $primaryKey = 'codins';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codins',
        'detins',
    ];

    // Setters
    public function setCodins($codins)
    {
        $this->codins = $codins;
    }

    public function setDetins($detins)
    {
        $this->detins = $detins;
    }

    // Getters
    public function getCodins()
    {
        return $this->codins;
    }

    public function getDetins()
    {
        return $this->detins;
    }
}
