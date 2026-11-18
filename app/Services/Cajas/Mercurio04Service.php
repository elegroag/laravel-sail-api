<?php

namespace App\Services\Cajas;

use App\Models\Gener02;
use App\Models\Mercurio04;
use App\Models\Mercurio08;
use App\Models\Mercurio09;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mercurio04Service
{
    /**
     * @return LengthAwarePaginator<int, Mercurio04>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = Mercurio04::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('codofi', 'like', $like)
                    ->orWhere('detalle', 'like', $like)
                    ->orWhere('principal', 'like', $like)
                    ->orWhere('estado', 'like', $like);
            });
        }

        return $query
            ->orderBy('codofi')
            ->paginate($perPage)
            ->appends($request->only(['q', 'per_page']));
    }

    /**
     * @param  array{codofi: string, detalle: string, principal: string, estado: string}  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardar(array $data, ?string $originalCodofi = null): array
    {
        try {
            DB::beginTransaction();

            $isUpdate = $originalCodofi !== null;

            if ($isUpdate) {
                $row = Mercurio04::find($originalCodofi);
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
                    'principal' => $data['principal'],
                    'estado' => $data['estado'],
                ]);
                $row->save();
            } else {
                if (Mercurio04::where('codofi', $data['codofi'])->exists()) {
                    DB::rollBack();

                    return [
                        'flag' => false,
                        'msg' => 'El código ya existe.',
                        'msj' => 'El código ya existe.',
                    ];
                }

                Mercurio04::create([
                    'codofi' => $data['codofi'],
                    'detalle' => $data['detalle'],
                    'principal' => $data['principal'],
                    'estado' => $data['estado'],
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
    public function borrar(string $codofi): array
    {
        try {
            DB::beginTransaction();

            $row = Mercurio04::find($codofi);
            if (! $row) {
                DB::rollBack();

                return [
                    'flag' => false,
                    'msg' => 'El registro a borrar no existe.',
                    'msj' => 'El registro a borrar no existe.',
                ];
            }

            $row->delete();
            DB::commit();

            return [
                'flag' => true,
                'msg' => 'Borrado con éxito',
                'msj' => 'Borrado con éxito',
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

    /**
     * @return list<array{value: string, label: string}>
     */
    public function opcionesTipopc(): array
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
     * @return list<array{value: int|string, label: string}>
     */
    public function opcionesUsuarios(): array
    {
        return Gener02::query()
            ->where('estado', 'A')
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
     * @return list<array{codofi: string, tipopc: string, tipopc_detalle: string, usuario: int, usuario_nombre: string, orden: int}>
     */
    public function asignaciones(string $codofi): array
    {
        return Mercurio08::query()
            ->where('codofi', $codofi)
            ->orderBy('tipopc')
            ->orderBy('orden')
            ->orderBy('usuario')
            ->get()
            ->map(function (Mercurio08 $row) {
                $tipopc = Mercurio09::find($row->tipopc);
                $usuario = Gener02::where('usuario', $row->usuario)->first();

                return [
                    'codofi' => (string) $row->codofi,
                    'tipopc' => (string) $row->tipopc,
                    'tipopc_detalle' => $tipopc ? (string) $tipopc->detalle : '',
                    'usuario' => (int) $row->usuario,
                    'usuario_nombre' => $usuario ? (string) $usuario->nombre : '',
                    'orden' => (int) ($row->orden ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardarOpcion(string $codofi, string $tipopc, int $usuario): array
    {
        try {
            DB::beginTransaction();

            $exists = Mercurio08::query()
                ->where('codofi', $codofi)
                ->where('tipopc', $tipopc)
                ->where('usuario', $usuario)
                ->exists();

            if ($exists) {
                DB::rollBack();

                return [
                    'flag' => false,
                    'msg' => 'La asignación ya existe.',
                    'msj' => 'La asignación ya existe.',
                ];
            }

            $maxOrden = Mercurio08::query()
                ->where('codofi', $codofi)
                ->where('tipopc', $tipopc)
                ->max('orden');

            Mercurio08::create([
                'codofi' => $codofi,
                'tipopc' => $tipopc,
                'usuario' => $usuario,
                'orden' => ((int) $maxOrden) + 1,
            ]);

            DB::commit();

            return [
                'flag' => true,
                'msg' => 'Asignación guardada con éxito',
                'msj' => 'Asignación guardada con éxito',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'flag' => false,
                'msg' => 'No se pudo guardar la asignación: '.$e->getMessage(),
                'msj' => 'No se pudo guardar la asignación: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function borrarOpcion(string $codofi, string $tipopc, int $usuario): array
    {
        try {
            DB::beginTransaction();

            $deleted = Mercurio08::query()
                ->where('codofi', $codofi)
                ->where('tipopc', $tipopc)
                ->where('usuario', $usuario)
                ->delete();

            if ($deleted === 0) {
                DB::rollBack();

                return [
                    'flag' => false,
                    'msg' => 'La asignación no existe.',
                    'msj' => 'La asignación no existe.',
                ];
            }

            DB::commit();

            return [
                'flag' => true,
                'msg' => 'Asignación eliminada con éxito',
                'msj' => 'Asignación eliminada con éxito',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'flag' => false,
                'msg' => 'No se pudo eliminar la asignación: '.$e->getMessage(),
                'msj' => 'No se pudo eliminar la asignación: '.$e->getMessage(),
            ];
        }
    }
}
