<?php

namespace App\Models;

use App\Models\Adapter\DbBase;
use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercurio41 extends ModelBase
{

    protected $table = 'mercurio41';
    protected $primaryKey = 'id';

    public $incrementing = true;
    public $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'coddoc',
        'documento',
        'calemp',
        'log',
        'cedtra',
        'tipdoc',
        'priape',
        'segape',
        'prinom',
        'segnom',
        'fecnac',
        'ciunac',
        'sexo',
        'estciv',
        'cabhog',
        'codciu',
        'codzon',
        'direccion',
        'barrio',
        'telefono',
        'celular',
        'email',
        'fecini',
        'salario',
        'captra',
        'tipdis',
        'nivedu',
        'rural',
        'vivienda',
        'tipafi',
        'autoriza',
        'motivo',
        'codact',
        'fecsol',
        'estado',
        'codest',
        'fecest',
        'usuario',
        'coddocrepleg',
        'peretn',
        'resguardo_id',
        'pub_indigena_id',
        'facvul',
        'orisex',
        'tippag',
        'numcue',
        'codcaj',
        'cargo',
        'codban',
        'tipcue',
        'dirlab'
    ];

    public function setTipcue($tipcue)
    {
        $this->tipcue = $tipcue;
    }

    public function getTipcue()
    {
        return $this->tipcue;
    }

    public function setCodban($codban)
    {
        $this->codban = $codban;
    }

    public function getCodban()
    {
        return $this->codban;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    public function getCoddoc()
    {
        return $this->coddoc;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    public function getDocumento()
    {
        return $this->documento;
    }

    public function setCalemp($calemp)
    {
        $this->calemp = $calemp;
    }

    public function getCalemp()
    {
        return $this->calemp;
    }

    public function setLog($log)
    {
        $this->log = $log;
    }

    public function getLog()
    {
        return $this->log;
    }

    public function setCedtra($cedtra)
    {
        $this->cedtra = $cedtra;
    }

    public function getCedtra()
    {
        return $this->cedtra;
    }

    public function setTipdoc($tipdoc)
    {
        $this->tipdoc = $tipdoc;
    }

    public function getTipdoc()
    {
        return $this->tipdoc;
    }

    public function setPriape($priape)
    {
        $this->priape = $priape;
    }

    public function getPriape()
    {
        return $this->priape;
    }

    public function setSegape($segape)
    {
        $this->segape = $segape;
    }

    public function getSegape()
    {
        return $this->segape;
    }

    public function setPrinom($prinom)
    {
        $this->prinom = $prinom;
    }

    public function getPrinom()
    {
        return $this->prinom;
    }

    public function setSegnom($segnom)
    {
        $this->segnom = $segnom;
    }

    public function getSegnom()
    {
        return $this->segnom;
    }

    public function setFecnac($fecnac)
    {
        $this->fecnac = $fecnac;
    }

    public function getFecnac()
    {
        return (is_null($this->fecnac)) ? '' : Carbon::parse($this->fecnac);
    }

    public function setCiunac($ciunac)
    {
        $this->ciunac = $ciunac;
    }

    public function getCiunac()
    {
        return $this->ciunac;
    }

    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }

    public function getSexo()
    {
        return $this->sexo;
    }

    public function setEstciv($estciv)
    {
        $this->estciv = $estciv;
    }

    public function getEstciv()
    {
        return $this->estciv;
    }

    public function setCabhog($cabhog)
    {
        $this->cabhog = $cabhog;
    }

    public function getCabhog()
    {
        return $this->cabhog;
    }

    public function setCodciu($codciu)
    {
        $this->codciu = $codciu;
    }

    public function getCodciu()
    {
        return $this->codciu;
    }

    public function setCodzon($codzon)
    {
        $this->codzon = $codzon;
    }

    public function getCodzon()
    {
        return $this->codzon;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function setDirlab($dirlab)
    {
        $this->dirlab = $dirlab;
    }

    public function getDirlab()
    {
        return $this->dirlab;
    }

    public function setBarrio($barrio)
    {
        $this->barrio = $barrio;
    }

    public function getBarrio()
    {
        return $this->barrio;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    public function getCelular()
    {
        return $this->celular;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setFecini($fecini)
    {
        $this->fecini = $fecini;
    }

    public function getFecini()
    {
        return (is_null($this->fecini)) ? '' : Carbon::parse($this->fecini);
    }

    public function setSalario($salario)
    {
        $this->salario = $salario;
    }

    public function getSalario()
    {
        return $this->salario;
    }

    public function setCaptra($captra)
    {
        $this->captra = $captra;
    }

    public function getCaptra()
    {
        return $this->captra;
    }

    public function setTipdis($tipdis)
    {
        $this->tipdis = $tipdis;
    }

    public function getTipdis()
    {
        return $this->tipdis;
    }

    public function setNivedu($nivedu)
    {
        $this->nivedu = $nivedu;
    }

    public function getNivedu()
    {
        return $this->nivedu;
    }

    public function setRural($rural)
    {
        $this->rural = $rural;
    }

    public function getRural()
    {
        return $this->rural;
    }

    public function setVivienda($vivienda)
    {
        $this->vivienda = $vivienda;
    }

    public function getVivienda()
    {
        return $this->vivienda;
    }

    public function setTipafi($tipafi)
    {
        $this->tipafi = $tipafi;
    }

    public function getTipafi()
    {
        return $this->tipafi;
    }

    public function setAutoriza($autoriza)
    {
        $this->autoriza = $autoriza;
    }

    public function getAutoriza()
    {
        return $this->autoriza;
    }

    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }

    public function getMotivo()
    {
        return $this->motivo;
    }

    public function setCodact($codact)
    {
        $this->codact = $codact;
    }

    public function getCodact()
    {
        return $this->codact;
    }

    public function setFecsol($fecsol)
    {
        $this->fecsol = $fecsol;
    }

    public function getFecsol()
    {
        return (is_null($this->fecsol)) ? '' : Carbon::parse($this->fecsol);
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setCodest($codest)
    {
        $this->codest = $codest;
    }

    public function getCodest()
    {
        return $this->codest;
    }

    public function setFecest($fecest)
    {
        $this->fecest = $fecest;
    }

    public function getFecest()
    {
        return (is_null($this->fecest)) ? '' : Carbon::parse($this->fecest);
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function setCoddocrepleg($coddocrepleg)
    {
        $this->coddocrepleg = $coddocrepleg;
    }

    public function getCoddocrepleg()
    {
        return $this->coddocrepleg;
    }

    public function setPeretn($peretn)
    {
        $this->peretn = $peretn;
    }

    public function getPeretn()
    {
        return $this->peretn;
    }

    public function setResguardo_id($resguardo_id)
    {
        $this->resguardo_id = $resguardo_id;
    }

    public function getResguardo_id()
    {
        return $this->resguardo_id;
    }

    public function setPub_indigena_id($pub_indigena_id)
    {
        $this->pub_indigena_id = $pub_indigena_id;
    }

    public function getPub_indigena_id()
    {
        return $this->pub_indigena_id;
    }

    public function setFacvul($facvul)
    {
        $this->facvul = $facvul;
    }

    public function getFacvul()
    {
        return $this->facvul;
    }

    public function setOrisex($orisex)
    {
        $this->orisex = $orisex;
    }

    public function getOrisex()
    {
        return $this->orisex;
    }

    public function setTippag($tippag)
    {
        $this->tippag = $tippag;
    }

    public function getTippag()
    {
        return $this->tippag;
    }

    public function setNumcue($numcue)
    {
        $this->numcue = $numcue;
    }

    public function getNumcue()
    {
        return $this->numcue;
    }

    public function setCodcaj($codcaj)
    {
        $this->codcaj = $codcaj;
    }

    public function getCodcaj()
    {
        return $this->codcaj;
    }

    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
    }

    public function getCargo()
    {
        return $this->cargo;
    }

    public function CamposDisponibles()
    {
        $db = DbBase::rawConnect();
        $rqs = $db->inQueryAssoc("SELECT * FROM mercurio12");
        $data = array();
        foreach ($rqs as $ai => $row) $data[$row['coddoc']] = $row['detalle'];
        return $data;
    }

    public function getCalempDetalle()
    {
        switch ($this->calemp) {
            case 'E':
                return 'EMPRESA';
                break;
            case 'I':
                return 'INDEPENDIENTE';
                break;
            case 'P':
                return 'PENSIONADO';
                break;
            case 'F':
                return 'FACULTATIVO';
                break;
            case 'D':
                return 'DESEMPLEADO';
                break;
            default:
                return null;
                break;
        }
    }

    public function getCalempArray()
    {
        return array(
            'E' => 'EMPRESA',
            'I' => 'INDEPENDIENTE',
            'P' => 'PENSIONADO',
            'F' => 'FACULTATIVO',
            'D' => 'DESEMPLEADO'
        );
    }

    public function getCoddocreplegArray()
    {
        return array(
            1 => 'CC',
            10 => 'TMF',
            11 => 'CD',
            12 => 'ISE',
            13 => 'V',
            14 => 'PT',
            2 => 'TI',
            3 => 'NI',
            4 => 'CE',
            5 => 'NU',
            6 => 'PA',
            7 => 'RC',
            8 => 'PEP',
            9 => 'CB'
        );
    }

    public function getEstadoDetalle()
    {
        $return = "";
        if ($this->estado == "T") $return = "TEMPORAL";
        if ($this->estado == "D") $return = "DEVUELTO";
        if ($this->estado == "A") $return = "APROBADO";
        if ($this->estado == "X") $return = "RECHAZADO";
        return $return;
    }

    public function CamposDisponibleDetalle($campo)
    {
        $data = $this->CamposDisponibles();
        return $data["{$campo}"];
    }

    public function getFecsolString()
    {
        return (isset($this->fecsol)) ? $this->fecsol : null;
    }

    public function getNombreCompleto()
    {
        return $this->priape . " " . $this->segape . " " . $this->prinom . " " . $this->segnom;
    }
}
