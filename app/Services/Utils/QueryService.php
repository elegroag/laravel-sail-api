<?php

namespace App\Services\Utils;

use Illuminate\Http\Request;

class QueryService
{
    public function converserialize($str, $indice)
    {
        $data = [];
        $strArray = preg_split('/&/', $str);
        $i = 0;
        foreach ($strArray as $item) {
            $array = preg_split('/=/', $item);
            if (count($array) < 2) {
                continue;
            }
            $data[$i]["$indice"] = trim($array[1]);
            $i++;
        }

        return $data;
    }

    public function converQuery(Request $request)
    {
        $campo = $this->converserialize($request->input('campo'), 'mcampo');
        $condi = $this->converserialize($request->input('condi'), 'mcondi');
        $value = $this->converserialize($request->input('value'), 'mvalue');
        $query = [];
        for ($i = 0; $i < count($campo); $i++) {
            $mcampo = $campo[$i]['mcampo'];
            $mcondi = $condi[$i]['mcondi'];
            $mvalue = $value[$i]['mvalue'];
            switch ($mcondi) {
                case 'como':
                    $mcondi = 'like';
                    break;
                case 'igual':
                    $mcondi = '=';
                    break;
                case 'mayor':
                    $mcondi = '>';
                    break;
                case 'menor':
                    $mcondi = '<';
                    break;
                case 'mayorigual':
                    $mcondi = '>=';
                    break;
                case 'menorigual':
                    $mcondi = '<=';
                    break;
                case 'diferente':
                    $mcondi = '<>';
                    break;
            }
            if ($mcondi == 'like') {
                $query[] = "$mcampo $mcondi '%$mvalue%'";
            } else {
                $query[] = "$mcampo $mcondi '$mvalue'";
            }
        }
        $query = count($query) != 0 ? implode(' AND ', $query) : ' 1=1 ';

        return $query;
    }
}
