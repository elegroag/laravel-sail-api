<?php
class ParamsBeneficiario
{
    static $zonas;
    static $ciudades;
    static $tipos_documentos;
    static $ocupaciones;
    static $sexos;
    static $estado_civil;
    static $capacidad_trabajar;
    static $tipo_discapacidad;
    static $nivel_educativo;
    static $parentesco;
    static $huerfano;
    static $tipo_hijo;
    static $calendario;
    static $tiene_giro;
    static $pago;
    static $codigo_giro;
    static $datos_captura;
    static $pertenencia_etnicas;
    static $resguardos;
    static $pueblos_indigenas;
    static $tipo_cuenta;
    static $tipo_pago;
    static $bancos;

    public function setDatosCaptura($datos_captura)
    {
        self::$datos_captura = $datos_captura;
    }

    public static function getTipoPago()
    {
        if (is_null(self::$tipo_pago) == true) {
            foreach (self::$datos_captura['tipo_pago'] as $data) self::$tipo_pago[$data['estado']] = $data['detalle'];
        }
        return self::$tipo_pago;
    }

    public static function getBancos()
    {
        foreach (self::$datos_captura['bancos'] as $data) {
            self::$bancos[$data['codban']] = $data['detalle'];
        }
        return self::$bancos;
    }

    public static function getTipoCuenta()
    {
        if (is_null(self::$tipo_cuenta) == true) {
            foreach (self::$datos_captura['tipo_cuenta'] as $data) self::$tipo_cuenta[$data['estado']] = $data['detalle'];
        }
        return self::$tipo_cuenta;
    }

    public static function getPertenenciaEtnicas()
    {
        foreach (self::$datos_captura['pertenencia_etnicas'] as $data) self::$pertenencia_etnicas[$data['codigo']] = $data['nombre'];
        return self::$pertenencia_etnicas;
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

    public static function getParentesco()
    {
        foreach (self::$datos_captura['parent'] as $data) self::$parentesco[$data['estado']] = $data['detalle'];
        return self::$parentesco;
    }

    public static function getHuerfano()
    {
        foreach (self::$datos_captura['huerfano'] as $data) self::$huerfano[$data['estado']] = $data['detalle'];
        return self::$huerfano;
    }

    public static function getTipoHijo()
    {
        foreach (self::$datos_captura['tiphij'] as $data) self::$tipo_hijo[$data['estado']] = $data['detalle'];
        return self::$tipo_hijo;
    }

    public static function getCalendario()
    {
        foreach (self::$datos_captura['calendario'] as $data) self::$calendario[$data['estado']] = $data['detalle'];
        return self::$calendario;
    }

    public static function getTieneGiro()
    {
        foreach (self::$datos_captura['giro'] as $data) self::$tiene_giro[$data['estado']] = $data['detalle'];
        return self::$tiene_giro;
    }

    public static function getPago()
    {
        foreach (self::$datos_captura['pago'] as $data) self::$pago[$data['estado']] = $data['detalle'];
        return self::$pago;
    }

    public static function getCodigoGiro()
    {
        foreach (self::$datos_captura['codigo_giro'] as $data) self::$codigo_giro["{$data['codgir']}"] = $data['detalle'];
        return self::$codigo_giro;
    }

    public static function getCapacidadTrabajar()
    {
        foreach (self::$datos_captura['captra'] as $data) self::$capacidad_trabajar[$data['captra']] = $data['detalle'];
        return self::$capacidad_trabajar;
    }

    public static function getTipoDiscapacidad()
    {
        foreach (self::$datos_captura['discapacidades'] as $data) self::$tipo_discapacidad[$data['tipdis']] = $data['detalle'];
        return self::$tipo_discapacidad;
    }

    public static function getNivelEducativo()
    {
        foreach (self::$datos_captura['nivel_educativos'] as $data) self::$nivel_educativo[$data['nivedu']] = $data['detalle'];
        return self::$nivel_educativo;
    }

    public static function getEstadoCivil()
    {
        foreach (self::$datos_captura['estado_civiles'] as $data) self::$estado_civil[$data['estciv']] = $data['detest'];
        return self::$estado_civil;
    }

    public static function getSexos()
    {
        foreach (self::$datos_captura['sexos'] as $data) self::$sexos[$data['codsex']] = $data['detsex'];
        return self::$sexos;
    }

    public static function getZonas()
    {
        foreach (self::$datos_captura['zonas'] as $data) self::$zonas[$data['codzon']] = $data['detzon'];
        return self::$zonas;
    }

    public static function getCiudades()
    {
        foreach (self::$datos_captura['ciudades'] as $data) self::$ciudades[$data['codciu']] = $data['detciu'];
        return self::$ciudades;
    }

    public static function getTiposDocumentos()
    {
        foreach (self::$datos_captura['tipo_documentos'] as $data) self::$tipos_documentos[$data['coddoc']] = $data['detdoc'];
        return self::$tipos_documentos;
    }

    public static function getOcupaciones()
    {
        foreach (self::$datos_captura['ocupaciones'] as $data) self::$ocupaciones["{$data['codocu']}"] = $data['detalle'];
        return self::$ocupaciones;
    }
}
