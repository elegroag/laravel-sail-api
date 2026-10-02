<?php

namespace App\Services\Carnet;

use App\Exceptions\DebugException;
use App\Models\Mercurio01;
use App\Models\Mercurio07;
use App\Services\Utils\SenderEmail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Envía el carnet digital generado al correo registrado del afiliado.
 */
class CarnetEmailService
{
    /**
     * @param  array{documento?: string, coddoc?: string}  $user
     * @return array{email: string, email_masked: string}
     */
    public function enviar(array $user, ?string $tipo, string $contenido, string $nombreArchivo, string $formatoLabel): array
    {
        $documento = $user['documento'] ?? null;
        $coddoc = $user['coddoc'] ?? null;
        if (! $documento || ! $coddoc || ! $tipo) {
            throw new DebugException('No se pudo identificar al afiliado para el envío del carnet.');
        }

        $afiliado = Mercurio07::where('documento', $documento)
            ->where('coddoc', $coddoc)
            ->where('tipo', $tipo)
            ->first();
        if (! $afiliado) {
            throw new DebugException('No se encontró la cuenta del afiliado.');
        }

        $email = trim((string) $afiliado->getEmail());
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DebugException('No tienes un correo electrónico válido registrado en tu cuenta.');
        }

        $emailCaja = Mercurio01::first();
        if (! $emailCaja) {
            throw new DebugException('No se encontró la configuración de correo de la caja.');
        }

        $html = view('emails.carnet-digital', [
            'nombre' => mb_convert_case(mb_strtolower(trim((string) $afiliado->getNombre())), MB_CASE_TITLE) ?: 'Afiliado',
            'formato' => $formatoLabel,
        ])->render();

        // SenderEmail solo adjunta archivos en disco y usa el nombre del archivo como nombre del adjunto.
        $directorio = storage_path('app/tmp/carnet-'.Str::random(16));
        File::ensureDirectoryExists($directorio);
        $ruta = $directorio.'/'.$nombreArchivo;

        try {
            File::put($ruta, $contenido);

            (new SenderEmail)->setters(
                "emisor_email: {$emailCaja->getEmail()}",
                "emisor_clave: {$emailCaja->getClave()}",
                'emisor_nombre: Comfaca En Línea',
                'asunto: Carnet digital de afiliado - Comfaca En Línea'
            )->send($email, $html, [$ruta]);
        } finally {
            File::deleteDirectory($directorio);
        }

        return [
            'email' => $email,
            'email_masked' => $this->maskEmail($email),
        ];
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        if ($domain === '') {
            return '***';
        }

        return (substr($local, 0, 1) ?: '*').str_repeat('*', max(strlen($local) - 1, 2)).'@'.$domain;
    }
}
