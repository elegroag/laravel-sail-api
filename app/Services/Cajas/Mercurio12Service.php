<?php

namespace App\Services\Cajas;

use App\Models\Mercurio12;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mercurio12Service
{
    /**
     * @return LengthAwarePaginator<int, Mercurio12>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = Mercurio12::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(function ($sub) use ($like, $q) {
                $sub->where('detalle', 'like', $like);
                if (ctype_digit($q)) {
                    $sub->orWhere('coddoc', (int) $q);
                }
            });
        }

        return $query
            ->orderBy('coddoc')
            ->paginate($perPage)
            ->appends($request->only(['q', 'per_page']));
    }

    /**
     * @param  array{coddoc: int, detalle: string}  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardar(array $data, ?int $originalCoddoc = null): array
    {
        try {
            DB::beginTransaction();

            $key = $originalCoddoc ?? $data['coddoc'];
            $row = Mercurio12::find($key) ?? new Mercurio12;
            $row->coddoc = $data['coddoc'];
            $row->detalle = $data['detalle'];
            $row->save();

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
                'msg' => 'No se puede guardar/editar el registro: '.$e->getMessage(),
                'msj' => 'No se puede guardar/editar el registro: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function borrar(int $coddoc): array
    {
        try {
            DB::beginTransaction();
            Mercurio12::where('coddoc', $coddoc)->delete();
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
                'msg' => 'No se puede borrar el registro: '.$e->getMessage(),
                'msj' => 'No se puede borrar el registro: '.$e->getMessage(),
            ];
        }
    }
}
