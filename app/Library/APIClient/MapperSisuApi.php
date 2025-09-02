<?php
namespace App\Library\APIClient;

if (!function_exists('mapper_sisu_api')) {
    function mapper_sisu_api($str)
    {
        $servicios = array(
            "ComfacaEmpresas" => "company",
            "ComfacaAfilia" => "affiliation",
            "AportesEmpresas" => "aportes",
            "Correspondencias" => "correspondencia",
            "Novedades" => "novedades",
            "ServicioSat" => "sat",
            "Tesoreria" => "tesoreria",
            "CruzarDaviplata" => "tippag",
            "Reprocesos" => "sat",
            "Certificados" => "certificados",
            "Usuarios" => "usuarios",
            "Funcionalidades" => "satservice"
        );
        return $servicios["{$str}"];
    }
}
