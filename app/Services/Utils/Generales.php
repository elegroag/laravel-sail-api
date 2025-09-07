<?php

namespace App\Services\Utils;

use App\Models\Gener18;
use Carbon\Carbon;

class Generales
{

    public static function GeneraClave($pass = null)
    {
        $pass = (is_null($pass)) ? self::GeneraPass() : $pass;
        $mclave = '';
        for ($i = 0; $i < strlen($pass); $i++) {
            if ($i % 2 != 0) {
                $x = 6;
            } else {
                $x = -4;
            }
            $mclave .= chr(ord(substr($pass, $i, 1)) + $x + 5);
        }
        return array(md5($mclave), $pass);
    }

    public static function GeneraPass()
    {
        $pass = "";
        $seed = str_split('abcdefghijklmnopqrstuvwxyz1234567890');
        shuffle($seed);
        foreach (array_rand($seed, 5) as $k) $pass .= $seed[$k];
        return $pass;
    }

    public static function TipoDocumento($afiliado)
    {
        $mtipoDocumentos = new Gener18();
        $coddoc_detalle = "";
        foreach ($mtipoDocumentos->getFind() as $entity) {
            if ($entity->getCoddoc() == $afiliado->getCoddoc()) {
                $coddoc_detalle = $entity->getDetdoc();
                break;
            }
        }
        return $coddoc_detalle;
    }

    public static function ValidaCaptcha($code)
    {
        if (is_null($code) || $code == '') {
            return false;
        }
        /* $securimage = new Securimage();
        if ($securimage->check($code) == true) {
            return true;
        } else {
            return false;
        } */
    }

    public static function localTipoDocumento($coddoc)
    {
        $data = self::getTipoDocumentos();
        return $data[$coddoc];
    }

    public static function getTipoDocumentos()
    {
        return array(
            1 => "CEDULA CIUDADANIA",
            2 => "TARJETA IDENTIDAD",
            3 => "NIT",
            4 => "CEDULA EXTRANJERIA",
            5 => "NUIP",
            6 => "PASAPORTE",
            7 => "REGISTRO CIVIL",
            8 => "PERMISO ESPECIAL DE PERMANENCIA",
            9 => "CERTIFICADO CABILDO",
            10 => "TRAJETA DE MOVILIDAD FRONTERIZA",
            11 => "CARNE DIPLOMATICO",
            12 => "IDENTIFICACION DADA POR LA SECRETARIA DE EDUCACION",
            13 => "VISA",
            14 => "PERMISO PROTECCION TEMPORAL"
        );
    }
}
