<?php

if (! function_exists('sexos_array')) {

    function sexos_array()
    {
        return [
            'M' => 'Masculino',
            'F' => 'Femenino',
            'I' => 'Indefinido',
        ];
    }
}

if (! function_exists('estados_civiles_array')) {

    function estados_civiles_array()
    {
        return [
            '1' => 'SOLTERO',
            '2' => 'CASADO',
            '3' => 'VIUDO',
            '4' => 'UNION LIBRE',
            '5' => 'SEPARADO',
            '6' => 'DIVORCIADO',
        ];
    }
}

if (! function_exists('cabeza_hogar')) {

    function cabeza_hogar()
    {
        return [
            'S' => 'Sí',
            'N' => 'No',
        ];
    }
}

if (! function_exists('capacidad_trabajar')) {

    function capacidad_trabajar()
    {
        return [
            'S' => 'Sí',
            'N' => 'No',
        ];
    }
}


if (! function_exists('tipo_discapacidad_array')) {

    function tipo_discapacidad_array()
    {
        return [
            '00' => 'NINGUNA',
            '01' => 'DISCAPACIDAD FISICA',
            '02' => 'DISCAPACIDAD VISUAL',
            '03' => 'DISCAPACIDAD AUDITIVA',
            '04' => 'DISCAPACIDAD INTELECTUAL',
            '05' => 'DISCAPACIDAD PSICOSOCIAL (MENTAL)',
            '06' => 'SORDOCEGUERA',
            '07' => 'DISCAPACIDAD MULTIPLE',
        ];
    }
}


if (! function_exists('nivel_educativo_array')) {

    function nivel_educativo_array()
    {
        return [
            '1' => 'PREESCOLAR',
            '10' => 'TECNICO/TEGNOLOGO',
            '11' => 'UNIVERSITARIO',
            '12' => 'POSGRADO/MAESTRÍA',
            '13' => 'NINGUNO',
            '14' => 'INFORMACION NO DISPONIBLE',
            '2' => 'BASICA',
            '3' => 'SECUNDARIA',
            '4' => 'MEDIA',
            '6' => 'BÁSICA ADULTOS',
            '7' => 'SECUNDARIA ADULTO',
            '8' => 'MEDIA ADULTO',
        ];
    }
}


if (! function_exists('es_rural')) {

    function es_rural()
    {
        return [
            'S' => 'Sí',
            'N' => 'No',
        ];
    }
}


if (! function_exists('tipo_contrato')) {

    function tipo_contrato()
    {
        return [
            'F' => 'FIJO',
            'I' => 'INDEFINIDO',
        ];
    }
}


if (! function_exists('es_sindicalizado')) {

    function es_sindicalizado()
    {
        return [
            'S' => 'SI',
            'N' => 'NO',
        ];
    }
}


if (! function_exists('vivienda_array')) {

    function vivienda_array()
    {
        return [
            'N' => 'NO',
            'F' => 'FAMILIAR',
            'P' => 'PROPIA',
            'A' => 'ARRENDADA',
            'H' => 'HIPOTECA',
        ];
    }
}

if (! function_exists('orientacion_sexual_array')) {

    function orientacion_sexual_array()
    {
        return [
            '1' => 'Heterosexual',
            '2' => 'Homosexual',
            '3' => 'Bisexual',
            '4' => 'Información no disponible',
        ];
    }
}


if (! function_exists('vulnerabilidades_array')) {

    function vulnerabilidades_array()
    {
        return [
            '1' => 'Desplazado',
            '2' => 'Víctima del conflicto armado (No desplazado)',
            '3' => 'Desmovilizado o reinsertado',
            '4' => 'Hijo (as) de desmovilizados o reisertados',
            '5' => 'Damnificado desastre natural',
            '6' => 'Cabeza de familia',
            '7' => 'Hijo (as) de madres cabeza de familia',
            '8' => 'En condición de discapacidad',
            '9' => 'Población migrante',
            '10' => 'Población zonas frontera (Nacionales)',
            '11' => 'Ejercicio del trabajo sexual',
            '12' => 'No aplica',
            '13' => 'No disponible',
        ];
    }
}


if (! function_exists('pertenencia_etnica_array')) {

    function pertenencia_etnica_array()
    {
        return [
            '1' => 'Afrocolombiano',
            '2' => 'Comunidad negra',
            '3' => 'Indígena',
            '4' => 'Palanquero',
            '5' => 'Raizal del archipiélago de San Andrés, Providencia',
            '6' => 'Room/gitano',
            '7' => 'No se auto reconoce en ninguno de los anteriores',
            '8' => 'No Disponible',
        ];
    }
}


if (! function_exists('tipo_pago_array')) {

    function tipo_pago_array()
    {
        return [
            'T' => 'PENDIENTE FORMA DE PAGO',
            'A' => 'ABONO CUENTA PERSONAL',
            'D' => 'CUENTA DAVIPLATA',
        ];
    }
}


if (! function_exists('tipo_cuenta_array')) {

    function tipo_cuenta_array()
    {
        return [
            'A' => 'AHORROS',
            'C' => 'CORRIENTE',
        ];
    }
}

if (! function_exists('tipo_jornada_array')) {

    function tipo_jornada_array()
    {
        return [
            'C' => 'COMPLETA',
            'M' => 'MEDIA',
            'P' => 'PARCIAL',
        ];
    }
}


if (! function_exists('comision_array')) {

    function comision_array()
    {
        return [
            'S' => 'SI',
            'N' => 'NO',
        ];
    }
}


if (! function_exists('labora_otra_empresa_array')) {

    function labora_otra_empresa_array()
    {
        return [
            'S' => 'SI',
            'N' => 'NO',
        ];
    }
}
