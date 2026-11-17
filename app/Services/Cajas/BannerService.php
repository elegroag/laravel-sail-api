<?php

namespace App\Services\Cajas;

use App\Models\Banner;
use App\Models\Mercurio01;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BannerService
{
    /**
     * @return LengthAwarePaginator<int, Banner>
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = Banner::query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('content_html', 'like', $like)
                    ->orWhere('url_imagen', 'like', $like)
                    ->orWhere('imagen', 'like', $like)
                    ->orWhere('estado', 'like', $like);
            });
        }

        $estado = trim((string) $request->query('estado', ''));
        if (in_array($estado, ['A', 'I'], true)) {
            $query->where('estado', $estado);
        }

        return $query
            ->orderByDesc('fecha_inicia')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->only(['q', 'estado', 'per_page']));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function mapCollection(iterable $banners): array
    {
        $mercurio01 = $this->resolveMercurio01(false);

        return collect($banners)
            ->map(fn (Banner $banner) => $this->mapRecord($banner, $mercurio01))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardar(array $data, ?int $id = null, ?UploadedFile $imagen = null): array
    {
        try {
            DB::beginTransaction();

            $mercurio01 = $this->resolveMercurio01(true);
            $isUpdate = $id !== null;

            if ($isUpdate) {
                $banner = Banner::find($id);
                if (! $banner) {
                    throw new RuntimeException('Registro no encontrado.');
                }
            } else {
                $banner = new Banner;
            }

            $fechaInicia = Carbon::parse($data['fecha_inicia'])->startOfDay();
            $fechaFinaliza = Carbon::parse($data['fecha_finaliza'])->startOfDay();

            if ($fechaFinaliza->lt($fechaInicia)) {
                throw new RuntimeException('La fecha de finalización debe ser mayor o igual a la de inicio.');
            }

            $urlImagen = trim((string) ($data['url_imagen'] ?? ''));
            $banner->setEstado($data['estado'] ?? 'A');
            $banner->setContentHtml(Banner::sanitizeHtml($data['content_html'] ?? null));
            $banner->setFechaInicia($fechaInicia->toDateString());
            $banner->setFechaFinaliza($fechaFinaliza->toDateString());
            $banner->setUrlImagen($urlImagen !== '' ? $urlImagen : null);

            if ($imagen instanceof UploadedFile && $imagen->isValid()) {
                if (! $isUpdate) {
                    $banner->save();
                }
                $this->replaceImagen($imagen, $banner, $mercurio01, (int) $banner->getId(), $isUpdate);
            } elseif (! $isUpdate && $urlImagen === '') {
                throw new RuntimeException('Debe cargar una imagen o indicar una URL de imagen.');
            } elseif ($isUpdate && empty($banner->getImagen()) && $urlImagen === '') {
                throw new RuntimeException('El registro no tiene imagen asociada. Cargue un archivo o una URL.');
            }

            $banner->save();
            DB::commit();

            return [
                'flag' => true,
                'msg' => $isUpdate ? 'Actualización terminada con éxito' : 'Creación terminada con éxito',
                'msj' => $isUpdate ? 'Actualización terminada con éxito' : 'Creación terminada con éxito',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'flag' => false,
                'msg' => 'No se puede guardar el Registro: '.$e->getMessage(),
                'msj' => 'No se puede guardar el Registro: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function borrar(int $id): array
    {
        try {
            DB::beginTransaction();

            $banner = Banner::find($id);
            if (! $banner) {
                throw new RuntimeException('El registro a borrar no existe.');
            }

            $archivo = $banner->getImagen();
            if (! empty($archivo)) {
                $mercurio01 = $this->resolveMercurio01(true);
                $filePath = public_path($mercurio01->getPath().'galeria/'.$archivo);
                if (is_file($filePath)) {
                    unlink($filePath);
                }
            }

            $banner->delete();
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
                'msg' => 'No se puede Borrar el Registro: '.$e->getMessage(),
                'msj' => 'No se puede Borrar el Registro: '.$e->getMessage(),
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function mapRecord(Banner $item, ?Mercurio01 $mercurio01 = null): array
    {
        $fechaInicia = $item->getFechaInicia();
        $fechaFinaliza = $item->getFechaFinaliza();

        return [
            'id' => (int) $item->getId(),
            'content_html' => $item->getContentHtml(),
            'imagen' => $item->resolveImageUrl($mercurio01),
            'imagen_nombre' => $item->getImagen(),
            'url_imagen' => $item->getUrlImagen(),
            'fecha_inicia' => $fechaInicia ? Carbon::parse($fechaInicia)->toDateString() : null,
            'fecha_finaliza' => $fechaFinaliza ? Carbon::parse($fechaFinaliza)->toDateString() : null,
            'estado' => $item->getEstado(),
            'estado_label' => $item->getEstado() === 'A' ? 'Activo' : 'Inactivo',
        ];
    }

    public function resolveMercurio01(bool $required = true): ?Mercurio01
    {
        $mercurio01 = Mercurio01::where('codapl', 'MO')->first() ?? Mercurio01::first();

        if ($required && ! $mercurio01) {
            throw new RuntimeException('Configuración básica no encontrada.');
        }

        return $mercurio01;
    }

    protected function replaceImagen(
        UploadedFile $file,
        Banner $banner,
        Mercurio01 $mercurio01,
        int $id,
        bool $isUpdate
    ): void {
        $extension = strtolower((string) $file->getClientOriginalExtension());
        if (! in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            throw new RuntimeException('Formato de imagen no permitido. Use JPG, JPEG o PNG.');
        }

        $fileName = 'BANN_'.$id.'.'.$extension;
        $destinationPath = public_path($mercurio01->getPath().'galeria');

        if (! is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        if ($isUpdate) {
            $previousFile = $banner->getImagen();
            if (! empty($previousFile) && $previousFile !== $fileName) {
                $previousPath = public_path($mercurio01->getPath().'galeria/'.$previousFile);
                if (is_file($previousPath)) {
                    unlink($previousPath);
                }
            }
        }

        $file->move($destinationPath, $fileName);
        $banner->setImagen($fileName);
    }
}
