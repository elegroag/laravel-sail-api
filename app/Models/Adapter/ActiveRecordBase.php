<?php

namespace App\Models\Adapter;

use Illuminate\Support\Facades\DB;

class ActiveRecordBase
{

    public function find(string $table, string $where = "", string $fields = "*", $orderBy = "1")
    {
        $query = DB::table($table)->selectRaw($fields);
        if ($where != "") {
            $query = $query->whereRaw($where);
        }
        if ($orderBy != "") {
            $query = $query->orderByRaw($orderBy);
        }
        return $query->get();
    }

    public function fetchAll(string $sqlQuery)
    {
        return DB::select($sqlQuery);
    }

    public function inQueryAssoc(string $sqlQuery)
    {
        $results = DB::select($sqlQuery);
        return json_decode(json_encode($results), true);
    }

    public function inQueryNum(string $sqlQuery)
    {
        $results = DB::select($sqlQuery);
        return json_decode(json_encode($results), false);
    }

    public function fetchOne(string $sqlQuery)
    {
        $results = DB::select($sqlQuery);
        if (count($results) > 0) {
            return $results[0];
        } else {
            return null;
        }
    }

    public function insert(string $table, array $values, array $fields, bool|null $automaticQuotes = false)
    {
        $fields = "(" . implode(",", $fields) . ")";
        $values = implode(",", $values);
        if ($automaticQuotes) {
            $values = "'" . str_replace(",", "','", $values) . "'";
        }
        $sqlQuery = "INSERT INTO " . $table . " " . $fields . " VALUES (" . $values . ")";
        return DB::insert($sqlQuery);
    }

    public function update(string $table, array $fieldsArray, array $valuesArray, string|null $whereCondition = null, bool|null $automaticQuotes = false)
    {
        $setClause = "";
        for ($i = 0; $i < count($fieldsArray); $i++) {
            if ($automaticQuotes) {
                $setClause .= $fieldsArray[$i] . "='" . trim($valuesArray[$i]) . "'";
            } else {
                $setClause .= $fieldsArray[$i] . "=" . trim($valuesArray[$i]);
            }
            if ($i < count($fieldsArray) - 1) {
                $setClause .= ", ";
            }
        }
        $sqlQuery = "UPDATE " . $table . " SET " . $setClause;
        if ($whereCondition != null) {
            $sqlQuery .= " WHERE " . $whereCondition;
        }
        return DB::update($sqlQuery);
    }

    public function delete(string $table, string $whereCondition = '')
    {
        $sqlQuery = "DELETE FROM " . $table;
        if ($whereCondition != '') {
            $sqlQuery .= " WHERE " . $whereCondition;
        }
        return DB::delete($sqlQuery);
    }

    public function begin()
    {
        DB::beginTransaction();
    }

    public function commit()
    {
        DB::commit();
    }

    public function rollback()
    {
        DB::rollBack();
    }

    public function numRows(string $sqlQuery)
    {
        $results = DB::select($sqlQuery);
        return count($results);
    }

    public function lastInsertId()
    {
        return DB::getPdo()->lastInsertId();
    }

    public function fetchArray($sqlQuery)
    {
        $results = DB::select($sqlQuery);
        return json_decode(json_encode($results), true);
    }

    public function affectedRows()
    {
        return DB::affectingStatement('SELECT ROW_COUNT() AS affected_rows');
    }
}
