<?php

namespace App\Services\Utils;

use App\Models\Mercurio10;
use Carbon\Carbon;

class CalculatorDias
{
    public static function calcular($tipopc, $numero, $fecsol = '')
    {
        $mercurio10 = new Mercurio10;
        $fecsis = $mercurio10->maximum('fecsis', "conditions: tipopc='{$tipopc}' and numero='{$numero}'");
        $mercurio10 = new Mercurio10;
        $eventoSeguimiento = $mercurio10->findFirst("tipopc='{$tipopc}' and numero='{$numero}' and fecsis='{$fecsis}'");
        if ($eventoSeguimiento == false) {
            $fecha_envio = ($fecsol instanceof Carbon) ? $fecsol : Carbon::parse($fecsol);
            $fecha_cerrado = Carbon::now();
        } else {
            if ($fecsol instanceof Carbon) {
                if ($eventoSeguimiento->getEstado() == 'A' || $eventoSeguimiento->getEstado() == 'X') {
                    $fecha_envio = Carbon::now();
                    $fecha_cerrado = $fecha_envio;
                } else {
                    $fecha_envio = ($fecsol instanceof Carbon) ? $fecsol : Carbon::parse($fecsol);
                    $fecha_cerrado = Carbon::now();
                }
            } else {
                if ($eventoSeguimiento->getEstado() == 'A' || $eventoSeguimiento->getEstado() == 'X') {
                    $fecha_envio = Carbon::now();
                    $fecha_cerrado = $fecha_envio;
                } else {
                    $fecha_envio = $eventoSeguimiento->getFecsis();
                    $fecha_cerrado = Carbon::now();
                }
            }
        }
        $dias = $fecha_cerrado->diffInDays($fecha_envio);
        $dias = count(self::getDiasHabiles(
            $fecha_envio->format('Y-m-d'),
            $fecha_cerrado->format('Y-m-d')
        )) - 1;

        return $dias;
    }

    public static function getDiasHabiles($fechainicio, $fechafin, $diasferiados = [])
    {
        $fechainicio = strtotime($fechainicio);
        $fechafin = strtotime($fechafin);
        $diainc = 86400;
        $diashabiles = [];
        $midia = $fechainicio;
        for ($midia; $midia <= $fechafin; $midia += $diainc) {
            if (! in_array(date('N', $midia), [6, 7])) {
                if (! in_array(date('Y-m-d', $midia), $diasferiados)) {
                    array_push($diashabiles, date('Y-m-d', $midia));
                }
            }
        }

        return $diashabiles;
    }
}
