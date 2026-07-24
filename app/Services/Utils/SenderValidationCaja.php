<?php

namespace App\Services\Utils;

use App\Models\Mercurio01;
use App\Models\Mercurio02;
use App\Models\Mercurio10;
use Carbon\Carbon;

require_once 'SenderEmail.php';

class SenderValidationCaja
{
    private $email_pruebas = 'enlinea@comfaca.com';

    public function __construct() {}

    public function send(?string $tipopc, mixed $entity): void
    {
        $this->email_pruebas = config('mail.dev', 'enlinea@comfaca.com');

        $mercurio10 = Mercurio10::create([
            'tipopc' => $tipopc,
            'numero' => $entity->id,
            'item' => $entity->item,
            'estado' => 'P',
            'nota' => 'Envío a la Caja para verificación',
            'fecsis' => date('Y-m-d'),
        ]);

        if ($mercurio10->estado === 'P') {
            $base = $entity->ruuid ?? null;
            if ($base) {
                $sufijo = str_pad((string) $mercurio10->item, 2, '0', STR_PAD_LEFT);
                $mercurio10->setRuuid($base.'-'.$sufijo);
                $mercurio10->save();
            }
        }

        $mercurio02 = Mercurio02::first();
        $fecsol = $entity->fecsol
            ? Carbon::parse($entity->fecsol)->format('Y-m-d')
            : date('Y-m-d');

        $radicado = $entity->ruuid ?? ($entity->id ?? '');

        $arreglo = [
            'titulo' => "Cordial saludo,<br>Señor@ {$entity->repleg}",
            'msj' => 'La Caja de Compensación Familiar Comfaca, ha recepcionado una solicitud, por medio del sistema comfaca en línea, '.
                "emitido por el afiliado: {$entity->razsoc} con identificación: {$entity->nit}.<br>Su solicitud está pendiente de verificación por parte de la CAJA.<br/>".
                '<br/>Gracias por preferirnos.',
            'fecsol' => $fecsol,
            'radicado' => $radicado,
            'rutaImg' => config('app.url').'/img/header_reporte_ugpp.png',
            'url_activa' => config('app.url').'/web/login',
            'mercurio02' => [
                'razsoc' => $mercurio02->getRazsoc(),
                'direccion' => $mercurio02->getDireccion(),
                'email' => $mercurio02->getEmail(),
                'telefono' => $mercurio02->getTelefono(),
                'pagweb' => $mercurio02->getPagweb(),
            ],
        ];

        $html = view('emails/mail-caja', $arreglo)->render();
        $destinatario = (config('app.env') == 'production') ? $entity->email : $this->email_pruebas;
        $this->sendEmail('Proceso Afiliación Caja de Compensación Familiar COMFACA', $html, $destinatario);
    }

    public function sendEmail(string $asunto, string $html, string $destinatario): void
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
