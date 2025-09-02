<?php

namespace App\Models\Adapter;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Thiagoprz\CompositeKey\HasCompositeKey;

class ModelBase extends Model
{
    use HasFactory;
    use HasCompositeKey;

    public function __construct()
    {
        parent::__construct();
    }

    public function findFirst(...$data)
    {
        $params = $this->getParams($data);
        $query = DB::table($this->getTable());

        if (isset($params['conditions'])) {
            $conditions = $params['conditions'];
            if (is_array($conditions)) {
                $conditions = implode(",", $conditions);
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

    public function getSource()
    {
        return $this->getTable();
    }

    public function findAllBySql(string $sqlQuery)
    {
        $results = DB::select($sqlQuery);
        $collectObjects = collect();
        foreach ($results as $result) {
            $obj = new static();
            foreach ($result as $key => $value) {
                $obj->$key = $value;
            }
            $collectObjects->push($obj);
        }
        return $collectObjects;
    }

    public function findBySql(string $sqlQuery)
    {
        $results = DB::select($sqlQuery);
        if (count($results) > 0) {
            $result = $results[0];
            $obj = new static();
            foreach ($result as $key => $value) {
                $obj->$key = $value;
            }
            return $obj;
        } else {
            return null;
        }
    }

    public function find(...$data)
    {
        $params = $this->getParams($data);
        $query = DB::table($this->getTable());

        if (isset($params['conditions'])) {
            $conditions = $params['conditions'];
            if (is_array($conditions)) {
                $conditions = implode(",", $conditions);
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
            $query->limit((int)$params['limit']);
        }

        if (isset($params['offset'])) {
            $query->offset((int)$params['offset']);
        }

        $results = $query->get();
        $collectObjects = collect();
        foreach ($results as $result) {
            $obj = new static();
            foreach ($result as $key => $value) {
                $obj->$key = $value;
            }
            $collectObjects->push($obj);
        }
        return $collectObjects;
    }

    public function maximum(...$data)
    {
        $params = $this->getParams($data);
        // determine column
        if (isset($params['column'])) {
            $column = $params['column'];
        } elseif (isset($params[0]) && trim($params[0]) !== '') {
            $column = $params[0];
        } else {
            // no column provided
            return null;
        }

        $query = DB::table($this->getTable());

        // conditions (supports array of conditions or raw string)
        if (isset($params['conditions'])) {
            $conditions = $params['conditions'];
            if (is_array($conditions)) {
                $conditions = implode(",", $conditions);
            }
            $query->whereRaw($conditions);
        }


        // grouped maximum -> return collection of objects with group columns + maximum
        if (isset($params['group']) && $params['group']) {
            $groups = is_array($params['group']) ? $params['group'] : explode(',', $params['group']);
            $groups = array_map('trim', $groups);
            $query->groupBy($groups);

            // build select: group cols + MAX(column) AS maximum
            $select = $groups;
            $select[] = DB::raw("MAX({$column}) AS maximum");
            $query->select($select);

            if (isset($params['having']) && $params['having']) {
                $query->havingRaw($params['having']);
            }

            if (isset($params['order'])) {
                $orders = is_array($params['order']) ? $params['order'] : [$params['order']];
                foreach ($orders as $order) {
                    if (preg_match('/([a-zA-Z_0-9]+) (ASC|DESC)/', $order, $regs)) {
                        $query->orderBy($regs[1], $regs[2]);
                    } else {
                        $query->orderByRaw($order);
                    }
                }
            }

            if (isset($params['limit'])) {
                $query->limit((int)$params['limit']);
            }

            if (isset($params['offset'])) {
                $query->offset((int)$params['offset']);
            }

            $results = $query->get();
            $collectObjects = collect();
            foreach ($results as $result) {
                $obj = new static();
                foreach ($result as $key => $value) {
                    $obj->$key = $value;
                }
                $collectObjects->push($obj);
            }
            return $collectObjects;
        }

        // simple maximum (no group) -> return scalar
        // order/limit/offset are ignored for a scalar aggregate
        $maximum = $query->max(DB::raw($column));
        return $maximum;
    }

    public function minimum(...$data)
    {
        $params = $this->getParams($data);
        // determine column
        if (isset($params['column'])) {
            $column = $params['column'];
        } elseif (isset($params[0]) && trim($params[0]) !== '') {
            $column = $params[0];
        } else {
            // no column provided
            return null;
        }

        $query = DB::table($this->getTable());

        // conditions (supports array of conditions or raw string)
        if (isset($params['conditions'])) {
            $conditions = $params['conditions'];
            if (is_array($conditions)) {
                $conditions = implode(",", $conditions);
            }
            $query->whereRaw($conditions);
        }


        // grouped minimum -> return collection of objects with group columns + minimum
        if (isset($params['group']) && $params['group']) {
            $groups = is_array($params['group']) ? $params['group'] : explode(',', $params['group']);
            $groups = array_map('trim', $groups);
            $query->groupBy($groups);

            // build select: group cols + MIN(column) AS minimum
            $select = $groups;
            $select[] = DB::raw("MIN({$column}) AS minimum");
            $query->select($select);

            if (isset($params['having']) && $params['having']) {
                $query->havingRaw($params['having']);
            }

            if (isset($params['order'])) {
                $orders = is_array($params['order']) ? $params['order'] : [$params['order']];
                foreach ($orders as $order) {
                    if (preg_match('/([a-zA-Z_0-9]+) (ASC|DESC)/', $order, $regs)) {
                        $query->orderBy($regs[1], $regs[2]);
                    } else {
                        $query->orderByRaw($order);
                    }
                }
            }

            if (isset($params['limit'])) {
                $query->limit((int)$params['limit']);
            }

            if (isset($params['offset'])) {
                $query->offset((int)$params['offset']);
            }

            $results = $query->get();
            $collectObjects = collect();
            foreach ($results as $result) {
                $obj = new static();
                foreach ($result as $key => $value) {
                    $obj->$key = $value;
                }
                $collectObjects->push($obj);
            }
            return $collectObjects;
        }

        // simple minimum (no group) -> return scalar
        $minimum = $query->min(DB::raw($column));
        return $minimum;
    }

    private function getParams($data)
    {
        $params = [];
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $item) {
                if (stristr($item, ':') === FALSE) {
                    $params[0] = $item;
                    continue;
                }
                $name = substr($item, 0, strpos($item, ':'));
                $params[$name] = substr($item, strpos($item, ':') + 1);
            }
        }
        return $params;
    }

    public function updateAll(...$data)
    {
        $params = $this->getParams($data);
        $query = DB::table($this->getTable());

        if (isset($params['conditions'])) {
            $conditions = $params['conditions'];
            if (is_array($conditions)) {
                $conditions = implode(",", $conditions);
            }
            $query->whereRaw($conditions);
        }
        // Parse set values. Accept formats:
        // - set:col1=val1,col2=val2
        // - set:["col1=val1","col2=val2"]
        $updateData = [];
        if (isset($params['set'])) {
            $sets = $params['set'];
            $setItems = is_array($sets) ? $sets : explode(',', $sets);
            foreach ($setItems as $item) {
                if (!is_string($item)) continue;
                if (strpos($item, '=') === false) continue;
                [$col, $val] = array_map('trim', explode('=', $item, 2));
                // normalize value
                if (strcasecmp($val, 'NULL') === 0) {
                    $value = null;
                } elseif (is_numeric($val)) {
                    // numeric types
                    $value = (strpos($val, '.') !== false) ? (float)$val : (int)$val;
                } else {
                    // strip surrounding single/double quotes if present
                    if ((str_starts_with($val, "'") && str_ends_with($val, "'")) || (str_starts_with($val, '"') && str_ends_with($val, '"'))) {
                        $value = substr($val, 1, -1);
                    } else {
                        $value = $val;
                    }
                }
                $updateData[$col] = $value;
            }
        } elseif (isset($params[0]) && is_string($params[0]) && strpos($params[0], '=') !== false) {
            // fallback: first param contains set clause
            $setItems = explode(',', $params[0]);
            foreach ($setItems as $item) {
                if (strpos($item, '=') === false) continue;
                [$col, $val] = array_map('trim', explode('=', $item, 2));
                if (strcasecmp($val, 'NULL') === 0) {
                    $value = null;
                } elseif (is_numeric($val)) {
                    $value = (strpos($val, '.') !== false) ? (float)$val : (int)$val;
                } else {
                    if ((str_starts_with($val, "'") && str_ends_with($val, "'")) || (str_starts_with($val, '"') && str_ends_with($val, '"'))) {
                        $value = substr($val, 1, -1);
                    } else {
                        $value = $val;
                    }
                }
                $updateData[$col] = $value;
            }
        }

        if (empty($updateData)) {
            // nothing to update
            return 0;
        }

        // execute update with bindings
        $affected = $query->update($updateData);
        return $affected;
    }

    public function deleteAll(...$data)
    {
        $params = $this->getParams($data);
        $query = DB::table($this->getTable());

        if (isset($params['conditions'])) {
            $conditions = $params['conditions'];
            if (is_array($conditions)) {
                $conditions = implode(",", $conditions);
            }
            $query->whereRaw($conditions);
        } else {
            if (isset($params[0]) && is_string($params[0]) && trim($params[0]) != '') {
                $query->whereRaw($params[0]);
            } else {
                return 0; // no conditions -> prevent deleting all records
            }
        }
        return $query->delete();
    }

    public function getArray(){
        return $this->toArray();
    }

    public function setCreateAttributes($clase, $data = null)
    {
        if(is_array($data) || is_object($data))
        {
            foreach ($data as $prop => $valor)
            {
                if(property_exists($clase, $prop)){
                    $clase->$prop = "$valor";
                }
            }
        }
    }
}