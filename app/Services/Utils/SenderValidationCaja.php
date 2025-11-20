<?php

namespace App\Services\Utils;

use App\Models\Mercurio01;
use App\Models\Mercurio02;
use App\Models\Mercurio10;

require_once 'SenderEmail.php';

class SenderValidationCaja
{
    private $email_pruebas = 'enlinea@comfaca.com';

    public function __construct() {}

    public function send($tipopc, $entity)
    {
        $this->email_pruebas = env('MAIL_DEV') ?? 'enlinea@comfaca.com';

        Mercurio10::create([
            'tipopc' => $tipopc,
            'numero' => $entity->id,
            'item' => $entity->item,
            'estado' => 'P',
            'nota' => 'Envío a la Caja para verificación',
            'fecsis' => date('Y-m-d'),
        ]);

        $mercurio02 = Mercurio02::first();
        $arreglo = [
            'titulo' => "Cordial saludo,<br>Señor@ {$entity->repleg}",
            'msj' => 'La Caja de Compensación Familiar Comfaca, ha recepcionado una solicitud, por medio del sistema comfaca en línea, ' .
                "emitido por el afiliado: {$entity->razsoc} con identificación: {$entity->nit}.<br>Su solicitud está pendiente de verificación por parte de la CAJA.<br/>" .
                '<br/>Gracias por preferirnos.',
            'rutaImg' => 'https://comfacaenlinea.com.co/public/img/header_reporte_ugpp.png',
            'url_activa' => 'https://comfacaenlinea.com.co/Mercurio/Mercurio/login/ingreso_persona',
            'mercurio02' => [
                'razsoc' => $mercurio02->getRazsoc(),
                'direccion' => $mercurio02->getDireccion(),
                'email' => $mercurio02->getEmail(),
                'telefono' => $mercurio02->getTelefono(),
                'pagweb' => $mercurio02->getPagweb(),
            ],
        ];

        $html = view('emails/mail-caja', $arreglo)->render();
        $destinatario = (env('APP_ENV') == 'production') ? $entity->email : $this->email_pruebas;
        $this->sendEmail('Proceso Afiliación Caja de Compensación Familiar COMFACA', $html, $destinatario);
    }

    public function sendEmail($asunto, $html, $destinatario)
    {
        $emailCaja = Mercurio01::first();
        $senderEmail = new SenderEmail;
        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            "asunto: {$asunto}"
        );

        $senderEmail->send(
            $destinatario,
            $html
        );
    }
}
