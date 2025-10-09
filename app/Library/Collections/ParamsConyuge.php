<?php

namespace App\Library\Collections;

class ParamsConyuge
{
    public static $zonas;

    public static $ciudades;

    public static $tipos_documentos;

    public static $ocupaciones;

    public static $sexos;

    public static $estado_civil;

    public static $capacidad_trabajar;

    public static $nivel_educativo;

    public static $vivienda;

    public static $tipo_pago;

    public static $codigo_cuenta;

    public static $tipo_cuenta;

    public static $recibe_subsidio;

    public static $companero_permanente;

    public static $datos_captura;

    public static $bancos;

    public static $tipo_discapacidad;

    public static $pertenencia_etnicas;

    public static $resguardos;

    public static $pueblos_indigenas;

    public function setDatosCaptura($datos_captura)
    {
        self::$datos_captura = $datos_captura;
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

    public static function getPertenenciaEtnicas()
    {
        foreach (self::$datos_captura['pertenencia_etnicas'] as $data) {
            self::$pertenencia_etnicas[$data['codigo']] = $data['nombre'];
        }

        return self::$pertenencia_etnicas;
    }

    public static function getTipoDiscapacidad()
    {
        foreach (self::$datos_captura['discapacidades'] as $data) {
            self::$tipo_discapacidad[$data['tipdis']] = $data['detalle'];
        }

        return self::$tipo_discapacidad;
    }

    public static function getBancos()
    {
        foreach (self::$datos_captura['bancos'] as $data) {
            self::$bancos[$data['codban']] = $data['detalle'];
        }

        return self::$bancos;
    }

    public static function getCompaneroPermanente()
    {
        if (is_null(self::$companero_permanente) == true) {
            foreach (self::$datos_captura['companero_permanente'] as $data) {
                self::$companero_permanente[$data['estado']] = $data['detalle'];
            }
        }

        return self::$companero_permanente;
    }

    public static function getRecibeSubsidio()
    {
        if (is_null(self::$recibe_subsidio) == true) {
            foreach (self::$datos_captura['recibe_subsidio'] as $data) {
                self::$recibe_subsidio[$data['estado']] = $data['detalle'];
            }
        }

        return self::$recibe_subsidio;
    }

    public static function getTipoCuenta()
    {
        if (is_null(self::$tipo_cuenta) == true) {
            foreach (self::$datos_captura['tipo_cuenta'] as $data) {
                self::$tipo_cuenta[$data['estado']] = $data['detalle'];
            }
        }

        return self::$tipo_cuenta;
    }

    public static function getCodigoCuenta()
    {
        if (is_null(self::$codigo_cuenta) == true) {
            foreach (self::$datos_captura['codigo_cuenta'] as $data) {
                self::$codigo_cuenta[$data['codcue']] = $data['detalle'];
            }
        }

        return self::$codigo_cuenta;
    }

    public static function getTipoPago()
    {
        if (is_null(self::$tipo_pago) == true) {
            foreach (self::$datos_captura['tipo_pago'] as $data) {
                self::$tipo_pago[$data['estado']] = $data['detalle'];
            }
        }

        return self::$tipo_pago;
    }

    public static function getVivienda()
    {
        if (is_null(self::$vivienda) == true) {
            foreach (self::$datos_captura['vivienda'] as $data) {
                self::$vivienda[$data['vivienda']] = $data['detalle'];
            }
        }

        return self::$vivienda;
    }

    public static function getCapacidadTrabajar()
    {
        if (is_null(self::$capacidad_trabajar) == true) {
            foreach (self::$datos_captura['captra'] as $data) {
                self::$capacidad_trabajar[$data['captra']] = $data['detalle'];
            }
        }

        return self::$capacidad_trabajar;
    }

    public static function getNivelEducativo()
    {
        if (is_null(self::$nivel_educativo) == true) {
            foreach (self::$datos_captura['nivel_educativos'] as $data) {
                self::$nivel_educativo[$data['nivedu']] = $data['detalle'];
            }
        }

        return self::$nivel_educativo;
    }

    public static function getEstadoCivil()
    {
        if (is_null(self::$estado_civil) == true) {
            foreach (self::$datos_captura['estado_civiles'] as $data) {
                self::$estado_civil[$data['estciv']] = $data['detest'];
            }
        }

        return self::$estado_civil;
    }

    public static function getSexos()
    {
        if (is_null(self::$sexos) == true) {
            foreach (self::$datos_captura['sexos'] as $data) {
                self::$sexos[$data['codsex']] = $data['detsex'];
            }
        }

        return self::$sexos;
    }

    public static function getZonas()
    {
        if (is_null(self::$zonas) == true) {
            foreach (self::$datos_captura['zonas'] as $data) {
                self::$zonas[$data['codzon']] = $data['detzon'];
            }
        }

        return self::$zonas;
    }

    public static function getCiudades()
    {
        if (is_null(self::$ciudades) == true) {
            foreach (self::$datos_captura['ciudades'] as $data) {
                self::$ciudades[$data['codciu']] = $data['detciu'];
            }
        }

        return self::$ciudades;
    }

    public static function getTiposDocumentos()
    {
        if (is_null(self::$tipos_documentos) == true) {
            foreach (self::$datos_captura['tipo_documentos'] as $data) {
                self::$tipos_documentos[$data['coddoc']] = $data['detdoc'];
            }
        }

        return self::$tipos_documentos;
    }

    public static function getOcupaciones()
    {
        if (is_null(self::$ocupaciones) == true) {
            foreach (self::$datos_captura['ocupaciones'] as $data) {
                self::$ocupaciones["{$data['codocu']}"] = $data['detalle'];
            }
        }

        return self::$ocupaciones;
    }
}
