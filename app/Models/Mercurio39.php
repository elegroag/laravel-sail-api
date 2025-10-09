<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Mercurio39 extends ModelBase
{
    protected $table = 'mercurio39';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
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
        'fax',
        'email',
        'fecing',
        'salario',
        'captra',
        'tipdis',
        'nivedu',
        'rural',
        'vivienda',
        'tipafi',
        'autoriza',
        'codact',
        'calemp',
        'usuario',
        'estado',
        'codest',
        'motivo',
        'fecest',
        'tipo',
        'coddoc',
        'documento',
        'fecsol',
    ];

    /**
     * Metodo para establecer el valor del campo id
     *
     * @param  int  $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Metodo para establecer el valor del campo log
     *
     * @param  int  $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * Metodo para establecer el valor del campo cedtra
     *
     * @param  string  $cedtra
     */
    public function setCedtra($cedtra)
    {
        $this->cedtra = $cedtra;
    }

    /**
     * Metodo para establecer el valor del campo tipdoc
     *
     * @param  string  $tipdoc
     */
    public function setTipdoc($tipdoc)
    {
        $this->tipdoc = $tipdoc;
    }

    /**
     * Metodo para establecer el valor del campo priape
     *
     * @param  string  $priape
     */
    public function setPriape($priape)
    {
        $this->priape = $priape;
    }

    /**
     * Metodo para establecer el valor del campo segape
     *
     * @param  string  $segape
     */
    public function setSegape($segape)
    {
        $this->segape = $segape;
    }

    /**
     * Metodo para establecer el valor del campo prinom
     *
     * @param  string  $prinom
     */
    public function setPrinom($prinom)
    {
        $this->prinom = $prinom;
    }

    /**
     * Metodo para establecer el valor del campo segnom
     *
     * @param  string  $segnom
     */
    public function setSegnom($segnom)
    {
        $this->segnom = $segnom;
    }

    /**
     * Metodo para establecer el valor del campo fecnac
     */
    public function setFecnac($fecnac)
    {
        $this->fecnac = $fecnac;
    }

    /**
     * Metodo para establecer el valor del campo ciunac
     *
     * @param  string  $ciunac
     */
    public function setCiunac($ciunac)
    {
        $this->ciunac = $ciunac;
    }

    /**
     * Metodo para establecer el valor del campo sexo
     *
     * @param  string  $sexo
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }

    /**
     * Metodo para establecer el valor del campo estciv
     *
     * @param  string  $estciv
     */
    public function setEstciv($estciv)
    {
        $this->estciv = $estciv;
    }

    /**
     * Metodo para establecer el valor del campo cabhog
     *
     * @param  string  $cabhog
     */
    public function setCabhog($cabhog)
    {
        $this->cabhog = $cabhog;
    }

    /**
     * Metodo para establecer el valor del campo codciu
     *
     * @param  string  $codciu
     */
    public function setCodciu($codciu)
    {
        $this->codciu = $codciu;
    }

    /**
     * Metodo para establecer el valor del campo codzon
     *
     * @param  string  $codzon
     */
    public function setCodzon($codzon)
    {
        $this->codzon = $codzon;
    }

    /**
     * Metodo para establecer el valor del campo direccion
     *
     * @param  string  $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    /**
     * Metodo para establecer el valor del campo barrio
     *
     * @param  string  $barrio
     */
    public function setBarrio($barrio)
    {
        $this->barrio = $barrio;
    }

    /**
     * Metodo para establecer el valor del campo telefono
     *
     * @param  string  $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    /**
     * Metodo para establecer el valor del campo celular
     *
     * @param  string  $celular
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    /**
     * Metodo para establecer el valor del campo fax
     *
     * @param  string  $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * Metodo para establecer el valor del campo email
     *
     * @param  string  $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Metodo para establecer el valor del campo fecing
     */
    public function setFecing($fecing)
    {
        $this->fecing = $fecing;
    }

    /**
     * Metodo para establecer el valor del campo salario
     *
     * @param  int  $salario
     */
    public function setSalario($salario)
    {
        $this->salario = $salario;
    }

    /**
     * Metodo para establecer el valor del campo captra
     *
     * @param  string  $captra
     */
    public function setCaptra($captra)
    {
        $this->captra = $captra;
    }

    /**
     * Metodo para establecer el valor del campo tipdis
     *
     * @param  string  $tipdis
     */
    public function setTipdis($tipdis)
    {
        $this->tipdis = $tipdis;
    }

    /**
     * Metodo para establecer el valor del campo nivedu
     *
     * @param  string  $nivedu
     */
    public function setNivedu($nivedu)
    {
        $this->nivedu = $nivedu;
    }

    /**
     * Metodo para establecer el valor del campo rural
     *
     * @param  string  $rural
     */
    public function setRural($rural)
    {
        $this->rural = $rural;
    }

    /**
     * Metodo para establecer el valor del campo vivienda
     *
     * @param  string  $vivienda
     */
    public function setVivienda($vivienda)
    {
        $this->vivienda = $vivienda;
    }

    /**
     * Metodo para establecer el valor del campo tipafi
     *
     * @param  string  $tipafi
     */
    public function setTipafi($tipafi)
    {
        $this->tipafi = $tipafi;
    }

    /**
     * Metodo para establecer el valor del campo autoriza
     *
     * @param  string  $autoriza
     */
    public function setAutoriza($autoriza)
    {
        $this->autoriza = $autoriza;
    }

    /**
     * Metodo para establecer el valor del campo codact
     *
     * @param  string  $codact
     */
    public function setCodact($codact)
    {
        $this->codact = $codact;
    }

    /**
     * Metodo para establecer el valor del campo calemp
     *
     * @param  string  $calemp
     */
    public function setCalemp($calemp)
    {
        $this->calemp = $calemp;
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
     * Metodo para establecer el valor del campo estado
     *
     * @param  string  $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
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
     * Metodo para establecer el valor del campo motivo
     *
     * @param  string  $motivo
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }

    /**
     * Metodo para establecer el valor del campo fecest
     */
    public function setFecest($fecest)
    {
        $this->fecest = $fecest;
    }

    /**
     * Metodo para establecer el valor del campo tipo
     *
     * @param  string  $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * Metodo para establecer el valor del campo coddoc
     *
     * @param  string  $coddoc
     */
    public function setCoddoc($coddoc)
    {
        $this->coddoc = $coddoc;
    }

    /**
     * Metodo para establecer el valor del campo documento
     *
     * @param  string  $documento
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    /**
     * Devuelve el valor del campo id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Devuelve el valor del campo log
     *
     * @return int
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Devuelve el valor del campo cedtra
     *
     * @return string
     */
    public function getCedtra()
    {
        return $this->cedtra;
    }

    /**
     * Devuelve el valor del campo tipdoc
     *
     * @return string
     */
    public function getTipdoc()
    {
        return $this->tipdoc;
    }

    /**
     * Devuelve el valor del campo priape
     *
     * @return string
     */
    public function getPriape()
    {
        return $this->priape;
    }

    /**
     * Devuelve el valor del campo segape
     *
     * @return string
     */
    public function getSegape()
    {
        return $this->segape;
    }

    /**
     * Devuelve el valor del campo prinom
     *
     * @return string
     */
    public function getPrinom()
    {
        return $this->prinom;
    }

    /**
     * Devuelve el valor del campo segnom
     *
     * @return string
     */
    public function getSegnom()
    {
        return $this->segnom;
    }

    /**
     * Devuelve el valor del campo fecnac
     */
    public function getFecnac()
    {
        return Carbon::parse($this->fecnac);
    }

    /**
     * Devuelve el valor del campo ciunac
     *
     * @return string
     */
    public function getCiunac()
    {
        return $this->ciunac;
    }

    /**
     * Devuelve el valor del campo sexo
     *
     * @return string
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Devuelve el valor del campo estciv
     *
     * @return string
     */
    public function getEstciv()
    {
        return $this->estciv;
    }

    /**
     * Devuelve el valor del campo cabhog
     *
     * @return string
     */
    public function getCabhog()
    {
        return $this->cabhog;
    }

    /**
     * Devuelve el valor del campo codciu
     *
     * @return string
     */
    public function getCodciu()
    {
        return $this->codciu;
    }

    /**
     * Devuelve el valor del campo codzon
     *
     * @return string
     */
    public function getCodzon()
    {
        return $this->codzon;
    }

    /**
     * Devuelve el valor del campo direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Devuelve el valor del campo barrio
     *
     * @return string
     */
    public function getBarrio()
    {
        return $this->barrio;
    }

    /**
     * Devuelve el valor del campo telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Devuelve el valor del campo celular
     *
     * @return string
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * Devuelve el valor del campo fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Devuelve el valor del campo email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Devuelve el valor del campo fecing
     */
    public function getFecing()
    {
        return Carbon::parse($this->fecing);
    }

    /**
     * Devuelve el valor del campo salario
     *
     * @return int
     */
    public function getSalario()
    {
        return $this->salario;
    }

    /**
     * Devuelve el valor del campo captra
     *
     * @return string
     */
    public function getCaptra()
    {
        return $this->captra;
    }

    /**
     * Devuelve el valor del campo tipdis
     *
     * @return string
     */
    public function getTipdis()
    {
        return $this->tipdis;
    }

    /**
     * Devuelve el valor del campo nivedu
     *
     * @return string
     */
    public function getNivedu()
    {
        return $this->nivedu;
    }

    /**
     * Devuelve el valor del campo rural
     *
     * @return string
     */
    public function getRural()
    {
        return $this->rural;
    }

    /**
     * Devuelve el valor del campo vivienda
     *
     * @return string
     */
    public function getVivienda()
    {
        return $this->vivienda;
    }

    /**
     * Devuelve el valor del campo tipafi
     *
     * @return string
     */
    public function getTipafi()
    {
        return $this->tipafi;
    }

    /**
     * Devuelve el valor del campo autoriza
     *
     * @return string
     */
    public function getAutoriza()
    {
        return $this->autoriza;
    }

    /**
     * Devuelve el valor del campo codact
     *
     * @return string
     */
    public function getCodact()
    {
        return $this->codact;
    }

    /**
     * Devuelve el valor del campo calemp
     *
     * @return string
     */
    public function getCalemp()
    {
        return $this->calemp;
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
     * Devuelve el valor del campo estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoDetalle()
    {
        $return = '';
        if ($this->estado == 'T') {
            $return = 'TEMPORAL';
        }
        if ($this->estado == 'D') {
            $return = 'DEVUELTO';
        }
        if ($this->estado == 'A') {
            $return = 'APROBADO';
        }
        if ($this->estado == 'X') {
            $return = 'RECHAZADO';
        }
        if ($this->estado == 'P') {
            $return = 'PENDIENTE';
        }

        return $return;
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

    /**
     * Devuelve el valor del campo motivo
     *
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Devuelve el valor del campo fecest
     */
    public function getFecest()
    {
        return Carbon::parse($this->fecest);
    }

    /**
     * Devuelve el valor del campo tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Devuelve el valor del campo coddoc
     *
     * @return string
     */
    public function getCoddoc()
    {
        return $this->coddoc;
    }

    /**
     * Devuelve el valor del campo documento
     *
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    public function getFecsol()
    {
        return Carbon::parse($this->fecsol);
    }

    public function setFecsol($fecsol)
    {
        $this->fecsol = $fecsol;
    }
}
