<?php

namespace App\Services\Utils;

use App\Services\Srequest;

class Pagination
{
    public $cantidadPaginas = 0;
    public $pagina = 1;
    public $query;
    public $estado = 'P';
    public $filters = false;

    public function __construct(Srequest|null $argv = null)
    {
        if ($argv instanceof Srequest) {
            $this->cantidadPaginas = $argv->getParam('cantidadPaginas');
            $this->query = $argv->getParam('query');
            $this->estado = $argv->getParam('estado');
            if ($argv->getParam('pagina')) $this->pagina = $argv->getParam('pagina');
        }
    }

    /**
     * filter function
     * @param string $campo
     * @param string $condi
     * @param string $value
     * @return string
     */
    public function filter($campo, $condi, $value)
    {
        $campo = $this->converSerialize($campo, 'mcampo');
        $condi = $this->converSerialize($condi, 'mcondi');
        $value = $this->converSerialize($value, 'mvalue');
        $this->procesarFilter($campo, $condi, $value);
        return $this->query;
    }

    public function persistencia($params)
    {
        if ($params == false) return $this->query;
        $this->procesarFilter($params['campo'], $params['condi'], $params['value']);
        return $this->query;
    }

    function procesarFilter($campo, $condi, $value)
    {
        if (count($campo) == 0) return $this->query;

        $this->filters = array(
            'campo' => $campo,
            'condi' => $condi,
            'value' => $value
        );

        $data = array();
        for ($i = 0; $i < count($campo); $i++) {
            $mcampo = $campo[$i]['mcampo'];
            $mcondi = $condi[$i]['mcondi'];
            $mvalue = $value[$i]['mvalue'];
            switch ($mcondi) {
                case "como":
                    $mcondi = "like";
                    break;
                case "igual":
                    $mcondi = "=";
                    break;
                case "mayor":
                    $mcondi = ">";
                    break;
                case "menor":
                    $mcondi = "<";
                    break;
                case "mayorigual":
                    $mcondi = ">=";
                    break;
                case "menorigual":
                    $mcondi = "<=";
                    break;
                case "diferente":
                    $mcondi = "<>";
                    break;
            }
            if ($mcondi == "like") {
                $data[] = "{$mcampo} {$mcondi} '%{$mvalue}%'";
            } else {
                $data[] = "{$mcampo} {$mcondi} '{$mvalue}'";
            }
        }
        $this->query = (count($data) > 0) ? $this->query . ' AND ' . implode(" AND ", $data) : $this->query;
    }

    public function converSerialize($str, $indice)
    {
        $data = array();
        $strArray = preg_split("/&/", $str);
        $i = 0;
        foreach ($strArray as $item) {
            $array = preg_split("/=/", $item);
            if (count($array) < 2) continue;
            $data[$i]["$indice"] = trim($array[1]);
            $i++;
        }
        return $data;
    }

    public function setters(...$params)
    {
        $arguments = get_params_destructures($params);
        foreach ($arguments as $prop => $valor) if (property_exists($this, $prop)) $this->$prop = "{$valor}";
        return $this;
    }

    /**
     * render function
     * @param ConyugeServices $service
     * @return array
     */
    public function render($service)
    {
        $modelEntity = $service->findPagination($this->query);
        if ($modelEntity == false) {
            return array(
                "msj" => "No se encuentran registros a mostrar...",
                "consulta" => "",
                "paginate" => "",
                "filtro" => $this->query
            );
        }

        $paginate = Paginate::execute($modelEntity, $this->pagina, $this->cantidadPaginas);
        $html = $service->showTabla($paginate);
        $html_paginate = view('cajas/layouts/paginate', array(
            'paginate' => $paginate,
            'estado'   => $this->estado,
            'event'    => "buscar_pagina(this, '{$this->estado}')",
            'event_pagina' => "cambiar_pagina(this, '{$this->estado}')",
        ))->render();

        return array(
            'consulta' => $html,
            'paginate' => $html_paginate,
            'filtro' => $this->query,
            'msj' => "Consulta realizada con éxito."
        );
    }

    public function adapterQuery($conditions)
    {
        if (is_array($conditions)) {
            $where = array();
            foreach ($conditions as $key => $value) {
                $where[] = " {$key}='{$value}' ";
            }
            $this->query .= implode(" AND ", $where);
        } else {
            if ($conditions) {
                $this->query .= " AND {$conditions} ";
            }
        }
        return $this->query;
    }

    /**
     * getCollection function
     * @changed [2023-12-00]
     * retorna una colleción de datos paginados
     * @author elegroag <elegroag@ibero.edu.co>
     * @param [type] $service
     * @return void
     */
    public function getCollection($service)
    {
        $modelEntity = $service->findPagination($this->query);
        $data = $service->dataOptional($modelEntity, $this->estado);
        return array(
            'data' => $data,
            'pagina' => $this->pagina,
            'cantidad_paginas' => $this->cantidadPaginas,
            'estado' => $this->estado,
            'filters' => $this->filters
        );
    }
}
