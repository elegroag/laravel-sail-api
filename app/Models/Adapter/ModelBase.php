<?php

namespace App\Models\Adapter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModelBase extends Model
{
    use HasFactory;

    protected $db;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->db = DbBase::rawConnect();
    }

    public function findFirst(...$data)
    {
        $params = get_params_destructures($data);
        $query = DB::table($this->getTable());

        if (isset($params['conditions'])) {
            $conditions = $params['conditions'];
            if (is_array($conditions)) {
                $conditions = implode(',', $conditions);
            }
            $query->whereRaw($conditions);
        } else {
            if (isset($params[0]) && is_string($params[0]) && trim($params[0]) != '') {
                $query->whereRaw($params[0]);
            }
        }

        if (isset($params['order'])) {
            $orders = is_array($params['order']) ? $params['order'] : [$params['order']];
            foreach ($orders as $order) {
                if (preg_match('/([a-zA-Z_0-9]+) (ASC|DESC)/', $order, $regs)) {
                    $query->orderBy($regs[1], $regs[2]);
                } else {
                    $query->orderBy($order);
                }
            }
        }

        if (isset($params['columns'])) {
            $columns = is_array($params['columns']) ? $params['columns'] : explode(',', $params['columns']);
            $query->select($columns);
        } else {
            $query->select('*');
        }

        $result = $query->first();

        if ($result) {
            foreach ($result as $key => $value) {
                $this->$key = $value;
            }

            return $this;
        } else {
            return null;
        }
    }

    public function getFind(...$data)
    {
        $params = get_params_destructures($data);
        $query = DB::table($this->getTable());

        if (isset($params['conditions'])) {
            $conditions = $params['conditions'];
            if (is_array($conditions)) {
                $conditions = implode(',', $conditions);
            }
            $query->whereRaw($conditions);
        } else {
            if (isset($params[0]) && is_string($params[0]) && trim($params[0]) != '') {
                $query->whereRaw($params[0]);
            }
        }

        if (isset($params['order'])) {
            $orders = is_array($params['order']) ? $params['order'] : [$params['order']];
            foreach ($orders as $order) {
                if (preg_match('/([a-zA-Z_0-9]+) (ASC|DESC)/', $order, $regs)) {
                    $query->orderBy($regs[1], $regs[2]);
                } else {
                    $query->orderBy($order);
                }
            }
        }

        if (isset($params['columns'])) {
            $columns = is_array($params['columns']) ? $params['columns'] : explode(',', $params['columns']);
            $query->select($columns);
        } else {
            $query->select('*');
        }

        if (isset($params['limit'])) {
            $query->limit((int) $params['limit']);
        }

        if (isset($params['offset'])) {
            $query->offset((int) $params['offset']);
        }

        $results = $query->get();
        $collectObjects = collect();
        foreach ($results as $result) {
            $obj = new static;
            foreach ($result as $key => $value) {
                $obj->$key = $value;
            }
            $collectObjects->push($obj);
        }

        return $collectObjects;
    }

    public function getArray()
    {
        return $this->toArray();
    }

    public function setCreateAttributes($clase, $data = null)
    {
        if (is_array($data) || is_object($data)) {
            foreach ($data as $prop => $valor) {
                if (property_exists($clase, $prop)) {
                    $clase->$prop = "$valor";
                }
            }
        }
    }

    public function getCount(...$argv)
    {
        $params = get_params_destructures($argv);
        $query = DB::table($this->getTable());

        if (isset($params['conditions'])) {
            $conditions = $params['conditions'];
            if (is_array($conditions)) {
                $conditions = implode(',', $conditions);
            }
            $query->whereRaw($conditions);
        }
        // Si se envía una columna usarla, en otro caso usar '*'
        $item = isset($params[0]) ? trim($params[0]) : '';

        // Evitar que Laravel cite `count(*)` como columna, usar selectRaw o count()
        if ($item === '' || $item === '*') {
            // Cuenta simple
            return (int) $query->count();
        }

        // Para expresiones como DISTINCT o columnas específicas
        $query->selectRaw("count($item) as num");

        return (int) ($query->value('num') ?? 0);
    }
}
