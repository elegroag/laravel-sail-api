<?php

namespace App\Services\Cajas;

use App\Models\Gener02;
use App\Models\Gener40;
use App\Models\Gener42;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Gener42Service
{
    /**
     * @return list<array{value: int|string, label: string}>
     */
    public function opcionesUsuarios(): array
    {
        return Gener02::query()
            ->orderBy('nombre')
            ->get(['usuario', 'nombre'])
            ->map(fn (Gener02 $row) => [
                'value' => $row->usuario,
                'label' => trim((string) $row->nombre).' ('.$row->usuario.')',
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{codigo: string, detalle: string, orden: int|null}>
     */
    public function permisosAsignados(int|string $usuario): array
    {
        $codigos = Gener42::query()
            ->where('usuario', $usuario)
            ->pluck('permiso');

        return $this->mapPermisos(
            Gener40::query()
                ->whereIn('codigo', $codigos)
                ->orderBy('orden')
                ->orderBy('codigo')
                ->get()
        );
    }

    /**
     * @return list<array{codigo: string, detalle: string, orden: int|null}>
     */
    public function permisosDisponibles(int|string $usuario): array
    {
        $codigos = Gener42::query()
            ->where('usuario', $usuario)
            ->pluck('permiso');

        return $this->mapPermisos(
            Gener40::query()
                ->when($codigos->isNotEmpty(), fn ($q) => $q->whereNotIn('codigo', $codigos))
                ->orderBy('orden')
                ->orderBy('codigo')
                ->get()
        );
    }

    /**
     * @param  list<string>  $permisos
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function asignar(int|string $usuario, array $permisos): array
    {
        try {
            $permisos = $this->permisosValidos($permisos);

            if ($permisos === []) {
                return [
                    'flag' => false,
                    'msg' => 'No se enviaron permisos válidos',
                    'msj' => 'No se enviaron permisos válidos',
                ];
            }

            DB::beginTransaction();

            foreach ($permisos as $permiso) {
                Gener42::firstOrCreate([
                    'usuario' => $usuario,
                    'permiso' => $permiso,
                ]);
            }

            DB::commit();

            return [
                'flag' => true,
                'msg' => 'Operación realizada correctamente',
                'msj' => 'Operación realizada correctamente',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'flag' => false,
                'msg' => 'No se pudieron asignar los permisos: '.$e->getMessage(),
                'msj' => 'No se pudieron asignar los permisos: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @param  list<string>  $permisos
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function quitar(int|string $usuario, array $permisos): array
    {
        try {
            $permisos = $this->permisosValidos($permisos);

            if ($permisos === []) {
                return [
                    'flag' => false,
                    'msg' => 'No se enviaron permisos válidos',
                    'msj' => 'No se enviaron permisos válidos',
                ];
            }

            DB::beginTransaction();

            Gener42::query()
                ->where('usuario', $usuario)
                ->whereIn('permiso', $permisos)
                ->delete();

            DB::commit();

            return [
                'flag' => true,
                'msg' => 'Operación realizada correctamente',
                'msj' => 'Operación realizada correctamente',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'flag' => false,
                'msg' => 'No se pudieron quitar los permisos: '.$e->getMessage(),
                'msj' => 'No se pudieron quitar los permisos: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @param  Collection<int, Gener40>  $rows
     * @return list<array{codigo: string, detalle: string, orden: int|null}>
     */
    private function mapPermisos(Collection $rows): array
    {
        return $rows->map(fn (Gener40 $row) => [
            'codigo' => (string) $row->codigo,
            'detalle' => (string) $row->detalle,
            'orden' => $row->orden !== null ? (int) $row->orden : null,
        ])->values()->all();
    }

    /**
     * @param  list<mixed>  $permisos
     * @return list<string>
     */
    private function permisosValidos(array $permisos): array
    {
        $codigos = array_values(array_unique(array_filter(array_map(
            fn ($permiso) => trim((string) $permiso),
            $permisos
        ))));

        if ($codigos === []) {
            return [];
        }

        return Gener40::query()
            ->whereIn('codigo', $codigos)
            ->pluck('codigo')
            ->map(fn ($codigo) => (string) $codigo)
            ->values()
            ->all();
    }
}
