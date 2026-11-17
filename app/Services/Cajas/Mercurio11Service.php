<?php

namespace App\Services\Cajas;

use App\Models\Mercurio11;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mercurio11Service
{
    /**
     * @return LengthAwarePaginator<int, Mercurio11>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = Mercurio11::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('codest', 'like', $like)
                    ->orWhere('detalle', 'like', $like);
            });
        }

        return $query
            ->orderBy('codest')
            ->paginate($perPage)
            ->appends($request->only(['q', 'per_page']));
    }

    /**
     * @param  array{codest: string, detalle: string}  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardar(array $data, ?string $originalCodest = null): array
    {
        try {
            DB::beginTransaction();

            $isUpdate = $originalCodest !== null;

            if ($isUpdate) {
                $row = Mercurio11::find($originalCodest);
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
                ]);
                $row->save();
            } else {
                if (Mercurio11::where('codest', $data['codest'])->exists()) {
                    DB::rollBack();

                    return [
                        'flag' => false,
                        'msg' => 'El código ya existe.',
                        'msj' => 'El código ya existe.',
                    ];
                }

                Mercurio11::create([
                    'codest' => $data['codest'],
                    'detalle' => $data['detalle'],
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
    public function borrar(string $codest): array
    {
        try {
            DB::beginTransaction();

            $row = Mercurio11::find($codest);
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
}
