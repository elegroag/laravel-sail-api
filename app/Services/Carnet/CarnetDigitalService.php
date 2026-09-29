<?php

namespace App\Services\Carnet;

use App\Exceptions\DebugException;
use App\Models\CarnetToken;
use App\Services\Api\ApiSubsidio;
use Carbon\Carbon;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Throwable;

class CarnetDigitalService
{
    /**
     * La aplicación corre en UTC; las fechas visibles al afiliado van en hora de Colombia.
     */
    private const ZONA_HORARIA = 'America/Bogota';

    /**
     * Datos del carnet del trabajador consultados en SISU.
     *
     * @return array{trabajador: array<string, string>, beneficiarios: array<int, array<string, string>>, consultado: string}
     */
    public function datosCarnet(string $cedtra, string $coddoc): array
    {
        $ps = new ApiSubsidio;
        $ps->send([
            'servicio' => 'PoblacionAfiliada',
            'metodo' => 'nucleo_familiar_trabajador',
            'params' => [
                'cedtra' => $cedtra,
            ],
        ]);
        $out = $ps->toArray();

        $trabajador = ($out['success'] ?? false) ? ($out['data']['trabajador'] ?? []) : [];
        if (empty($trabajador)) {
            throw new DebugException('No fue posible consultar la información de afiliación del trabajador.', 501);
        }

        $tiposDocumento = $this->parametros('parametros_trabajadores', 'tipo_documentos', 'coddoc', 'codrua');
        $parentescos = $this->parametros('parametros_beneficiarios', 'parent', 'estado', 'detalle');

        $nombre = trim((string) ($trabajador['fullname'] ?? $this->nombreCompleto($trabajador)));
        $estado = (string) ($trabajador['estado'] ?? '');
        $codcat = (string) ($trabajador['codcat'] ?? '');

        return [
            'trabajador' => [
                'nombre' => $nombre,
                'iniciales' => $this->iniciales($trabajador, $nombre),
                'tipo_documento' => $tiposDocumento[$coddoc] ?? '',
                'documento' => $this->formatoDocumento($cedtra),
                'empresa' => (string) ($trabajador['razsoc'] ?? ''),
                'nit' => (string) ($trabajador['nit'] ?? ''),
                'categoria' => $codcat,
                'estado' => $estado,
                'estado_detalle' => get_user_estados()[$estado] ?? $estado,
                'fecha_afiliacion' => $this->formatoFecha($trabajador['fecafi'] ?? null),
            ],
            'beneficiarios' => $this->beneficiarios(
                $out['data']['conyuges'] ?? [],
                $out['data']['beneficiarios'] ?? [],
                $tiposDocumento,
                $parentescos
            ),
            'consultado' => now(self::ZONA_HORARIA)->format('d/m/Y h:i a'),
        ];
    }

    public function urlVerificacion(CarnetToken $carnetToken): string
    {
        return route('carnet.verificar', ['token' => $carnetToken->getToken()]);
    }

