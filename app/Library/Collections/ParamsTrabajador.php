<?php

namespace App\Library\Collections;

class ParamsTrabajador
{
    public static $zonas;

    public static $ciudades;

    public static $tipos_documentos;

    public static $ocupaciones;

    public static $sexos;

    public static $estado_civil;

    public static $cabeza_hogar;

    public static $capacidad_trabajar;

    public static $tipo_discapacidad;

    public static $nivel_educativo;

    public static $rural;

    public static $tipo_contrato;

    public static $vivienda;

    public static $sidicalizado;

    public static $tipo_afiliado;

    public static $pertenencia_etnicas;

    public static $tipo_pago;

    public static $vulnerabilidades;

    public static $orientacion_sexual;

    public static $labora_otra_empresa;

    public static $datos_captura;

    public static $resguardos;

    public static $pueblos_indigenas;

    public static $bancos;

    public static $empleador;

    public static $vendedor;

    public static $codigo_cuenta;

    public static $tipo_cuenta;

    public static $giro;

    public static $codigo_giro;

    public function setDatosCaptura($datos_captura)
    {
        self::$datos_captura = $datos_captura;
    }

    public static function getCodigoGiro()
    {
        foreach (self::$datos_captura['codigo_giro'] as $data) {
            self::$codigo_giro[$data['codgir']] = $data['detalle'];
        }

        return self::$codigo_giro;
    }

    public static function getTipoCuenta()
    {
        foreach (self::$datos_captura['tipo_cuenta'] as $data) {
            self::$tipo_cuenta[$data['estado']] = $data['detalle'];
        }

        return self::$tipo_cuenta;
    }

    public static function getGiro()
    {
        foreach (self::$datos_captura['giro'] as $data) {
            self::$giro[$data['estado']] = $data['detalle'];
        }

        return self::$giro;
    }

    public static function getEmpleador()
    {
        foreach (self::$datos_captura['empleador'] as $data) {
            self::$empleador[$data['estado']] = $data['detalle'];
        }

        return self::$empleador;
    }

    public static function getCodigoCuenta()
    {
        foreach (self::$datos_captura['codigo_cuenta'] as $data) {
            self::$codigo_cuenta[$data['codcue']] = $data['detalle'];
        }

        return self::$codigo_cuenta;
    }

    public static function getVendedor()
    {
        foreach (self::$datos_captura['vendedor'] as $data) {
            self::$vendedor[$data['estado']] = $data['detalle'];
        }

        return self::$vendedor;
    }

    public static function getBancos()
    {
        foreach (self::$datos_captura['bancos'] as $data) {
            self::$bancos[$data['codban']] = $data['detalle'];
        }

        return self::$bancos;
    }

    public static function getResguardos()
    {
        foreach (self::$datos_captura['resguardos'] as $data) {
            self::$resguardos[$data['id']] = $data['detalle'];
        }

        return self::$resguardos;
    }

    public static function getPueblosIndigenas()
    {
        foreach (self::$datos_captura['pueblos_indigenas'] as $data) {
            self::$pueblos_indigenas[$data['id']] = $data['detalle'];
        }

        return self::$pueblos_indigenas;
    }

    public static function getTipoPago()
    {
        foreach (self::$datos_captura['tipo_pago'] as $data) {
            if ($data['estado'] == 'C') {
                continue;
            }
            if ($data['estado'] == 'T') {
                $data['detalle'] = 'PENDIENTE FORMA DE PAGO';
            }
            self::$tipo_pago[$data['estado']] = $data['detalle'];
        }

        return self::$tipo_pago;
    }

    public static function getOrientacionSexual()
    {
        foreach (self::$datos_captura['orientacion_sexuales'] as $data) {
            self::$orientacion_sexual[$data['codori']] = $data['detalle'];
        }

        return self::$orientacion_sexual;
    }

    public static function getVulnerabilidades()
    {
        foreach (self::$datos_captura['vulnerabilidades'] as $data) {
            self::$vulnerabilidades[$data['codigo']] = $data['nombre'];
        }

        return self::$vulnerabilidades;
    }

    public static function getPertenenciaEtnicas()
    {
        foreach (self::$datos_captura['pertenencia_etnicas'] as $data) {
            self::$pertenencia_etnicas[$data['codigo']] = $data['nombre'];
        }

        return self::$pertenencia_etnicas;
    }

