<?php

namespace App\Services\Utils;

use App\Models\Mercurio01;
use App\Models\Mercurio02;
use App\Models\Mercurio07;
use App\Services\Request;

class NotifyEmailServices
{

    protected $email_pruebas = "elegro@comfaca.com.co";

    public function emailRechazar($entity, $msj)
    {
        $mercurio07 = Mercurio07::where([
            "tipo" => $entity->getTipo(),
            "coddoc" => $entity->getCoddoc(),
            "documento" => $entity->getDocumento()
        ])->first();

        $mercurio02 = Mercurio02::first();
        $params = array(
            "titulo" => "Cordial saludo, señor@ {$mercurio07->getNombre()}",
            "msj" => $msj,
            "rutaImg"    => "https://comfacaenlinea.com.co/public/img/header_reporte_ugpp.png",
            "url_activa" => "https://comfacaenlinea.com.co/Mercurio/Mercurio/login/index",
            "mercurio02" => array(
                "razsoc"    => $mercurio02->getRazsoc(),
                "direccion" => $mercurio02->getDireccion(),
                "email"     => $mercurio02->getEmail(),
                "telefono"  => $mercurio02->getTelefono(),
                "pagweb"    => $mercurio02->getPagweb()
            )
        );

        $html = view("caja/layouts/rechazar", $params)->render();
        $emailCaja = Mercurio01::first();

        $senderEmail = new SenderEmail(
            new Request(
                array(
                    "emisor_email" => $emailCaja->getEmail(),
                    "emisor_clave" => $emailCaja->getClave(),
                    "asunto" => "Se Rechaza Afiliación, Caja de Compensación Familiar COMFACA"
                )
            )
        );
        $senderEmail->send(
            env("API_MODE") === "production" ? $mercurio07->getEmail() : $this->email_pruebas,
            $html
        );
    }

    public function emailDevolver($entity, $msj)
    {
        $mercurio07 = Mercurio07::where([
            "tipo" => $entity->getTipo(),
            "coddoc" => $entity->getCoddoc(),
            "documento" => $entity->getDocumento()
        ])->first();

        $mercurio02 = Mercurio02::first();

        $params = array(
            "titulo" => "Cordial saludo, señor@ {$mercurio07->getNombre()}",
            "msj"    => $msj,
            "rutaImg"    => "https://comfacaenlinea.com.co/public/img/header_reporte_ugpp.png",
            "url_activa" => "https://comfacaenlinea.com.co/Mercurio/Mercurio/login/index",
            "mercurio02" => array(
                "razsoc"    => $mercurio02->getRazsoc(),
                "direccion" => $mercurio02->getDireccion(),
                "email"     => $mercurio02->getEmail(),
                "telefono"  => $mercurio02->getTelefono(),
                "pagweb"    => $mercurio02->getPagweb()
            )
        );
        $html = view("cajas/layouts/devolver", $params)->render();
        $emailCaja = Mercurio01::first();
        $senderEmail = new SenderEmail(
            new Request(
                array(
                    "emisor_email" => $emailCaja->getEmail(),
                    "emisor_clave" => $emailCaja->getClave(),
                    "asunto" => "Se Devuelve Afiliación, Caja de Compensación Familiar COMFACA"
                )
            )
        );

        $senderEmail->send(env("API_MODE") === "production" ? $mercurio07->getEmail() : $this->email_pruebas, $html);
    }

    public function emailApruebaEmpresa($correo, $nombre, $asunto, $msj, $file)
    {
        $mercurio02 = Mercurio02::first();
        $mercurio01 = Mercurio01::first();
    }
}
