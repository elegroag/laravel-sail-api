<?php

namespace App\Services\Cajas;

use App\Models\Gener40;
use App\Models\Gener42;
use App\Models\MenuItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Gener40Service
{
    /**
     * @return LengthAwarePaginator<int, Gener40>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = Gener40::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('codigo', 'like', $like)
                    ->orWhere('detalle', 'like', $like)
                    ->orWhere('controller', 'like', $like)
                    ->orWhere('action', 'like', $like);
            });
        }

        return $query
            ->withCount('asignaciones')
            ->orderByRaw('orden IS NULL')
            ->orderBy('orden')
            ->orderBy('codigo')
            ->paginate($perPage)
            ->appends($request->only(['q', 'per_page']));
    }

    /**
     * Todos los registros para exportar a Excel (sin paginación).
     *
     * @return list<array{0: string, 1: string, 2: string, 3: string, 4: int|string}>
     */
    public function exportRows(): array
    {
        return Gener40::query()
            ->orderByRaw('orden IS NULL')
            ->orderBy('orden')
            ->orderBy('codigo')
            ->get(['codigo', 'detalle', 'controller', 'action', 'orden'])
            ->map(fn (Gener40 $row) => [
                (string) $row->codigo,
                (string) ($row->detalle ?? ''),
                (string) ($row->controller ?? ''),
                (string) ($row->action ?? ''),
                $row->orden !== null ? (int) $row->orden : '',
            ])
            ->values()
            ->all();
    }

    /**
     * Controllers y actions disponibles desde el menú de Cajas.
     *
     * @return array{controllers: list<array{value: string, label: string}>, actions: list<array{value: string, label: string}>}
     */
    public function opcionesRuta(): array
    {
        $controllers = MenuItem::query()
            ->whereNotNull('controller')
            ->where('controller', '!=', '')
            ->distinct()
            ->orderBy('controller')
            ->pluck('controller')
            ->map(fn ($value) => ['value' => (string) $value, 'label' => (string) $value])
            ->values()
            ->all();

        $actions = MenuItem::query()
            ->whereNotNull('action')
            ->where('action', '!=', '')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->map(fn ($value) => ['value' => (string) $value, 'label' => (string) $value])
            ->values()
            ->all();

        return [
            'controllers' => $controllers,
            'actions' => $actions,
        ];
    }

    /**
     * @param  array{codigo: string, detalle: string, controller: ?string, action: ?string, orden: ?int}  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardar(array $data, ?string $originalCodigo = null): array
    {
        try {
            DB::beginTransaction();

            $isUpdate = $originalCodigo !== null;

            if ($isUpdate) {
                $row = Gener40::find($originalCodigo);
                if (! $row) {
                    DB::rollBack();

                    return [
                        'flag' => false,
                        'msg' => 'Registro no encontrado.',
                        'msj' => 'Registro no encontrado.',
                    ];
                }

                $row->fill([
                    'detalle' => $data['detalle'],
                    'controller' => $data['controller'],
                    'action' => $data['action'],
                    'orden' => $data['orden'],
                ]);
                $row->save();
            } else {
                if (Gener40::where('codigo', $data['codigo'])->exists()) {
                    DB::rollBack();

                    return [
                        'flag' => false,
                        'msg' => 'El código ya existe.',
                        'msj' => 'El código ya existe.',
                    ];
                }

                Gener40::create([
                    'codigo' => $data['codigo'],
                    'detalle' => $data['detalle'],
                    'controller' => $data['controller'],
                    'action' => $data['action'],
                    'orden' => $data['orden'],
                ]);
            }

            DB::commit();

            return [
                'flag' => true,
                'msg' => 'Operación realizada con éxito',
                'msj' => 'Operación realizada con éxito',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'flag' => false,
                'msg' => 'No se puede guardar/editar el Registro: '.$e->getMessage(),
                'msj' => 'No se puede guardar/editar el Registro: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function borrar(string $codigo): array
    {
        try {
            DB::beginTransaction();

            $row = Gener40::find($codigo);
            if (! $row) {
                DB::rollBack();

                return [
                    'flag' => false,
                    'msg' => 'El registro a borrar no existe.',
                    'msj' => 'El registro a borrar no existe.',
                ];
            }

            $asignaciones = Gener42::query()->where('permiso', $codigo)->count();
            if ($asignaciones > 0) {
                Gener42::query()->where('permiso', $codigo)->delete();
            }

            $row->delete();
            DB::commit();

            $msj = $asignaciones > 0
                ? "Borrado con éxito. Se revocaron {$asignaciones} asignación(es) de usuarios."
                : 'Borrado con éxito';

            return [
                'flag' => true,
                'msg' => $msj,
                'msj' => $msj,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'flag' => false,
                'msg' => 'No se puede borrar el Registro: '.$e->getMessage(),
                'msj' => 'No se puede borrar el Registro: '.$e->getMessage(),
            ];
        }
    }
}
