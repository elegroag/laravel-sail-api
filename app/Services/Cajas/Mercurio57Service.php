<?php

namespace App\Services\Cajas;

use App\Models\Mercurio01;
use App\Models\Mercurio57;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class Mercurio57Service
{
    /**
     * @return list<array{numpro: int, archivo: string, archivo_nombre: string|null, url: string, orden: int, estado: string, estado_label: string}>
     */
    public function listar(): array
    {
        $mercurio01 = $this->resolveMercurio01(false);

        return Mercurio57::query()
            ->orderBy('orden')
            ->orderBy('numpro')
            ->get()
            ->map(function (Mercurio57 $item) use ($mercurio01) {
                $nombre = $item->getArchivo();
                $imageUrl = '';

                if ($nombre && $mercurio01) {
                    $imageUrl = $mercurio01->dominioUrl('galeria/'.$nombre);
                }

                if ($mercurio01 && empty($item->getUrl()) && $imageUrl !== '') {
                    $item->setUrl($imageUrl);
                    $item->save();
                }

                return [
                    'numpro' => (int) $item->getNumpro(),
                    'archivo' => $imageUrl,
                    'archivo_nombre' => $nombre,
                    'url' => (string) ($item->getUrl() ?: $imageUrl),
                    'orden' => (int) $item->getOrden(),
                    'estado' => (string) $item->getEstado(),
                    'estado_label' => $item->getEstado() === 'A' ? 'Activo' : 'Inactivo',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array{estado: string}  $data
     * @return array{flag: bool, msj: string}
     */
    public function crear(array $data, UploadedFile $archivo): array
    {
        try {
            DB::beginTransaction();

            $mercurio01 = $this->resolveMercurio01(true);
            $numpro = (int) (Mercurio57::max('numpro') ?? 0) + 1;
            $orden = (int) (Mercurio57::max('orden') ?? 0) + 1;

            $extension = strtolower((string) $archivo->getClientOriginalExtension());
            if (! in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
                throw new RuntimeException('Formato de imagen no permitido. Use JPG, JPEG o PNG.');
            }

            $fileName = 'ME57_'.$numpro.'.'.$extension;
            $destinationPath = public_path($mercurio01->getPath().'galeria');

            if (! File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $archivo->move($destinationPath, $fileName);

            $row = new Mercurio57;
            $row->setNumpro($numpro);
            $row->setOrden($orden);
            $row->setEstado($data['estado']);
            $row->setArchivo($fileName);
            $row->setUrl($mercurio01->dominioUrl('galeria/'.$fileName));
            $row->save();

            DB::commit();

            return ['flag' => true, 'msj' => 'Creación terminada con éxito'];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['flag' => false, 'msj' => 'No se puede guardar el registro: '.$e->getMessage()];
        }
    }

    /**
     * @param  array{estado: string}  $data
     * @return array{flag: bool, msj: string}
     */
    public function actualizar(int $numpro, array $data, ?UploadedFile $archivo = null): array
    {
        try {
            DB::beginTransaction();

            $row = Mercurio57::where('numpro', $numpro)->first();
            if (! $row) {
                throw new RuntimeException('Registro no encontrado.');
            }

            $mercurio01 = $this->resolveMercurio01(true);
            $row->setEstado($data['estado']);

            if ($archivo) {
                $extension = strtolower((string) $archivo->getClientOriginalExtension());
                if (! in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
                    throw new RuntimeException('Formato de imagen no permitido. Use JPG, JPEG o PNG.');
                }

                $fileName = 'ME57_'.$numpro.'.'.$extension;
                $destinationPath = public_path($mercurio01->getPath().'galeria');

                if (! File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $previous = $row->getArchivo();
                if (! empty($previous) && $previous !== $fileName) {
                    $previousPath = public_path($mercurio01->getPath().'galeria/'.$previous);
                    if (File::exists($previousPath)) {
                        File::delete($previousPath);
                    }
                }

                $archivo->move($destinationPath, $fileName);
                $row->setArchivo($fileName);
            }

            if (empty($row->getArchivo())) {
                throw new RuntimeException('El registro no tiene imagen asociada.');
            }

            $row->setUrl($mercurio01->dominioUrl('galeria/'.$row->getArchivo()));
            $row->save();

            DB::commit();

            return ['flag' => true, 'msj' => 'Actualización terminada con éxito'];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['flag' => false, 'msj' => 'No se puede guardar el registro: '.$e->getMessage()];
        }
    }

    /**
     * @return array{flag: bool, msj: string}
     */
    public function borrar(int $numpro): array
    {
        try {
            DB::beginTransaction();

            $row = Mercurio57::where('numpro', $numpro)->first();
            if (! $row) {
                throw new RuntimeException('El registro a borrar no existe.');
            }

            $archivo = $row->getArchivo();
            $mercurio01 = $this->resolveMercurio01(false);

            if ($mercurio01 && ! empty($archivo)) {
                $filePath = public_path($mercurio01->getPath().'galeria/'.$archivo);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }

            $row->delete();
            DB::commit();

            return ['flag' => true, 'msj' => 'Borrado con éxito'];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['flag' => false, 'msj' => 'No se puede borrar el registro: '.$e->getMessage()];
        }
    }

    /**
     * @param  'up'|'down'  $direction
     * @return array{flag: bool, msj: string}
     */
    public function mover(int $numpro, string $direction): array
    {
        try {
            DB::beginTransaction();

            $objetivo = Mercurio57::where('numpro', $numpro)->first();
            if (! $objetivo) {
                throw new RuntimeException('Registro no encontrado.');
            }

            $ordenObj = (int) $objetivo->getOrden();

            if ($direction === 'up') {
                $vecino = Mercurio57::query()
                    ->where('orden', '<', $ordenObj)
                    ->orderByDesc('orden')
                    ->first();
            } else {
                $vecino = Mercurio57::query()
                    ->where('orden', '>', $ordenObj)
                    ->orderBy('orden')
                    ->first();
            }

            if ($vecino) {
                $ordenVecino = (int) $vecino->getOrden();
                $objetivo->orden = $ordenVecino;
                $vecino->orden = $ordenObj;
                $objetivo->save();
                $vecino->save();
            }

            DB::commit();

            return ['flag' => true, 'msj' => 'Ordenado con éxito'];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['flag' => false, 'msj' => 'No se puede ordenar el registro: '.$e->getMessage()];
        }
    }

    public function resolveMercurio01(bool $required = true): ?Mercurio01
    {
        $mercurio01 = Mercurio01::where('codapl', 'MO')->first() ?? Mercurio01::first();

        if ($required && ! $mercurio01) {
            throw new RuntimeException('Configuración básica no encontrada.');
        }

        return $mercurio01;
    }
}
