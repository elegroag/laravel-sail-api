<?php

namespace App\Library\Collections;

class ParamsEmpresa
{
    public static $calidad_empresa;

    public static $ciudades_comerciales;

    public static $ciudades;

    public static $zonas;

    public static $codigo_cajas;

    public static $tipo_documentos;

    public static $codrua_documentos;

    public static $tipo_sociedades;

    public static $actividades;

    public static $tipo_persona;

    public static $tipo_empresa;

    public static $departamentos;

    public static $tipo_duracion;

    public static $codigo_indice;

    public static $paga_mes;

    public static $forma_presentacion;

    public static $pymes;

    public static $contratista;

    public static $tipo_aportante;

    public static $oficina;

    public static $colegio;

    public static $datos_captura;

    public function setDatosCaptura($datos_captura)
    {
        self::$datos_captura = $datos_captura;
    }

    public static function getPymes()
    {
        foreach (self::$datos_captura['pymes'] as $data) {
            self::$pymes[$data['estado']] = $data['detalle'];
        }

        return self::$pymes;
    }

    public static function getContratista()
    {
        foreach (self::$datos_captura['contratista'] as $data) {
            self::$contratista[$data['estado']] = $data['detalle'];
        }

        return self::$contratista;
    }

    public static function getTipoAportante()
    {
        foreach (self::$datos_captura['tipo_aportante'] as $data) {
            self::$tipo_aportante[$data['estado']] = $data['detalle'];
        }

        return self::$tipo_aportante;
    }

    public static function getOficina()
    {
        foreach (self::$datos_captura['oficina'] as $data) {
            self::$oficina["{$data['ofiafi']}"] = $data['detalle'];
        }

        return self::$oficina;
    }

    public static function getColegio()
    {
        foreach (self::$datos_captura['colegio'] as $data) {
            self::$colegio["{$data['estado']}"] = $data['detalle'];
        }

        return self::$colegio;
    }

    public static function getFormaPresentacion()
    {
        foreach (self::$datos_captura['forma_presentacion'] as $data) {
            self::$forma_presentacion[$data['estado']] = $data['detalle'];
        }

        return self::$forma_presentacion;
    }

    public static function getPagaMes()
    {
        foreach (self::$datos_captura['paga_mes'] as $data) {
            self::$paga_mes[$data['estado']] = $data['detalle'];
        }

        return self::$paga_mes;
    }

    public static function getCodigoIndice()
    {
        foreach (self::$datos_captura['codigo_indice'] as $data) {
            self::$codigo_indice[$data['codind']] = $data['detalle'];
        }

        return self::$codigo_indice;
    }

    public static function getTipoDuracion()
    {
        foreach (self::$datos_captura['tipo_duracion'] as $data) {
            self::$tipo_duracion[$data['estado']] = $data['detalle'];
        }

        return self::$tipo_duracion;
    }

    public static function getZonas()
    {
        foreach (self::$datos_captura['zonas'] as $data) {
            self::$zonas[$data['codzon']] = $data['codzon'].' '.$data['detzon'];
        }

        return self::$zonas;
    }

    public static function getCiudades()
    {
        foreach (self::$datos_captura['ciudades'] as $data) {
            self::$ciudades["{$data['codciu']}"] = $data['detciu'];
        }

        return self::$ciudades;
    }

    public static function getCiudadesComerciales()
    {
        foreach (self::$datos_captura['ciudad_comercial'] as $data) {
            self::$ciudades_comerciales["{$data['codciu']}"] = $data['detciu'];
        }

        return self::$ciudades_comerciales;
    }

    public static function getCalidadEmpresa()
    {
        foreach (self::$datos_captura['calidad_empresa'] as $data) {
            self::$calidad_empresa[$data['estado']] = $data['detalle'];
        }

        return self::$calidad_empresa;
    }

    public static function getCodigoCajas()
    {
        foreach (self::$datos_captura['codigo_cajas'] as $data) {
            self::$codigo_cajas[$data['codcaj']] = $data['detalle'];
        }

        return self::$codigo_cajas;
    }

    public static function getTipoDocumentos()
    {
        foreach (self::$datos_captura['tipo_documentos'] as $data) {
            self::$tipo_documentos["{$data['coddoc']}"] = $data['detdoc'];
        }

        return self::$tipo_documentos;
    }

    public static function getCodruaDocumentos()
    {
        foreach (self::$datos_captura['tipo_documentos'] as $data) {
            self::$codrua_documentos["{$data['codrua']}"] = $data['detdoc'];
        }

        return self::$codrua_documentos;
    }

    public static function getTipoSociedades()
    {
        foreach (self::$datos_captura['tipo_sociedades'] as $data) {
            self::$tipo_sociedades["{$data['tipsoc']}"] = $data['detalle'];
        }

        return self::$tipo_sociedades;
    }

    public static function getActividades()
    {
        foreach (self::$datos_captura['actividades'] as $data) {
            self::$actividades["{$data['codact']}"] = $data['codact'].' '.$data['detalle'];
        }

        return self::$actividades;
    }

    public static function getTipoPersona()
    {
        foreach (self::$datos_captura['tipo_persona'] as $data) {
            self::$tipo_persona["{$data['estado']}"] = $data['detalle'];
        }

        return self::$tipo_persona;
    }

    public static function getTipoEmpresa()
    {
        foreach (self::$datos_captura['tipo_empresa'] as $data) {
            self::$tipo_empresa["{$data['estado']}"] = $data['detalle'];
        }

        return self::$tipo_empresa;
    }

    public static function getDepartamentos()
    {
        foreach (self::$datos_captura['departamentos'] as $data) {
            self::$departamentos[$data['coddep']] = $data['detdep'];
        }

        return self::$departamentos;
    }
}
