<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Gener09 extends ModelBase
{
    protected $table = 'gener09';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'codzon',
        'detzon',
    ];

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCodzon($codzon)
    {
        $this->codzon = $codzon;
    }

    public function getCodzon()
    {
        return $this->codzon;
    }

    public function setDetzon($detzon)
    {
        $this->detzon = $detzon;
    }

    public function getDetzon()
    {
        return $this->detzon;
    }
}
