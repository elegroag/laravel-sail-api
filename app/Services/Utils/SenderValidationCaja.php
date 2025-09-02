<?php
require_once 'SenderEmail.php';

class SenderValidationCaja 
{

    private $email_pruebas = 'soportesistemas.comfaca@gmail.com';

    public function __construct()
    {
        parent::__construct();
    }


    public function send($tipopc, $entity)
    {
        $mercurio10 = new Mercurio10();
        $mercurio10->setTransaction(parent::$transaction);
        $mercurio10->setTipopc($tipopc);
        $mercurio10->setNumero($entity->id);
        $mercurio10->setItem($entity->item);
        $mercurio10->setEstado("P");
        $mercurio10->setNota("Envío a la Caja para verificación");
        $mercurio10->setFecsis(date('Y-m-d'));
        $mercurio10->save();

        $mercurio02 = $this->Mercurio02->findFirst();
        $arreglo = array(
            "titulo" => "Cordial saludo,<br>Señor@ {$entity->repleg}",
            "msj"    => "La Caja de Compensación Familiar Comfaca, ha recepcionado una solicitud, por medio del sistema comfaca en línea, " .
                "emitido por el afiliado: {$entity->razsoc} con identificación: {$entity->nit}.<br>Su solicitud está pendiente de verificación por parte de la CAJA.<br/>" .
                "<br/>Gracias por preferirnos.",
            "rutaImg"    => "https://comfacaenlinea.com.co/public/img/header_reporte_ugpp.png",
            "url_activa" => "https://comfacaenlinea.com.co/Mercurio/Mercurio/login/ingreso_persona",
            "mercurio02" => array(
                "razsoc"    => $mercurio02->getRazsoc(),
                "direccion" => $mercurio02->getDireccion(),
                "email"     => $mercurio02->getEmail(),
                "telefono"  => $mercurio02->getTelefono(),
                "pagweb"    => $mercurio02->getPagweb()
            )
        );

        $html = View::render("empresa/tmp/mailcaja", $arreglo);

        $destinatarios = array(array(
            "email" => ($this->enviroment == 'production') ? $entity->email : $this->email_pruebas,
            "nombre" => $entity->repleg
        ));

        $this->sendEmail("Proceso Afiliación Caja de Compensación Familiar COMFACA", $html, $destinatarios);
    }

    function sendEmail($asunto, $html, $destinatarios)
    {
        $emailCaja = $this->Mercurio01->findFirst();
        $senderEmail = new SenderEmail();
        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            "asunto: {$asunto}"
        );

        $senderEmail->send(
            $destinatarios,
            $html
        );
    }
}
