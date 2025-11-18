<?php

if (! function_exists('solicitud_estados_array')) {
    /**
     * @return array
     */
    function solicitud_estados_array()
    {
        return [
            'A' => 'Aprobado',
            'X' => 'Rechazado',
            'P' => 'Pendiente',
            'D' => 'Devuelto',
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
                return 'Temporal';
                break;
            case 'D':
                return 'Devuelto';
                break;
            case 'A':
                return 'Aprobado';
                break;
            case 'X':
                return 'Rechazado';
                break;
            case 'P':
                return 'Pendiente';
                break;
            case 'C':
                return 'Cancelar';
                break;
            default:
                return 'Sin estado';
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
                return 'Sin tipo';
                break;
        }
    }
}
