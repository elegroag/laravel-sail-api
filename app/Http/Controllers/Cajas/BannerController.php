<?php

namespace App\Http\Controllers\Cajas;

use App\Exceptions\DebugException;
use App\Http\Controllers\Adapter\ApplicationController;
use App\Models\Adapter\DbBase;
use App\Models\Banner;
use App\Models\Mercurio01;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BannerController extends ApplicationController
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
        return view('cajas.banners.index', [
            'title' => 'Banners login',
            'help' => 'Administra los banners promocionales que se muestran como Dialog en el login de Mercurio.',
        ]);
    }

    public function galeria()
    {
        try {
            $mercurio01 = $this->resolveMercurio01();

            $galeria = Banner::orderByDesc('fecha_inicia')->orderByDesc('id')->get();

            $data = $galeria->map(function ($item) use ($mercurio01) {
                return $this->mapRecord($item, $mercurio01);
            });

            $response = parent::successFunc('Consulta exitosa');
            $response['data'] = $data;

            return $this->renderObject($response);
        } catch (DebugException $e) {
            return $this->renderObject(parent::errorFunc($e->getMessage()));
        } catch (\Throwable $e) {
            parent::setLogger($e->getMessage());

            return $this->renderObject(parent::errorFunc('No se pudo cargar los banners.'));
        }
    }

    public function editar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $id = $request->input('id');
            $mercurio01 = $this->resolveMercurio01();

            $banner = Banner::where('id', $id)->first();
            if (! $banner) {
                throw new DebugException('Registro no encontrado.');
            }

            return $this->renderObject($this->mapRecord($banner, $mercurio01), false);
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
            $id = $request->input('id');
            $isUpdate = ! empty($id);
            $estado = $request->input('estado', 'A');
            $urlImagen = trim((string) $request->input('url_imagen', ''));
            $contentHtml = Banner::sanitizeHtml($request->input('content_html'));
            $fechaInicia = $request->input('fecha_inicia');
            $fechaFinaliza = $request->input('fecha_finaliza');

            if (! in_array($estado, ['A', 'I'], true)) {
                throw new DebugException('Estado no válido.');
            }

            if (empty($fechaInicia) || empty($fechaFinaliza)) {
                throw new DebugException('Las fechas de inicio y finalización son requeridas.');
            }

            try {
                $fechaIniciaCarbon = Carbon::parse($fechaInicia)->startOfDay();
                $fechaFinalizaCarbon = Carbon::parse($fechaFinaliza)->startOfDay();
            } catch (\Throwable $e) {
                throw new DebugException('Formato de fecha no válido.');
            }

            if ($fechaFinalizaCarbon->lt($fechaIniciaCarbon)) {
                throw new DebugException('La fecha de finalización debe ser mayor o igual a la de inicio.');
            }

            if ($isUpdate) {
                $banner = Banner::where('id', $id)->first();
                if (! $banner) {
                    throw new DebugException('Registro no encontrado.');
                }
            } else {
                $banner = new Banner;
            }

            $banner->setEstado($estado);
            $banner->setContentHtml($contentHtml);
            $banner->setFechaInicia($fechaIniciaCarbon->toDateString());
            $banner->setFechaFinaliza($fechaFinalizaCarbon->toDateString());
            $banner->setUrlImagen($urlImagen !== '' ? $urlImagen : null);

            $hasFile = $request->hasFile('imagen') && $request->file('imagen')->isValid();

            if ($hasFile) {
                if (! $isUpdate) {
                    // Guardar primero para obtener id y nombrar el archivo.
                    if (! $banner->save()) {
                        parent::setLogger($banner->getMessages());
                        $this->db->rollback();
                        throw new DebugException('Error al guardar el banner.');
                    }
                }
                $this->replaceImagen($request, $banner, $mercurio01, (int) $banner->getId(), $isUpdate);
            } elseif (! $isUpdate && $urlImagen === '') {
                throw new DebugException('Debe cargar una imagen o indicar una URL de imagen.');
            } elseif ($isUpdate && empty($banner->getImagen()) && $urlImagen === '') {
                throw new DebugException('El registro no tiene imagen asociada. Cargue un archivo o una URL.');
            }

            if (! $banner->save()) {
                parent::setLogger($banner->getMessages());
                $this->db->rollback();
                throw new DebugException('Error al guardar el banner.');
            }

            $this->db->commit();
            $response = parent::successFunc($isUpdate ? 'Actualización terminada con éxito' : 'Creación terminada con éxito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede guardar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    public function borrar(Request $request)
    {
        try {
            $this->setResponse('ajax');
            $id = $request->input('id');

            $this->db->begin();
            $banner = Banner::where('id', $id)->first();

            if ($banner) {
                $archivo = $banner->getImagen();
                $mercurio01 = $this->resolveMercurio01();

                if (! empty($archivo)) {
                    $filePath = public_path($mercurio01->getPath().'galeria/'.$archivo);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $banner->delete();
            } else {
                throw new DebugException('El registro a borrar no existe.');
            }

            $this->db->commit();
            $response = parent::successFunc('Borrado con éxito');

            return $this->renderObject($response, false);
        } catch (DebugException $e) {
            $this->db->rollback();
            $response = parent::errorFunc('No se puede Borrar el Registro: '.$e->getMessage());

            return $this->renderObject($response, false);
        }
    }

    protected function resolveMercurio01(): Mercurio01
    {
        $mercurio01 = Mercurio01::where('codapl', 'MO')->first();
        if (! $mercurio01) {
            $mercurio01 = Mercurio01::first();
        }
        if (! $mercurio01) {
            throw new DebugException('Configuración básica no encontrada.');
        }

        return $mercurio01;
    }

    protected function mapRecord(Banner $item, Mercurio01 $mercurio01): array
    {
        $imageUrl = $item->resolveImageUrl($mercurio01);
        $fechaInicia = $item->getFechaInicia();
        $fechaFinaliza = $item->getFechaFinaliza();

        return [
            'id' => $item->getId(),
            'content_html' => $item->getContentHtml(),
            'imagen' => $imageUrl,
            'imagen_nombre' => $item->getImagen(),
            'url_imagen' => $item->getUrlImagen(),
            'fecha_inicia' => $fechaInicia ? Carbon::parse($fechaInicia)->toDateString() : null,
            'fecha_finaliza' => $fechaFinaliza ? Carbon::parse($fechaFinaliza)->toDateString() : null,
            'estado' => $item->getEstado(),
            'estado_label' => $item->getEstado() === 'A' ? 'Activo' : 'Inactivo',
        ];
    }

    protected function replaceImagen(Request $request, Banner $banner, Mercurio01 $mercurio01, int $id, bool $isUpdate): void
    {
        $file = $request->file('imagen');
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            throw new DebugException('Formato de imagen no permitido. Use JPG, JPEG o PNG.');
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
                if (file_exists($previousPath)) {
                    unlink($previousPath);
                }
            }
        }

        $file->move($destinationPath, $fileName);
        $banner->setImagen($fileName);
    }
}
