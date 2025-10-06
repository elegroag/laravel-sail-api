<?php

namespace App\Services;

class Srequest
{
    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function getParam($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function getArguments()
    {
        return array_values($this->params);
    }

    public function getKeys()
    {
        return array_keys($this->params);
    }

    public function count()
    {
        return count($this->params);
    }
}
