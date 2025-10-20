<?php

if (! function_exists('solicitud_estados_array')) {
    /**
     * @return array
     */
    function solicitud_estados_array()
    {
        return [
            'A' => 'APROBO',
            'X' => 'RECHAZO',
            'P' => 'PENDIENTE',
            'D' => 'DEVOLVIO',
        ];
    }
}


if (! function_exists('solicitud_estado_detalle')) {
    /**
     * @return string
     */
    function solicitud_estado_detalle($estado)
    {
        switch ($estado) {
            case 'T':
                return 'TEMPORAL';
                break;
            case 'D':
                return 'DEVUELTO';
                break;
            case 'A':
                return 'APROBADO';
                break;
            case 'X':
                return 'RECHAZADO';
                break;
            case 'P':
                return 'PENDIENTE';
                break;
            case 'C':
                return 'CANCELAR';
                break;
            default:
                return 'SIN ESTADO';
                break;
        }
    }
}


if (! function_exists('solicitud_tipo_actualizacion_array')) {
    /**
     * @return array
     */
    function solicitud_tipo_actualizacion_array()
    {
        return [
            'E' => 'Empresa',
            'I' => 'Independiente',
            'T' => 'Trabajador',
            'P' => 'Pensionado',
            'B' => 'Beneficiario',
            'C' => 'Conyuge',
        ];
    }
}

if (! function_exists('solicitud_tipo_actualizacion_detalle')) {
    /**
     * @return string
     */
    function solicitud_tipo_actualizacion_detalle($tipo)
    {
        switch ($tipo) {
            case 'E':
                return 'Empresa';
                break;
            case 'I':
                return 'Independiente';
                break;
            case 'T':
                return 'Trabajador';
                break;
            case 'P':
                return 'Pensionado';
                break;
            case 'B':
                return 'Beneficiario';
                break;
            case 'C':
                return 'Conyuge';
                break;
            default:
                return 'SIN TIPO';
                break;
        }
    }
}
