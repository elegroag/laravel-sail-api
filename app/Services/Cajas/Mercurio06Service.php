<?php

namespace App\Services\Cajas;

use App\Models\Mercurio06;
use App\Models\Mercurio28;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mercurio06Service
{
    /**
     * @return LengthAwarePaginator<int, Mercurio06>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = Mercurio06::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('tipo', 'like', $like)
                    ->orWhere('detalle', 'like', $like);
            });
        }

        return $query
            ->orderBy('tipo')
            ->paginate($perPage)
            ->appends($request->only(['q', 'per_page', 'campos_tipo']));
    }

    /**
     * @param  array{tipo: string, detalle: string}  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardarTipo(array $data, ?string $originalTipo = null): array
    {
        try {
            DB::beginTransaction();

            $key = $originalTipo ?: $data['tipo'];
            $row = Mercurio06::find($key) ?? new Mercurio06;
            $row->tipo = $data['tipo'];
            $row->detalle = $data['detalle'];
            $row->save();

            DB::commit();

            return [
                'flag' => true,
                'msg' => 'Operación exitosa',
                'msj' => 'Operación exitosa',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'flag' => false,
                'msg' => 'No se pudo guardar el registro: '.$e->getMessage(),
                'msj' => 'No se pudo guardar el registro: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function borrarTipo(string $tipo): array
    {
        try {
            DB::beginTransaction();
            Mercurio06::where('tipo', $tipo)->delete();
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

    /**
     * @return list<array{tipo: string, campo: string, detalle: string, orden: int}>
     */
    public function listarCampos(string $tipo): array
    {
        return Mercurio28::where('tipo', $tipo)
            ->orderBy('orden')
            ->orderBy('campo')
            ->get()
            ->map(fn (Mercurio28 $row) => [
                'tipo' => $row->tipo,
                'campo' => $row->campo,
                'detalle' => $row->detalle,
                'orden' => (int) $row->orden,
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array{tipo: string, campo: string, detalle: string, orden: int}  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardarCampo(array $data, ?string $originalCampo = null): array
    {
        try {
            DB::beginTransaction();

            $campoKey = $originalCampo ?: $data['campo'];
            $row = Mercurio28::where('tipo', $data['tipo'])
                ->where('campo', $campoKey)
                ->first() ?? new Mercurio28;

            $row->tipo = $data['tipo'];
            $row->campo = $data['campo'];
            $row->detalle = $data['detalle'];
            $row->orden = $data['orden'];
            $row->save();

            DB::commit();

            return [
                'flag' => true,
                'msg' => 'Movimiento realizado con éxito',
                'msj' => 'Movimiento realizado con éxito',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'flag' => false,
                'msg' => 'No se pudo realizar el movimiento: '.$e->getMessage(),
                'msj' => 'No se pudo realizar el movimiento: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function borrarCampo(string $tipo, string $campo): array
    {
        try {
            DB::beginTransaction();
            Mercurio28::where('tipo', $tipo)->where('campo', $campo)->delete();
            DB::commit();

            return [
                'flag' => true,
                'msg' => 'Movimiento realizado con éxito',
                'msj' => 'Movimiento realizado con éxito',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'flag' => false,
                'msg' => 'No se pudo realizar el movimiento: '.$e->getMessage(),
                'msj' => 'No se pudo realizar el movimiento: '.$e->getMessage(),
            ];
        }
    }
}
