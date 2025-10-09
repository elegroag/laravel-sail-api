<?php

namespace App\Services\Utils;

use App\Models\Gener18;

class Generales
{
    public static function TipoDocumento($afiliado)
    {
        $mtipoDocumentos = new Gener18;
        $coddoc_detalle = '';
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
    }

    public static function localTipoDocumento($coddoc)
    {
        $data = self::getTipoDocumentos();

        return $data[$coddoc];
    }

    public static function getTipoDocumentos()
    {
        return [
            1 => 'CEDULA CIUDADANIA',
            2 => 'TARJETA IDENTIDAD',
            3 => 'NIT',
            4 => 'CEDULA EXTRANJERIA',
            5 => 'NUIP',
            6 => 'PASAPORTE',
            7 => 'REGISTRO CIVIL',
            8 => 'PERMISO ESPECIAL DE PERMANENCIA',
            9 => 'CERTIFICADO CABILDO',
            10 => 'TRAJETA DE MOVILIDAD FRONTERIZA',
            11 => 'CARNE DIPLOMATICO',
            12 => 'IDENTIFICACION DADA POR LA SECRETARIA DE EDUCACION',
            13 => 'VISA',
            14 => 'PERMISO PROTECCION TEMPORAL',
        ];
    }

    public static function GeneraHashByClave($mclave)
    {
        return md5(password_hash_old(strval($mclave)));
    }
}
