<?php
namespace App\Models;

use App\Models\Adapter\ModelBase;

class ComandoEstructuras extends ModelBase {

    protected $table = 'comando_estructuras';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'procesador',
        'estructura',
        'variables',
        'tipo',
        'sistema',
        'env',
        'descripcion',
        'asyncro',
    ];



    protected $id;
    protected $procesador;
    protected $estructura;
    protected $variables;
    protected $tipo;
    protected $sistema;
    protected $env;
    protected $descripcion;
    protected $asyncro;
    
    public function setAsyncro($asyncro)
    {
        $this->asyncro = $asyncro;
    }

    public function getAsyncro()
    {
        return $this->asyncro;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setProcesador($procesador)
    {
        $this->procesador = $procesador;
    }

    public function getProcesador()
    {
        return $this->procesador;
    }

    public function setEstructura($estructura)
    {
        $this->estructura = $estructura;
    }

    public function getEstructura()
    {
        return $this->estructura;
    }

    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    public function getVariables()
    {
        return $this->variables;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function setSistema($sistema)
    {
        $this->sistema = $sistema;
    }

    public function getSistema()
    {
        return $this->sistema;
    }

    public function setEnv($env)
    {
        $this->env = $env;
    }

    public function getEnv()
    {
        return $this->env;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }   

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    public function getProcesadorArray()
    {
        return array(
            'php'   => 'PHP5.4', 
            'p7'    => 'PHP7.3',
            'py'    => 'PYTHON3',
            'javac' => 'JAVA COMPILER',
            'npm'   => 'NODE.JS'
        );
    }

    public function getProcesadorDetalle($procesador='')
    {
        if(!empty($procesador)){
            $this->procesador = $procesador;
        }
        switch ($this->procesador) {
            case 'p7':
                return 'PHP7.3';
            break;
            case 'php':
                return 'PHP5.4';
            break;
            case 'py':
                return 'PYTHON3';
            break;
            case 'javac':
                return 'JAVA COMPILER';
            break;
            case 'npm':
                return 'NODE.JS';
            break;
        }
    }

    public function initialize()
    {
        $this->hasMany("estructura","comandos","id");
        $this->belongsTo("estructura","comandos","id");
    }
}