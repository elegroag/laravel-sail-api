<?php

if (! function_exists('calemp_array')) {
    /**
     * @return array
     */
    function calemp_array()
    {
        return [
            'E' => 'EMPRESA',
            'I' => 'INDEPENDIENTE',
            'P' => 'PENSIONADO',
            'F' => 'FACULTATIVO',
            'D' => 'DESEMPLEADO',
        ];
    }
}

if (! function_exists('coddoc_repleg_array')) {
    /**
     * @return array
     */
    function coddoc_repleg_array()
    {
        return [
            1 => 'CC',
            10 => 'TMF',
            11 => 'CD',
            12 => 'ISE',
            13 => 'V',
            14 => 'PT',
            2 => 'TI',
            3 => 'NI',
            4 => 'CE',
            5 => 'NU',
            6 => 'PA',
            7 => 'RC',
            8 => 'PEP',
            9 => 'CB',
        ];
    }
}

if (! function_exists('tipper_array')) {
    /**
     * @return array
     */
    function tipper_array()
    {
        return [
            'N' => 'NATURAL',
            'J' => 'JURIDICA',
        ];
    }
}

if (! function_exists('calemp_detalle_value')) {
    /**
     * @param  string  $calemp
     * @return string|null
     */
    function calemp_detalle_value($calemp)
    {
        switch ($calemp) {
            case 'E':
                return 'EMPRESA';
                break;
            case 'I':
                return 'INDEPENDIENTE';
                break;
            case 'P':
                return 'PENSIONADO';
                break;
            case 'F':
                return 'FACULTATIVO';
                break;
            case 'D':
                return 'DESEMPLEADO';
                break;
            default:
                return null;
                break;
        }
    }
}

if (! function_exists('calemp_use_tipo_value')) {
    /**
     * @param  string  $detalle
     * @return string|null
     */
    function calemp_use_tipo_value($detalle)
    {
        switch (strtolower($detalle)) {
            case 'empresa':
                return 'E';
                break;
            case 'independiente':
                return 'I';
                break;
            case 'pensionado':
                return 'O';
                break;
            case 'facultativo':
                return 'F';
                break;
            case 'desempleado':
                return 'D';
                break;
            default:
                return null;
                break;
        }
    }
}

if (! function_exists('estado_detalle_value')) {
    /**
     * @param  string  $estado
     * @return string
     */
    function estado_detalle_value($estado)
    {
        $return = '';
        if ($estado == 'T') {
            $return = 'TEMPORAL';
        }
        if ($estado == 'D') {
            $return = 'DEVUELTO';
        }
        if ($estado == 'A') {
            $return = 'APROBADO';
        }
        if ($estado == 'X') {
            $return = 'RECHAZADO';
        }

        return $return;
    }
}

if (! function_exists('get_array_tipos')) {
    function get_array_tipos()
    {
        return [
            'P' => 'Particular',
            'T' => 'Trabajador',
            'E' => 'Empresa aportante',
            'I' => 'Independiente aportante',
            'O' => 'Pensionado',
            'F' => 'Facultativo',
            'S' => 'Servicio domestico',
        ];
    }
}

if (! function_exists('get_tipo_detalle')) {
    function get_tipo_detalle($tipo)
    {
        switch ($tipo) {
            case 'P':
                return 'Particular';
                break;
            case 'T':
                return 'Trabajador';
                break;
            case 'E':
                return 'Empresa aportante';
                break;
            case 'I':
                return 'Independiente aportante';
                break;
            case 'O':
                return 'Pensionado aportante';
                break;
            case 'F':
                return 'Facultativo';
                break;
            case 'S':
                return 'Servicio domestico';
                break;
        }
        return false;
    }
}

if (! function_exists('get_user_estados')) {
    function get_user_estados()
    {
        return [
            'A' => 'ACTIVO',
            'I' => 'INACTIVO',
            'M' => 'MUERTO',
            'B' => 'BLOQUEADO',
        ];
    }
}

if (! function_exists('get_user_estado_detalle')) {
    function get_user_estado_detalle($estado)
    {
        switch ($estado) {
            case 'A':
                return 'ACTIVO';
                break;
            case 'I':
                return 'INACTIVO';
                break;
            case 'M':
                return 'MUERTO';
                break;
            case 'B':
                return 'BLOQUEADO';
                break;
        }
        return false;
    }
}


if (! function_exists('coddoc_repleg_detalle')) {
    /**
     * @return array
     */
    function coddoc_repleg_detalle($coddoc)
    {
        return coddoc_repleg_array()[$coddoc] ?? false;
    }
}
