<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;
use Carbon\Carbon;

class Banner extends ModelBase
{
    protected $table = 'banners';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'content_html',
        'imagen',
        'url_imagen',
        'fecha_inicia',
        'fecha_finaliza',
        'estado',
    ];

    public function setContentHtml($contentHtml)
    {
        $this->content_html = $contentHtml;
    }

    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }

    public function setUrlImagen($urlImagen)
    {
        $this->url_imagen = $urlImagen;
    }

    public function setFechaInicia($fechaInicia)
    {
        $this->fecha_inicia = $fechaInicia;
    }

    public function setFechaFinaliza($fechaFinaliza)
    {
        $this->fecha_finaliza = $fechaFinaliza;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getContentHtml()
    {
        return $this->content_html;
    }

    public function getImagen()
    {
        return $this->imagen;
    }

    public function getUrlImagen()
    {
        return $this->url_imagen;
    }

    public function getFechaInicia()
    {
        return $this->fecha_inicia;
    }

    public function getFechaFinaliza()
    {
        return $this->fecha_finaliza;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Banner activo vigente para mostrar en el login.
     */
    public static function findActiveForLogin(): ?self
    {
        $today = Carbon::today()->toDateString();

        return static::query()
            ->where('estado', 'A')
            ->whereDate('fecha_inicia', '<=', $today)
            ->whereDate('fecha_finaliza', '>=', $today)
            ->orderByDesc('fecha_inicia')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Resuelve la URL de imagen: prioriza url_imagen; si no, arma desde archivo local.
     */
    public function resolveImageUrl(?Mercurio01 $mercurio01 = null): string
    {
        $urlImagen = trim((string) $this->getUrlImagen());
        if ($urlImagen !== '') {
            return $urlImagen;
        }

        $imagen = $this->getImagen();
        if (empty($imagen)) {
            return '';
        }

        if (! $mercurio01) {
            $mercurio01 = Mercurio01::where('codapl', 'MO')->first() ?? Mercurio01::first();
        }

        if (! $mercurio01) {
            return '';
        }

        return $mercurio01->dominioUrl('galeria/'.$imagen);
    }

    public static function sanitizeHtml(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return $html;
        }

        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html) ?? $html;
        $html = preg_replace('/\son\w+\s*=\s*(["\']).*?\1/iu', '', $html) ?? $html;
        $html = preg_replace('/\son\w+\s*=\s*[^\s>]+/iu', '', $html) ?? $html;

        return $html;
    }

    /**
     * Payload para Inertia login.
     */
    public function toLoginPayload(?Mercurio01 $mercurio01 = null): array
    {
        return [
            'id' => $this->getId(),
            'content_html' => self::sanitizeHtml($this->getContentHtml()),
            'image_url' => $this->resolveImageUrl($mercurio01),
            'fecha_inicia' => $this->getFechaInicia(),
            'fecha_finaliza' => $this->getFechaFinaliza(),
        ];
    }
}