    public static function getCapacidadTrabajar()
    {
        foreach (self::$datos_captura['captra'] as $data) {
            self::$capacidad_trabajar[$data['captra']] = $data['detalle'];
        }

        return self::$capacidad_trabajar;
    }

    public static function getTipoDiscapacidad()
    {
        foreach (self::$datos_captura['discapacidades'] as $data) {
            self::$tipo_discapacidad[$data['tipdis']] = $data['detalle'];
        }

        return self::$tipo_discapacidad;
    }

    public static function getNivelEducativo()
    {
        foreach (self::$datos_captura['nivel_educativos'] as $data) {
            self::$nivel_educativo[$data['nivedu']] = $data['detalle'];
        }

        return self::$nivel_educativo;
    }

    public static function getRural()
    {
        foreach (self::$datos_captura['rural'] as $data) {
            self::$rural[$data['rural']] = $data['detalle'];
        }

        return self::$rural;
    }

    public static function getTipoContrato()
    {
        foreach (self::$datos_captura['tipcon'] as $data) {
            self::$tipo_contrato[$data['tipcon']] = $data['detalle'];
        }

        return self::$tipo_contrato;
    }

    public static function getSindicalizado()
    {
        foreach (self::$datos_captura['sidicalizado'] as $data) {
            self::$sidicalizado[$data['trasin']] = $data['detalle'];
        }

        return self::$sidicalizado;
    }

    public static function getVivienda()
    {
        foreach (self::$datos_captura['vivienda'] as $data) {
            self::$vivienda[$data['vivienda']] = $data['detalle'];
        }

        return self::$vivienda;
    }

    public static function getTipoAfiliado()
    {
        foreach (self::$datos_captura['tipo_cotizantes'] as $data) {
            if (is_null($data['circular'])) {
                continue;
            }
            self::$tipo_afiliado[$data['tipcot']] = $data['detalle'];
        }

        return self::$tipo_afiliado;
    }

    public static function getCabezaHogar()
    {
        foreach (self::$datos_captura['cabhog'] as $data) {
            self::$cabeza_hogar[$data['cabhog']] = $data['detalle'];
        }

        return self::$cabeza_hogar;
    }

    public static function getEstadoCivil()
    {
        foreach (self::$datos_captura['estado_civiles'] as $data) {
            self::$estado_civil[$data['estciv']] = $data['detest'];
        }

        return self::$estado_civil;
    }

    public static function getSexos()
    {
        foreach (self::$datos_captura['sexos'] as $data) {
            self::$sexos[$data['codsex']] = $data['detsex'];
        }

        return self::$sexos;
    }

    public static function getZonas()
    {
        foreach (self::$datos_captura['zonas'] as $data) {
            if ($data['codzon'] < 19001 && $data['codzon'] >= 18001) {
                self::$zonas[$data['codzon']] = $data['codzon'].' - '.$data['detzon'];
            }
        }

        return self::$zonas;
    }

    public static function getCiudades()
    {
        foreach (self::$datos_captura['ciudades'] as $data) {
            self::$ciudades[$data['codciu']] = $data['codciu'].' - '.$data['detciu'];
        }

        return self::$ciudades;
    }

    public static function getTiposDocumentos()
    {
        foreach (self::$datos_captura['tipo_documentos'] as $data) {
            self::$tipos_documentos[$data['coddoc']] = $data['codrua'];
        }

        return self::$tipos_documentos;
    }

    public static function getOcupaciones()
    {
        foreach (self::$datos_captura['ocupaciones'] as $data) {
            self::$ocupaciones["{$data['codocu']}"] = $data['codocu'].' '.$data['detalle'];
        }

        return self::$ocupaciones;
    }

    public static function getLaboraOtraEmpresa()
    {
        foreach (self::$datos_captura['labora_otra_empresa'] as $data) {
            self::$labora_otra_empresa[$data['estado']] = $data['detalle'];
        }

        return self::$labora_otra_empresa;
    }

    public static function getOcupacionConyuges()
    {
        return [
            '00' => 'NINGUNA',
            '01' => 'EMPLEADO',
            '02' => 'ESTUDIANTE',
            '03' => 'PENSIONADO',
            '04' => 'INDEPENDIENTE',
            '05' => 'AMA DE CASA',
            '06' => 'OTROS',
        ];
    }
}
