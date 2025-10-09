<?php

namespace App\Services\Api;

abstract class ApiAbstract
{
    protected $app;

    protected $db;

    protected $output = [];

    protected $lineaComando;

    public function __construct() {}

    abstract public function send($attr);

    public function getLineaComando()
    {
        return $this->lineaComando;
    }

    public function isJson()
    {
        if ($this->output == '' || is_null($this->output) || $this->output == false) {
            return false;
        }

        return true;
    }

    /**
     * toArray function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return array
     */
    public function toArray()
    {
        return $this->output;
    }

    /**
     * getObject function
     *
     * @changed [2023-12-00]
     *
     * @author elegroag <elegroag@ibero.edu.co>
     *
     * @return object
     */
    public function getObject()
    {
        return (object) $this->output;
    }
}