    public function qrDataUri(string $contenido): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::MARKUP_SVG,
            'eccLevel' => QRCode::ECC_M,
            'addQuietzone' => false,
        ]);

        return (new QRCode($options))->render($contenido);
    }

    /**
     * Valida el token contra el estado actual del afiliado en SISU.
     *
     * @return array{nombre: string, activo: bool, estado_detalle: string, fecha: string}|null
     */
    public function verificar(string $token): ?array
    {
        $carnetToken = CarnetToken::findActivo($token);
        if (! $carnetToken) {
            return null;
        }

        $ps = new ApiSubsidio;
        $ps->send([
            'servicio' => 'ComfacaEmpresas',
            'metodo' => 'informacion_trabajador',
            'params' => [
                'cedtra' => $carnetToken->getDocumento(),
                'coddoc' => $carnetToken->getCoddoc(),
            ],
        ]);
        $out = $ps->toArray();

        if (! ($out['success'] ?? false) || empty($out['data'])) {
            throw new DebugException('No fue posible validar el carnet en este momento.', 501);
        }

        $carnetToken->registrarVerificacion();

        $estado = (string) ($out['data']['estado'] ?? '');

        return [
            'nombre' => self::enmascararNombre($this->nombreCompleto($out['data'])),
            'activo' => $estado === 'A',
            'estado_detalle' => get_user_estados()[$estado] ?? 'Sin afiliación vigente',
            'fecha' => now(self::ZONA_HORARIA)->format('d/m/Y h:i a'),
        ];
    }

    /**
     * Conserva el primer nombre y deja solo la inicial de las demás palabras.
     */
    public static function enmascararNombre(string $nombre): string
    {
        $palabras = preg_split('/\s+/u', trim($nombre), -1, PREG_SPLIT_NO_EMPTY);
        if (! $palabras) {
            return '';
        }

        $primera = array_shift($palabras);
        $resto = array_map(fn (string $palabra) => mb_substr($palabra, 0, 1).'***', $palabras);

        return mb_strtoupper(implode(' ', array_merge([$primera], $resto)));
    }

    /**
     * @param  array<int, array<string, mixed>>  $conyuges
     * @param  array<int, array<string, mixed>>  $beneficiarios
     * @param  array<string, string>  $tiposDocumento
     * @param  array<string, string>  $parentescos
     * @return array<int, array<string, string>>
     */
    private function beneficiarios(array $conyuges, array $beneficiarios, array $tiposDocumento, array $parentescos): array
    {
        $salida = [];

        foreach ($conyuges as $conyuge) {
            if (($conyuge['estado'] ?? 'A') !== 'A') {
                continue;
            }
            $salida[] = [
                'nombre' => $this->nombreCompleto($conyuge),
                'tipo_documento' => $tiposDocumento[$conyuge['coddoc'] ?? ''] ?? '',
                'documento' => $this->formatoDocumento((string) ($conyuge['cedcon'] ?? '')),
                'parentesco' => 'Cónyuge',
                'icono' => 'fa-heart',
            ];
        }

        foreach ($beneficiarios as $beneficiario) {
            if (($beneficiario['estado'] ?? 'A') !== 'A') {
                continue;
            }
            $parentesco = $parentescos[$beneficiario['parent'] ?? ''] ?? (string) ($beneficiario['parent'] ?? '');
            $salida[] = [
                'nombre' => trim((string) ($beneficiario['nombre'] ?? $this->nombreCompleto($beneficiario))),
                'tipo_documento' => $tiposDocumento[$beneficiario['coddoc'] ?? ''] ?? '',
                'documento' => $this->formatoDocumento((string) ($beneficiario['documento'] ?? $beneficiario['numdoc'] ?? '')),
                'parentesco' => mb_convert_case(mb_strtolower($parentesco), MB_CASE_TITLE),
                'icono' => str_contains(mb_strtoupper($parentesco), 'HIJ') ? 'fa-child' : 'fa-user',
            ];
        }

        return $salida;
    }

    /**
     * Catálogo de SISU como arreglo clave => valor; vacío si el servicio no responde.
     *
     * @return array<string, string>
     */
    private function parametros(string $metodo, string $catalogo, string $clave, string $valor): array
    {
        try {
            $ps = new ApiSubsidio;
            $ps->send([
                'servicio' => 'ComfacaAfilia',
                'metodo' => $metodo,
            ]);
            $out = $ps->toArray();
        } catch (Throwable) {
            return [];
        }

        $items = $out['data'][$catalogo] ?? $out[$catalogo] ?? [];
        $salida = [];
        foreach ($items as $item) {
            if (isset($item[$clave], $item[$valor])) {
                $salida[(string) $item[$clave]] = (string) $item[$valor];
            }
        }

        return $salida;
    }

    /**
     * @param  array<string, mixed>  $persona
     */
    private function nombreCompleto(array $persona): string
    {
        $partes = [
            $persona['prinom'] ?? '',
            $persona['segnom'] ?? '',
            $persona['priape'] ?? '',
            $persona['segape'] ?? '',
        ];

        return trim(preg_replace('/\s+/', ' ', implode(' ', $partes)));
    }

    /**
     * @param  array<string, mixed>  $trabajador
     */
    private function iniciales(array $trabajador, string $nombre): string
    {
        if (! empty($trabajador['prinom']) && ! empty($trabajador['priape'])) {
            return mb_strtoupper(mb_substr($trabajador['prinom'], 0, 1).mb_substr($trabajador['priape'], 0, 1));
        }

        $palabras = preg_split('/\s+/u', $nombre, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return mb_strtoupper(implode('', array_map(fn (string $palabra) => mb_substr($palabra, 0, 1), array_slice($palabras, 0, 2))));
    }

    private function formatoDocumento(string $documento): string
    {
        return ctype_digit($documento) ? number_format((float) $documento, 0, ',', '.') : $documento;
    }

    private function formatoFecha(mixed $fecha): string
    {
        if (empty($fecha)) {
            return '';
        }

        try {
            return Carbon::parse($fecha)->format('d/m/Y');
        } catch (Throwable) {
            return (string) $fecha;
        }
    }
}
