<?php

namespace App\Services\Utils;

use App\Exceptions\DebugException;
use App\Models\Mercurio07;
use App\Models\Mercurio19;
use Carbon\Carbon;

class CrearUsuario
{
    private $today;
    private $tipo;
    private $coddoc;
    private $documento;
    private $nombre;
    private $email;
    private $codciu;
    private $clave;
    private $feccla;
    private $fecreg;

    public function __construct()
    {
        $this->today = Carbon::parse('now');
        $this->codciu = '18001';
    }

    public function setters(...$params)
    {
        $arguments = get_params_destructures($params);
        foreach ($arguments as $prop => $valor) if (property_exists($this, $prop)) $this->$prop = "{$valor}";
        return $this;
    }

    /**
     * crear_usuario function
     * @return Object
     */
    public function procesar()
    {
        $fecreg = ($this->fecreg) ? $this->fecreg : date('Y-m-d H:i:s');
        $feccla = ($this->feccla) ? $this->feccla : $this->today->addMonths(3);
        $mercurio07 = (new Mercurio07())->findFirst(" tipo='{$this->tipo}' and coddoc='{$this->coddoc}' and documento='{$this->documento}' ");
        if ($mercurio07 == false) {
            $mercurio07 = new Mercurio07;
            $mercurio07->setTipo($this->tipo);
            $mercurio07->setCoddoc($this->coddoc);
            $mercurio07->setDocumento($this->documento);
            $mercurio07->setFecreg($fecreg);
            $mercurio07->setFeccla($feccla);
        }

        $mercurio07->setCodciu($this->codciu);
        $mercurio07->setEstado("A");
        $mercurio07->setFechaSyncron(date('Y-m-d'));
        $mercurio07->setAutoriza("S");
        $mercurio07->setNombre($this->nombre);
        $mercurio07->setEmail($this->email);
        $mercurio07->setClave($this->clave);

        if (!$mercurio07->save()) {
            $msj = "";
            foreach ($mercurio07->getMessages() as $m07) $msj .= $m07->getMessage() . "\n";
            throw new DebugException("Error " . $msj, 503);
        }
        return $mercurio07;
    }

    /**
     * crearOpcionesRecuperacion function
     * @param [type] $pregunta1
     * @param [type] $pregunta2
     * @param [type] $respuesta1
     * @param [type] $respuesta2
     * @return bool
     */
    public function crearOpcionesRecuperacion($codigo)
    {
        $mercurio19 = (new Mercurio19())->findFirst("tipo='{$this->tipo}' and coddoc='{$this->coddoc}' and documento='{$this->documento}'");
        if (!$mercurio19) {
            $mercurio19 = new Mercurio19();
            $mercurio19->setTipo($this->tipo);
            $mercurio19->setCoddoc($this->coddoc);
            $mercurio19->setDocumento($this->documento);
            $mercurio19->setRespuesta('0');
        }
        $mercurio19->setInicio(date('Y-m-d H:i:s'));
        $mercurio19->setIntentos(0);
        $mercurio19->setCodigo(1);
        $mercurio19->setCodver($codigo);

        if (!$mercurio19->save()) {
            $msj = "";
            foreach ($mercurio19->getMessages() as $m07) $msj .= $m07->getMessage() . "\n";
            throw new DebugException("Error " . $msj, 503);
        }
        return true;
    }
}
