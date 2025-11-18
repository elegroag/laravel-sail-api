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
            '1' => 'Soltero',
            '2' => 'Casado',
            '3' => 'Viudo',
            '4' => 'Union libre',
            '5' => 'Separado',
            '6' => 'Divorciado',
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
            '00' => 'Ninguna',
            '01' => 'Discapacidad Fisica',
            '02' => 'Discapacidad Visual',
            '03' => 'Discapacidad Auditiva',
            '04' => 'Discapacidad Intelectual',
            '05' => 'Discapacidad Psicosocial (Mental)',
            '06' => 'Sordoceguera',
            '07' => 'Discapacidad Multiple',
        ];
    }
}


if (! function_exists('nivel_educativo_array')) {

    function nivel_educativo_array()
    {
        return [
            '1' => 'Preescolar',
            '10' => 'Tecnico/Tecnologo',
            '11' => 'Universitario',
            '12' => 'Posgrado/Maestria',
            '13' => 'Ninguno',
            '14' => 'Informacion no disponible',
            '2' => 'Basica',
            '3' => 'Secundaria',
            '4' => 'Media',
            '6' => 'Basica adulto',
            '7' => 'Secundaria adulto',
            '8' => 'Media adulto',
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
            'F' => 'Fijo',
            'I' => 'Indefinido',
        ];
    }
}


if (! function_exists('es_sindicalizado')) {

    function es_sindicalizado()
    {
        return [
            'S' => 'Sí',
            'N' => 'No',
        ];
    }
}


if (! function_exists('vivienda_array')) {

    function vivienda_array()
    {
        return [
            'N' => 'No',
            'F' => 'Familiar',
            'P' => 'Propia',
            'A' => 'Arrendada',
            'H' => 'Hipoteca',
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
            'T' => 'Pendiente forma de pago',
            'A' => 'Abono cuenta personal',
            'D' => 'Cuenta Daviplata',
        ];
    }
}


if (! function_exists('tipo_cuenta_array')) {

    function tipo_cuenta_array()
    {
        return [
            'A' => 'Ahorros',
            'C' => 'Corriente',
        ];
    }
}

if (! function_exists('tipo_jornada_array')) {

    function tipo_jornada_array()
    {
        return [
            'C' => 'Completa',
            'M' => 'Media',
            'P' => 'Parcial',
        ];
    }
}


if (! function_exists('comision_array')) {

    function comision_array()
    {
        return [
            'S' => 'Sí',
            'N' => 'No',
        ];
    }
}


if (! function_exists('labora_otra_empresa_array')) {

    function labora_otra_empresa_array()
    {
        return [
            'S' => 'Sí',
            'N' => 'No',
        ];
    }
}


if (! function_exists('parentesco_array')) {

    function parentesco_array()
    {
        return [
            "1" => "Hijo",
            "2" => "Hermano",
            "3" => "Padre",
            "4" => "Custodia",
            "5" => "Cuidador"
        ];
    }
}


if (! function_exists('huerfano_array')) {

    function huerfano_array()
    {
        return [
            "0" => "No aplica",
            "1" => "Huerfano padre",
            "2" => "Huerfano madre"
        ];
    }
}

if (! function_exists('tipo_hijo_array')) {

    function tipo_hijo_array()
    {
        return [
            "0" => "No aplica",
            "1" => "Hijo natural",
            "2" => "Hijastro",
            "3" => 'Custodia nieto',
            "4" => 'Custodia sobrino',
            "5" => 'Custodia otro',
            "6" => 'Cuidador discapacitado',
        ];
    }
}

if (! function_exists('calendario_array')) {

    function calendario_array()
    {
        return [
            "A" => "A",
            "B" => "B",
            "N" => "No aplica"
        ];
    }
}


if (! function_exists('convive_array')) {

    function convive_array()
    {
        return [
            '1' => 'Conyuge',
            '2' => 'Trabajador',
            '3' => 'No aplica',
            '4' => 'Otras personas',
        ];
    }
}
