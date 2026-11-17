<?php

namespace App\Services\Cajas;

use App\Models\Mercurio09;
use App\Models\Mercurio12;
use App\Models\Mercurio13;
use App\Models\Mercurio14;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mercurio09Service
{
    /** tipopc sin documentos de empresa en UI legacy */
    public const TIPOPC_SIN_EMPRESA = ['1', '3', '4', '7'];

    public function __construct(
        protected Mercurio13Service $mercurio13Service,
        protected Mercurio14Service $mercurio14Service,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Mercurio09>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = Mercurio09::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('tipopc', 'like', $like)
                    ->orWhere('detalle', 'like', $like);

                if (ctype_digit(ltrim($q, '0')) || ctype_digit($q)) {
                    $sub->orWhere('dias', (int) $q);
                }
            });
        }

        return $query
            ->orderBy('tipopc')
            ->paginate($perPage)
            ->appends($request->only(['q', 'per_page']));
    }

    /**
     * @param  array{tipopc: string, detalle: string, dias: int|null}  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardar(array $data, ?string $originalTipopc = null): array
    {
        try {
            DB::beginTransaction();

            $isUpdate = $originalTipopc !== null;

            if ($isUpdate) {
                $row = Mercurio09::find($originalTipopc);
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
                    'dias' => $data['dias'],
                ]);
                $row->save();
            } else {
                if (Mercurio09::where('tipopc', $data['tipopc'])->exists()) {
                    DB::rollBack();

                    return [
                        'flag' => false,
                        'msg' => 'El código ya existe.',
                        'msj' => 'El código ya existe.',
                    ];
                }

                Mercurio09::create([
                    'tipopc' => $data['tipopc'],
                    'detalle' => $data['detalle'],
                    'dias' => $data['dias'],
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
    public function borrar(string $tipopc): array
    {
        try {
            DB::beginTransaction();

            $row = Mercurio09::find($tipopc);
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

    public function allowsEmpresa(string $tipopc): bool
    {
        return ! in_array($tipopc, self::TIPOPC_SIN_EMPRESA, true);
    }

    /**
     * @return list<array{coddoc: int, detalle: string, adjunto: bool, obliga: string, auto_generado: int, nota: string|null}>
     */
    public function documentosTrabajador(string $tipopc): array
    {
        $cruces = Mercurio13::query()
            ->where('tipopc', $tipopc)
            ->get()
            ->keyBy(fn (Mercurio13 $row) => (int) $row->coddoc);

        return Mercurio12::query()
            ->orderBy('coddoc')
            ->get(['coddoc', 'detalle'])
            ->map(function (Mercurio12 $doc) use ($cruces) {
                $coddoc = (int) $doc->coddoc;
                /** @var Mercurio13|null $cruce */
                $cruce = $cruces->get($coddoc);

                return [
                    'coddoc' => $coddoc,
                    'detalle' => (string) $doc->detalle,
                    'adjunto' => $cruce !== null,
                    'obliga' => $cruce?->obliga === 'S' ? 'S' : 'N',
                    'auto_generado' => (int) ($cruce?->auto_generado ?? 0),
                    'nota' => $cruce?->nota,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<array{coddoc: int, detalle: string, adjunto: bool, obliga: string, auto_generado: int, nota: string|null}>
     */
    public function documentosEmpresa(string $tipopc, string $tipsoc): array
    {
        $cruces = Mercurio14::query()
            ->where('tipopc', $tipopc)
            ->where('tipsoc', $tipsoc)
            ->get()
            ->keyBy(fn (Mercurio14 $row) => (int) $row->coddoc);

        return Mercurio12::query()
            ->orderBy('coddoc')
            ->get(['coddoc', 'detalle'])
            ->map(function (Mercurio12 $doc) use ($cruces) {
                $coddoc = (int) $doc->coddoc;
                /** @var Mercurio14|null $cruce */
                $cruce = $cruces->get($coddoc);

                return [
                    'coddoc' => $coddoc,
                    'detalle' => (string) $doc->detalle,
                    'adjunto' => $cruce !== null,
                    'obliga' => $cruce?->obliga === 'S' ? 'S' : 'N',
                    'auto_generado' => (int) ($cruce?->auto_generado ?? 0),
                    'nota' => $cruce?->nota,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function syncTrabajador(string $tipopc, int $coddoc, bool $adjunto, ?string $obliga = null): array
    {
        if (! $adjunto) {
            return $this->mercurio13Service->borrar($tipopc, $coddoc);
        }

        $existing = Mercurio13::where('tipopc', $tipopc)->where('coddoc', $coddoc)->first();
        $isUpdate = $existing !== null;

        return $this->mercurio13Service->guardar([
            'tipopc' => $tipopc,
            'coddoc' => $coddoc,
            'obliga' => $obliga ?? ($existing?->obliga ?? 'N'),
            'auto_generado' => (int) ($existing?->auto_generado ?? 0),
            'nota' => $existing?->nota,
        ], $isUpdate);
    }

    /**
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function syncEmpresa(string $tipopc, string $tipsoc, int $coddoc, bool $adjunto, ?string $obliga = null): array
    {
        if (! $this->allowsEmpresa($tipopc)) {
            return [
                'flag' => false,
                'msg' => 'Este tipo de opción no admite documentos de empresa.',
                'msj' => 'Este tipo de opción no admite documentos de empresa.',
            ];
        }

        if (! $adjunto) {
            return $this->mercurio14Service->borrar($tipopc, $tipsoc, $coddoc);
        }

        $existing = Mercurio14::where('tipopc', $tipopc)
            ->where('tipsoc', $tipsoc)
            ->where('coddoc', $coddoc)
            ->first();
        $isUpdate = $existing !== null;

        return $this->mercurio14Service->guardar([
            'tipopc' => $tipopc,
            'tipsoc' => $tipsoc,
            'coddoc' => $coddoc,
            'obliga' => $obliga ?? ($existing?->obliga ?? 'N'),
            'auto_generado' => (int) ($existing?->auto_generado ?? 0),
            'nota' => $existing?->nota,
        ], $isUpdate);
    }
}
