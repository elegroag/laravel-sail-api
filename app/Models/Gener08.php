<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

class Gener08 extends ModelBase
{

    protected $table = 'gener08';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'codciu',
        'detciu',
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

    public function mercurio05()
    {
        return $this->belongsTo(Mercurio05::class);
    }
}
