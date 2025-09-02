<?php
namespace App\Models;

use App\Models\Adapter\ModelBase;
class Subsi54 extends ModelBase
{


    protected $table = 'subsi54';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipsoc',
        'detalle',
    ];



    protected $id;
    protected $tipsoc;
    protected $detalle;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTipsoc()
    {
        return $this->tipsoc;
    }

    public function setTipsoc($tipsoc)
    {
        $this->tipsoc = $tipsoc;
    }

    public function getDetalle()
    {
        return $this->detalle;
    }

    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;
    }
}
