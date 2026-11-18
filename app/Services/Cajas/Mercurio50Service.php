<?php

namespace App\Services\Cajas;

use App\Models\Mercurio50;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class Mercurio50Service
{
    /**
     * @return LengthAwarePaginator<int, Mercurio50>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = Mercurio50::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('codapl', 'like', $like)
                    ->orWhere('webser', 'like', $like)
                    ->orWhere('path', 'like', $like)
                    ->orWhere('urlonl', 'like', $like)
                    ->orWhere('puncom', 'like', $like);
            });
        }

        return $query
            ->orderBy('codapl')
            ->paginate($perPage)
            ->appends($request->only(['q', 'per_page']));
    }

    /**
     * @param  array{codapl: string, webser: ?string, path: ?string, urlonl: ?string, puncom: ?string|int}  $data
     * @return array{flag: bool, msj: string}
     */
    public function crear(array $data): array
    {
        try {
            DB::beginTransaction();

            $row = new Mercurio50;
            $row->codapl = $data['codapl'];
            $row->webser = $data['webser'] ?? null;
            $row->path = $data['path'] ?? null;
            $row->urlonl = $data['urlonl'] ?? null;
            $row->puncom = $data['puncom'] ?? null;

            if (! $row->save()) {
                DB::rollBack();

                return ['flag' => false, 'msj' => 'No se pudo crear el registro.'];
            }

            DB::commit();

            return ['flag' => true, 'msj' => 'Creación con éxito.'];
        } catch (Throwable $e) {
            DB::rollBack();

            return ['flag' => false, 'msj' => 'No se puede crear el registro: '.$e->getMessage()];
        }
    }

    /**
     * @param  array{webser: ?string, path: ?string, urlonl: ?string, puncom: ?string|int}  $data
     * @return array{flag: bool, msj: string}
     */
    public function actualizar(string $codapl, array $data): array
    {
        try {
            DB::beginTransaction();

            $row = Mercurio50::where('codapl', $codapl)->first();
            if (! $row) {
                DB::rollBack();

                return ['flag' => false, 'msj' => 'El registro no existe.'];
            }

            $row->webser = $data['webser'] ?? null;
            $row->path = $data['path'] ?? null;
            $row->urlonl = $data['urlonl'] ?? null;
            $row->puncom = $data['puncom'] ?? null;

            if (! $row->save()) {
                DB::rollBack();

                return ['flag' => false, 'msj' => 'No se pudo actualizar el registro.'];
            }

            DB::commit();

            return ['flag' => true, 'msj' => 'Actualización con éxito.'];
        } catch (Throwable $e) {
            DB::rollBack();

            return ['flag' => false, 'msj' => 'No se puede actualizar el registro: '.$e->getMessage()];
        }
    }
}
