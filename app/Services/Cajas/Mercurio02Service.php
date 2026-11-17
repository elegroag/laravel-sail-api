<?php

namespace App\Services\Cajas;

use App\Models\Mercurio02;
use App\Services\Api\ApiSubsidio;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Mercurio02Service
{
    /**
     * @return LengthAwarePaginator<int, Mercurio02>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = Mercurio02::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('codcaj', 'like', $like)
                    ->orWhere('nit', 'like', $like)
                    ->orWhere('razsoc', 'like', $like)
                    ->orWhere('sigla', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        return $query
            ->orderBy('codcaj')
            ->paginate($perPage)
            ->appends($request->only(['q', 'per_page']));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardar(array $data, ?string $originalCodcaj = null): array
    {
        try {
            DB::beginTransaction();

            $key = $originalCodcaj ?: ($data['codcaj'] ?? null);
            $mercurio02 = $key ? Mercurio02::find($key) : null;
            $mercurio02 ??= new Mercurio02;

            $mercurio02->fill([
                'codcaj' => $data['codcaj'] ?? $mercurio02->codcaj,
                'nit' => $data['nit'] ?? null,
                'razsoc' => $data['razsoc'] ?? null,
                'sigla' => $data['sigla'] ?? null,
                'email' => $data['email'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'codciu' => $data['codciu'] ?? null,
                'pagweb' => $data['pagweb'] ?? null,
                'pagfac' => $data['pagfac'] ?? null,
                'pagtwi' => $data['pagtwi'] ?? null,
                'pagyou' => $data['pagyou'] ?? null,
            ]);
            $mercurio02->save();

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
                'msg' => 'No se pudo guardar/editar el registro: '.$e->getMessage(),
                'msj' => 'No se pudo guardar/editar el registro: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @return array<string, string>
     */
    public function obtenerCiudades(): array
    {
        try {
            $apiRest = new ApiSubsidio;
            $apiRest->send([
                'servicio' => 'ComfacaAfilia',
                'metodo' => 'listar_ciudades_departamentos',
            ]);

            $data = $apiRest->toArray();
            $ciudades = $data['ciudades'] ?? [];

            $options = [];
            if (is_array($ciudades)) {
                foreach ($ciudades as $ciudad) {
                    if (! is_array($ciudad) || empty($ciudad['codciu'])) {
                        continue;
                    }
                    $options[(string) $ciudad['codciu']] = (string) ($ciudad['detciu'] ?? $ciudad['codciu']);
                }
            }

            return $options;
        } catch (\Throwable $e) {
            Log::warning('Mercurio02: no se pudieron cargar ciudades', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
