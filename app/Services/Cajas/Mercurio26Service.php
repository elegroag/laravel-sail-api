<?php

namespace App\Services\Cajas;

use App\Models\Mercurio01;
use App\Models\Mercurio26;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class Mercurio26Service
{
    /**
     * @return list<array{numero: int, archivo: string, archivo_nombre: string|null, tipo: string, tipo_label: string, nota: string|null, orden: int, estado: string}>
     */
    public function listar(): array
    {
        $mercurio01 = $this->resolveMercurio01(false);

        return Mercurio26::query()
            ->orderBy('orden')
            ->orderBy('numero')
            ->get()
            ->map(function (Mercurio26 $item) use ($mercurio01) {
                $nombre = $item->getArchivo();
                $archivoUrl = '';

                if ($nombre && $mercurio01) {
                    $archivoUrl = $mercurio01->dominioUrl('galeria/'.$nombre);
                }

                return [
                    'numero' => (int) $item->getNumero(),
                    'archivo' => $archivoUrl,
                    'archivo_nombre' => $nombre,
                    'tipo' => (string) $item->getTipo(),
                    'tipo_label' => $item->getTipo() === 'V' ? 'Video' : 'Foto',
                    'nota' => $item->getNota(),
                    'orden' => (int) $item->getOrden(),
                    'estado' => (string) $item->getEstado(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array{tipo: string, nota: ?string}  $data
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function guardar(array $data, UploadedFile $archivo): array
    {
        try {
            DB::beginTransaction();

            $mercurio01 = $this->resolveMercurio01(true);
            $numero = (int) (Mercurio26::max('numero') ?? 0) + 1;
            $orden = (int) (Mercurio26::max('orden') ?? 0) + 1;
            $tipo = $data['tipo'];

            $extension = strtolower((string) $archivo->getClientOriginalExtension());
            if ($tipo === 'V') {
                if ($extension !== 'mp4') {
                    throw new RuntimeException('Formato de video no permitido. Use MP4.');
                }
            } elseif (! in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
                throw new RuntimeException('Formato de imagen no permitido. Use JPG, JPEG o PNG.');
            }

            $fileName = 'promo_'.$numero.'.'.$extension;
            $destinationPath = public_path($mercurio01->getPath().'galeria');

            if (! File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $archivo->move($destinationPath, $fileName);

            $row = new Mercurio26;
            $row->setNumero($numero);
            $row->setOrden($orden);
            $row->setTipo($tipo);
            $row->setEstado('A');
            $row->setNota($data['nota'] ?? '');
            $row->setArchivo($fileName);
            $row->save();

            DB::commit();

            return [
                'flag' => true,
                'msg' => 'Creación terminada con éxito',
                'msj' => 'Creación terminada con éxito',
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
    public function borrar(int $numero): array
    {
        try {
            DB::beginTransaction();

            $row = Mercurio26::find($numero);
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
     * @param  'up'|'down'  $direction
     * @return array{flag: bool, msg: string, msj: string}
     */
    public function mover(int $numero, string $direction): array
    {
        try {
            DB::beginTransaction();

            $objetivo = Mercurio26::find($numero);
            if (! $objetivo) {
                throw new RuntimeException('Registro no encontrado.');
            }

            $ordenObj = (int) $objetivo->getOrden();

            if ($direction === 'up') {
                $vecino = Mercurio26::query()
                    ->where('orden', '<', $ordenObj)
                    ->orderByDesc('orden')
                    ->first();
            } else {
                $vecino = Mercurio26::query()
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

            return [
                'flag' => true,
                'msg' => 'Ordenado con éxito',
                'msj' => 'Ordenado con éxito',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'flag' => false,
                'msg' => 'No se puede Ordenar el Registro: '.$e->getMessage(),
                'msj' => 'No se puede Ordenar el Registro: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Igual que el admin legacy: Mercurio01::first() (p. ej. ME → temp/galeria),
     * no MO (img/galeria de banners).
     */
    public function resolveMercurio01(bool $required = true): ?Mercurio01
    {
        $mercurio01 = Mercurio01::first();

        if ($required && ! $mercurio01) {
            throw new RuntimeException('Configuración básica no encontrada.');
        }

        return $mercurio01;
    }
}
