<?php

namespace App\Services\Cajas;

use App\Exceptions\DebugException;
use App\Models\Gener02;
use App\Models\Mercurio09;
use App\Models\Mercurio30;
use App\Models\Mercurio31;
use App\Models\Mercurio32;
use App\Models\Mercurio34;
use App\Models\Mercurio35;
use App\Models\Mercurio45;
use App\Services\Utils\GeneralService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReasignaService
{
    /** Tipopc soportados en reasignación masiva. */
    public const TIPOPC_MASIVO = ['1', '2', '3', '4', '7', '8'];

    /**
     * @return list<array{value: string, label: string}>
     */
    public function tipopcOptions(): array
    {
        return Mercurio09::query()
            ->orderBy('tipopc')
            ->get(['tipopc', 'detalle'])
            ->map(fn (Mercurio09 $row) => [
                'value' => (string) $row->tipopc,
                'label' => (string) $row->detalle,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function tipopcMasivoOptions(): array
    {
        return array_values(array_filter(
            $this->tipopcOptions(),
            fn (array $opt) => in_array($opt['value'], self::TIPOPC_MASIVO, true)
        ));
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function usuariosOptions(): array
    {
        return Gener02::query()
            ->select('gener02.usuario', 'gener02.nombre')
            ->where('gener02.estado', 'A')
            ->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
            ->distinct()
            ->orderBy('gener02.nombre')
            ->get()
            ->map(fn (Gener02 $row) => [
                'value' => (string) $row->usuario,
                'label' => (string) $row->nombre,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, list<array{value: string, label: string}>>
     */
    public function usuariosByTipopc(): array
    {
        $rows = Gener02::query()
            ->select('gener02.usuario', 'gener02.nombre', 'mercurio08.tipopc')
            ->where('gener02.estado', 'A')
            ->join('mercurio08', 'gener02.usuario', '=', 'mercurio08.usuario')
            ->orderBy('gener02.nombre')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $tipopc = (string) $row->tipopc;
            $map[$tipopc] ??= [];
            $value = (string) $row->usuario;
            if (! collect($map[$tipopc])->contains(fn ($opt) => $opt['value'] === $value)) {
                $map[$tipopc][] = [
                    'value' => $value,
                    'label' => (string) $row->nombre,
                ];
            }
        }

        return $map;
    }

    /**
     * @return list<array{id: int|string, documento: string, nombre: string}>
     */
    public function consultar(string $tipopc, string $usuario): array
    {
        $out = (new GeneralService)->consultaTipopc($tipopc, 'alluser', '', $usuario);
        $datos = $out['datos'] ?? [];

        if ($datos instanceof Collection) {
            $items = $datos;
        } elseif (is_array($datos)) {
            $items = collect($datos);
        } else {
            $items = collect();
        }

        return $items->map(function ($item) use ($tipopc) {
            [$documento, $nombre] = $this->resolveDocumentoNombre($item, $tipopc);

            return [
                'id' => is_object($item) && method_exists($item, 'getId')
                    ? $item->getId()
                    : (is_object($item) ? ($item->id ?? null) : ($item['id'] ?? null)),
                'documento' => $documento,
                'nombre' => $nombre,
            ];
        })->values()->all();
    }

    /**
     * @return array{success: bool, msj: string, updated: int}
     */
    public function reasignarMasivo(
        string $tipopc,
        string $usuori,
        string $usudes,
        string $fecini,
        string $fecfin
    ): array {
        if (! in_array($tipopc, self::TIPOPC_MASIVO, true)) {
            throw ValidationException::withMessages([
                'tipopc' => 'Tipo de opción no permitido para proceso masivo.',
            ]);
        }

        if ($usuori === $usudes) {
            throw ValidationException::withMessages([
                'usudes' => 'El usuario destino debe ser diferente al origen.',
            ]);
        }

        $this->assertFechas($fecini, $fecfin);

        $modelClass = $this->modelClassForTipopc($tipopc);
        if ($modelClass === null) {
            throw ValidationException::withMessages([
                'tipopc' => 'No hay modelo asociado al tipo de opción.',
            ]);
        }

        $updated = $modelClass::query()
            ->where('usuario', $usuori)
            ->where('estado', 'P')
            ->whereBetween('fecsol', [$fecini, $fecfin])
            ->update(['usuario' => $usudes]);

        return [
            'success' => true,
            'msj' => $updated > 0
                ? "Asignación de solicitudes con éxito ({$updated})"
                : 'No se encontraron solicitudes pendientes en el rango indicado.',
            'updated' => $updated,
        ];
    }

    /**
     * @return array{success: bool, msj: string}
     */
    public function cambiarUsuario(string $tipopc, int|string $id, string $usuario): array
    {
        DB::beginTransaction();
        try {
            $out = (new GeneralService)->consultaTipopc($tipopc, 'one', (string) $id, '');
            $solicitud = $out['datos'] ?? null;

            if (! $solicitud instanceof Model) {
                throw new DebugException('No se encontró la solicitud', 501);
            }

            $solicitud->update(['usuario' => $usuario]);
            DB::commit();

            return [
                'success' => true,
                'msj' => 'Cambio de usuario con éxito',
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'msj' => $e->getMessage(),
            ];
        }
    }

    /**
     * @return class-string<Model>|null
     */
    private function modelClassForTipopc(string $tipopc): ?string
    {
        return match ($tipopc) {
            '1' => Mercurio31::class,
            '2' => Mercurio30::class,
            '3' => Mercurio32::class,
            '4' => Mercurio34::class,
            '7' => Mercurio35::class,
            '8' => Mercurio45::class,
            default => null,
        };
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function resolveDocumentoNombre(mixed $item, string $tipopc): array
    {
        $get = function (string $attr) use ($item): string {
            if (is_object($item)) {
                $getter = 'get'.ucfirst($attr);
                if (method_exists($item, $getter)) {
                    return trim((string) ($item->{$getter}() ?? ''));
                }

                return trim((string) ($item->{$attr} ?? ''));
            }

            return trim((string) ($item[$attr] ?? ''));
        };

        return match ($tipopc) {
            '1', '9', '10', '11', '12' => [$get('cedtra'), $get('nombre')],
            '2' => [$get('nit'), $get('razsoc')],
            '3' => [$get('cedcon'), $get('nombre')],
            '4' => [$get('numdoc'), $get('nombre')],
            '5' => [$get('documento'), $get('nombre')],
            '7' => [$get('cedtra'), $get('nomtra') !== '' ? $get('nomtra') : $get('nombre')],
            '8' => [$get('codben') !== '' ? $get('codben') : $get('documento'), $get('nombre')],
            default => [$get('documento'), $get('nombre')],
        };
    }

    private function assertFechas(string $fecini, string $fecfin): void
    {
        try {
            $ini = Carbon::createFromFormat('Y-m-d', $fecini)->startOfDay();
            $fin = Carbon::createFromFormat('Y-m-d', $fecfin)->startOfDay();
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'fecini' => 'Las fechas deben tener formato YYYY-MM-DD.',
            ]);
        }

        if ($fin->lt($ini)) {
            throw ValidationException::withMessages([
                'fecfin' => 'La fecha final debe ser mayor o igual a la inicial.',
            ]);
        }
    }
}
