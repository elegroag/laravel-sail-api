<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;
use App\Models\Mercurio53;
use Illuminate\Http\Request;

class Mercurio53Controller extends ApplicationController
{
    protected $db;

    protected $user;

    protected $tipo;

    public function __construct()
    {
        $this->db = DbBase::rawConnect();
        $this->user = session()->has('user') ? session('user') : null;
        $this->tipo = session()->has('tipo') ? session('tipo') : null;
    }

    public function index()
    {
        return view('cajas.mercurio53.index', [
            'title' => 'Destacadas',
            'help' => 'Esta opcion permite manejar las imagenes destacadas.',
        ]);
    }

    public function galeria()
    {
        try {
            $mercurio01 = $this->resolveMercurio01();

            $galeria = Mercurio53::orderBy('orden', 'ASC')->get();

            $data = $galeria->map(function ($item) use ($mercurio01) {
                if (empty($item->getUrl()) && ! empty($item->getArchivo())) {
                    $this->syncImageUrl($item, $mercurio01);
                    $item->save();
                }

                return $this->mapRecord($item, $mercurio01);
            });

            $response = parent::successFunc('Consulta exitosa');
            $response['data'] = $data;

            return $this->renderObject($response);
        } catch (DebugException $e) {
            return $this->renderObject(parent::errorFunc($e->getMessage()));
        } catch (\Throwable $e) {
            parent::setLogger($e->getMessage());

            return $this->renderObject(parent::errorFunc('No se pudo cargar las imágenes destacadas.'));
        }
    }

    public function editar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numero = $request->input('numero');
            $mercurio01 = $this->resolveMercurio01();

            $mercurio53 = Mercurio53::where('numero', $numero)->first();
            if (! $mercurio53) {
                throw new DebugException('Registro no encontrado.');
            }

            return $this->renderObject($this->mapRecord($mercurio53, $mercurio01), false);
        } catch (DebugException $e) {
            $response = parent::errorFunc($e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function guardar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $this->db->begin();

            $mercurio01 = $this->resolveMercurio01();
            $numero = $request->input('numero');
            $isUpdate = ! empty($numero);

            if ($isUpdate) {
                $mercurio53 = Mercurio53::where('numero', $numero)->first();
                if (! $mercurio53) {
                    throw new DebugException('Registro no encontrado.');
                }
            } else {
                $numero = (Mercurio53::max('numero') ?? 0) + 1;
                $mercurio53 = new Mercurio53;
                $mercurio53->setNumero($numero);
                $mercurio53->setOrden((Mercurio53::max('orden') ?? 0) + 1);
            }

            if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
                $this->replaceArchivo($request, $mercurio53, $mercurio01, (int) $numero, $isUpdate);
            } elseif (! $isUpdate) {
                throw new DebugException('No se ha subido ningún archivo o el archivo no es válido.');
            } elseif (empty($mercurio53->getArchivo())) {
                throw new DebugException('El registro no tiene imagen asociada.');
            }

            $this->syncImageUrl($mercurio53, $mercurio01);

            if (! $mercurio53->save()) {
                parent::setLogger($mercurio53->getMessages());
                $this->db->rollback();
                throw new DebugException('Error al guardar la imagen destacada.');
            }

            $this->db->commit();
            $response = parent::successFunc($isUpdate ? 'Actualización terminada con éxito' : 'Creacion terminada Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede guardar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function arriba(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numero = $request->input('numero');

            $this->db->begin();
            $objetivo = Mercurio53::where('numero', $numero)->first();
            if (! $objetivo) {
                throw new DebugException('Registro no encontrado.');
            }

            $orden_obj = $objetivo->getOrden();
            $minimo = Mercurio53::min('orden');

            if ($orden_obj != $minimo) {
                $superior = Mercurio53::where('orden', '<', $orden_obj)->orderBy('orden', 'desc')->first();
                if ($superior) {
                    $orden_sup = $superior->getOrden();
                    $objetivo->orden = $orden_sup;
                    $superior->orden = $orden_obj;
                    $objetivo->save();
                    $superior->save();
                }
            }
            $this->db->commit();
            $response = parent::successFunc('Ordenado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede Ordenar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function abajo(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numero = $request->input('numero');

            $this->db->begin();
            $objetivo = Mercurio53::where('numero', $numero)->first();
            if (! $objetivo) {
                throw new DebugException('Registro no encontrado.');
            }

            $orden_obj = $objetivo->getOrden();
            $maximo = Mercurio53::max('orden');

            if ($orden_obj != $maximo) {
                $inferior = Mercurio53::where('orden', '>', $orden_obj)->orderBy('orden', 'asc')->first();
                if ($inferior) {
                    $orden_inf = $inferior->getOrden();
                    $objetivo->orden = $orden_inf;
                    $inferior->orden = $orden_obj;
                    $objetivo->save();
                    $inferior->save();
                }
            }
            $this->db->commit();
            $response = parent::successFunc('Ordenado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede Ordenar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function borrar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $numero = $request->input('numero');

            $this->db->begin();
            $mercurio53 = Mercurio53::where('numero', $numero)->first();

            if ($mercurio53) {
                $archivo = $mercurio53->getArchivo();
                $mercurio01 = Mercurio01::first();

                if ($mercurio01 && ! empty($archivo)) {
                    $filePath = public_path($mercurio01->getPath().'galeria/'.$archivo);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $mercurio53->delete();
            } else {
                throw new DebugException('El registro a borrar no existe.');
            }

            $this->db->commit();
            $response = parent::successFunc('Borrado Con Exito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede Borrar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    protected function resolveMercurio01(): Mercurio01
    {
        $mercurio01 = Mercurio01::where('codapl', 'MO')->get()->first();
        if (! $mercurio01) {
            throw new DebugException('Configuración básica no encontrada.');
        }

        return $mercurio01;
    }

    protected function mapRecord(Mercurio53 $item, Mercurio01 $mercurio01): array
    {
        $archivo = $item->getArchivo();
        $imageUrl = $this->buildImageUrl($mercurio01, $archivo);

        return [
            'numero' => $item->getNumero(),
            'orden' => $item->getOrden(),
            'url' => $item->getUrl() ?: $imageUrl,
            'archivo' => $imageUrl,
            'archivo_nombre' => $archivo,
        ];
    }

    protected function syncImageUrl(Mercurio53 $mercurio53, Mercurio01 $mercurio01): void
    {
        $imageUrl = $this->buildImageUrl($mercurio01, $mercurio53->getArchivo());
        if ($imageUrl !== '') {
            $mercurio53->setUrl($imageUrl);
        }
    }

    protected function buildImageUrl(Mercurio01 $mercurio01, ?string $fileName): string
    {
        if (empty($fileName)) {
            return '';
        }

        return $mercurio01->dominioUrl('galeria/'.$fileName);
    }

    protected function replaceArchivo(Request $request, Mercurio53 $mercurio53, Mercurio01 $mercurio01, int $numero, bool $isUpdate): void
    {
        $file = $request->file('archivo');
        $extension = $file->getClientOriginalExtension();
        $fileName = 'ME53_'.$numero.'.'.$extension;
        $destinationPath = public_path($mercurio01->getPath().'galeria');

        if ($isUpdate) {
            $previousFile = $mercurio53->getArchivo();
            if (! empty($previousFile) && $previousFile !== $fileName) {
                $previousPath = public_path($mercurio01->getPath().'galeria/'.$previousFile);
                if (file_exists($previousPath)) {
                    unlink($previousPath);
                }
            }
        }

        $file->move($destinationPath, $fileName);
        $mercurio53->setArchivo($fileName);
    }
}
