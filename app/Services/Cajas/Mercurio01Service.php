<?php

namespace App\Services\Cajas;

use App\Models\Mercurio01;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mercurio01Service
{
    /**
     * @return LengthAwarePaginator<int, Mercurio01>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = Mercurio01::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('codapl', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('path', 'like', $like)
                    ->orWhere('ftpserver', 'like', $like);
            });
        }

        return $query
            ->orderBy('codapl')
            ->paginate($perPage)
            ->appends($request->only(['q', 'per_page']));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardar(array $data, ?string $originalCodapl = null): array
    {
        try {
            DB::beginTransaction();

            $key = $originalCodapl ?: ($data['codapl'] ?? null);
            $mercurio01 = $key ? Mercurio01::find($key) : null;
            $mercurio01 ??= new Mercurio01;

            $mercurio01->fill([
                'codapl' => $data['codapl'] ?? $mercurio01->codapl,
                'email' => $data['email'] ?? null,
                'clave' => $data['clave'] ?? null,
                'path' => $data['path'] ?? null,
                'ftpserver' => $data['ftpserver'] ?? null,
                'pathserver' => $data['pathserver'] ?? null,
                'userserver' => $data['userserver'] ?? null,
                'passserver' => $data['passserver'] ?? null,
            ]);
            $mercurio01->save();

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
     * @return array{success: bool, data: array<string, mixed>|null}
     */
    public function editar(?string $codapl = null): array
    {
        $mercurio01 = $codapl
            ? Mercurio01::find($codapl)
            : Mercurio01::first();

        if (! $mercurio01) {
            return ['success' => false, 'data' => null];
        }

        return ['success' => true, 'data' => $mercurio01->toArray()];
    }
}
