<?php

if (! function_exists('calemp_array')) {
    /**
     * @return array
     */
    function calemp_array()
    {
        return [
            'E' => 'Empresa',
            'I' => 'Independiente',
            'P' => 'Pensionado',
            'F' => 'Facultativo',
            'D' => 'Desempleado',
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
            'N' => 'Natural',
            'J' => 'Juridica',
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
                return 'Empresa';
                break;
            case 'I':
                return 'Independiente';
                break;
            case 'P':
                return 'Pensionado';
                break;
            case 'F':
                return 'Facultativo';
                break;
            case 'D':
                return 'Desempleado';
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
            $return = 'Temporal';
        }
        if ($estado == 'D') {
            $return = 'Devuelto';
        }
        if ($estado == 'A') {
            $return = 'Aprobado';
        }
        if ($estado == 'X') {
            $return = 'Rechazado';
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
            'A' => 'Activo',
            'I' => 'Inactivo',
            'M' => 'Muerto',
            'B' => 'Bloqueado',
        ];
    }
}

if (! function_exists('get_user_estado_detalle')) {
    function get_user_estado_detalle($estado)
    {
        switch ($estado) {
            case 'A':
                return 'Activo';
                break;
            case 'I':
                return 'Inactivo';
                break;
            case 'M':
                return 'Muerto';
                break;
            case 'B':
                return 'Bloqueado';
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


if (! function_exists('tipo_document_repleg_detalle')) {
    /**
     * @return array
     */
    function tipo_document_repleg_detalle()
    {
        return [
            'CC' => 'Cedula de ciudadania',
            'TMF' => 'Tarjeta de movilidad fronteriza',
            'CD' => 'Carnet diplomático',
            'ISE' => 'Identificación dada por la secretaria de educación',
            'V' => 'Visa',
            'PT' => 'Pasaporte',
            'TI' => 'Tarjeta de identidad',
            'NI' => 'NIT',
            'CE' => 'Cédula de extranjería',
            'NU' => 'NUIP',
            'PA' => 'Pasaporte de extranjería',
            'RC' => 'Registro civil',
            'PEP' => 'Permiso especial de permanencia',
            'CB' => 'Certificado cabildo',
            '' => 'No definido'
        ];
    }
}

if (! function_exists('tipsal_array')) {

    function tipsal_array()
    {
        return [
            'F' => 'FIJO',
            'V' => 'VARIABLE',
            'I' => 'INTEGRAL',
        ];
    }
}

if (! function_exists('autoriza_array')) {

    function autoriza_array()
    {
        return [
            'S' => 'SI',
            'N' => 'NO',
        ];
    }
}
