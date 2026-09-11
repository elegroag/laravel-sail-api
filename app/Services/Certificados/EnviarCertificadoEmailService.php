<?php

namespace App\Services\Certificados;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Services\Utils\SenderEmail;

class EnviarCertificadoEmailService
{
    /**
     * @param  array{documento?: string, coddoc?: string}  $user
     * @return array{email: string, email_masked: string, nombre: string}
     */
    public function sendToSolicitante(array $user, ?string $tipo, Certificado $certificado, ?string $tipoCertificadoLabel = null): array
    {
        $documento = $user['documento'] ?? null;
        $coddoc = $user['coddoc'] ?? null;

        if (! $documento || ! $coddoc || ! $tipo) {
            throw new DebugException('No se pudo identificar el solicitante para el envío del certificado.');
        }

        $solicitante = Mercurio07::where('documento', $documento)
            ->where('coddoc', $coddoc)
            ->where('tipo', $tipo)
            ->first();

        if (! $solicitante) {
            throw new DebugException('No se encontró el usuario solicitante en Mercurio07.');
        }

        $email = trim((string) $solicitante->getEmail());
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DebugException('El solicitante no tiene un correo electrónico válido registrado.');
        }

        $path = $certificado->getFilePath();
        if (! is_file($path)) {
            throw new DebugException('No se encontró el archivo del certificado generado.');
        }

        $nombre = capitalize((string) $solicitante->getNombre()) ?: 'Usuario';
        $html = view('emails.certificado-afiliacion', [
            'nombre' => $nombre,
            'tipo_certificado' => $tipoCertificadoLabel,
        ])->render();

        $emailCaja = Mercurio01::first();
        if (! $emailCaja) {
            throw new DebugException('No se encontró la configuración de correo de la caja.');
        }

        $senderEmail = new SenderEmail;
        $senderEmail->setters(
            "emisor_email: {$emailCaja->getEmail()}",
            "emisor_clave: {$emailCaja->getClave()}",
            'emisor_nombre: Comfaca En Línea',
            'asunto: Certificado de afiliación - Comfaca En Línea'
        );

        $senderEmail->send($email, $html, [$path]);

        return [
            'email' => $email,
            'email_masked' => $this->maskEmail($email),
            'nombre' => $nombre,
        ];
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        if ($domain === '') {
            return '***';
        }

        $visible = substr($local, 0, 1) ?: '*';
        $hiddenLength = max(strlen($local) - 1, 2);

        return $visible.str_repeat('*', $hiddenLength).'@'.$domain;
    }
}
