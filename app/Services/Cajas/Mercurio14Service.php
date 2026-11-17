<?php

namespace App\Services\Cajas;

use App\Models\Mercurio09;
use App\Models\Mercurio12;
use App\Models\Mercurio14;
use App\Models\Subsi54;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mercurio14Service
{
    /**
     * @return LengthAwarePaginator<int, Mercurio14>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = Mercurio14::query()
            ->with(['mercurio09', 'mercurio12', 'subsi54']);

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(function ($sub) use ($like, $q) {
                $sub->where('tipopc', 'like', $like)
                    ->orWhere('tipsoc', 'like', $like)
                    ->orWhere('nota', 'like', $like)
                    ->orWhereHas('mercurio09', fn ($r) => $r->where('detalle', 'like', $like))
                    ->orWhereHas('mercurio12', fn ($r) => $r->where('detalle', 'like', $like))
                    ->orWhereHas('subsi54', fn ($r) => $r->where('detalle', 'like', $like));

                if (ctype_digit($q)) {
                    $sub->orWhere('coddoc', (int) $q);
                }
            });
        }

        return $query
            ->orderBy('tipopc')
            ->orderBy('tipsoc')
            ->orderBy('coddoc')
            ->paginate($perPage)
            ->appends($request->only(['q', 'per_page']));
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
     * @return list<array{value: string, label: string}>
     */
    public function opcionesTipsoc(): array
    {
        return Subsi54::query()
            ->orderBy('tipsoc')
            ->get(['tipsoc', 'detalle'])
            ->map(fn (Subsi54 $row) => [
                'value' => (string) $row->tipsoc,
                'label' => (string) $row->detalle,
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{value: int, label: string}>
     */
    public function opcionesCoddoc(): array
    {
        return Mercurio12::query()
            ->orderBy('coddoc')
            ->get(['coddoc', 'detalle'])
            ->map(fn (Mercurio12 $row) => [
                'value' => (int) $row->coddoc,
                'label' => (string) $row->detalle,
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array{tipopc: string, tipsoc: string, coddoc: int, obliga: string, auto_generado: int, nota: ?string}  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardar(array $data, bool $isUpdate = false): array
    {
        try {
            DB::beginTransaction();

            if ($isUpdate) {
                $row = Mercurio14::where('tipopc', $data['tipopc'])
                    ->where('tipsoc', $data['tipsoc'])
                    ->where('coddoc', $data['coddoc'])
                    ->firstOrFail();
            } else {
                $exists = Mercurio14::where('tipopc', $data['tipopc'])
                    ->where('tipsoc', $data['tipsoc'])
                    ->where('coddoc', $data['coddoc'])
                    ->exists();

                if ($exists) {
                    DB::rollBack();

                    return [
                        'flag' => false,
                        'msg' => 'Ya existe un registro con esa combinación de afiliación, sociedad y documento.',
                        'msj' => 'Ya existe un registro con esa combinación de afiliación, sociedad y documento.',
                    ];
                }

                $row = new Mercurio14;
                $row->tipopc = $data['tipopc'];
                $row->tipsoc = $data['tipsoc'];
                $row->coddoc = $data['coddoc'];
            }

            $row->obliga = $data['obliga'];
            $row->auto_generado = $data['auto_generado'];
            $row->nota = $data['nota'];
            $row->save();

            DB::commit();

            return [
                'flag' => true,
                'msg' => 'El registro se completó con éxito.',
                'msj' => 'El registro se completó con éxito.',
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
    public function borrar(string $tipopc, string $tipsoc, int $coddoc): array
    {
        try {
            DB::beginTransaction();

            $deleted = Mercurio14::where('tipopc', $tipopc)
                ->where('tipsoc', $tipsoc)
                ->where('coddoc', $coddoc)
                ->delete();

            if ($deleted === 0) {
                DB::rollBack();

                return [
                    'flag' => false,
                    'msg' => 'No se puede borrar el registro; no está disponible.',
                    'msj' => 'No se puede borrar el registro; no está disponible.',
                ];
            }

            DB::commit();

            return [
                'flag' => true,
                'msg' => 'El registro se borró con éxito.',
                'msj' => 'El registro se borró con éxito.',
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
