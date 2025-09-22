<?php

namespace App\Models;

use App\Models\Adapter\ModelBase;

/**
 * Modelo MenuItem
 * - Sin timestamps
 * - PK autoincremental `id`
 */
class MenuItem extends ModelBase
{
    protected $table = 'menu_items';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'title',
        'default_url',
        'icon',
        'color',
        'nota',
        'position',
        'parent_id',
        'is_visible',
        'codapl',
        'tipo',
    ];

    // Getters/Setters básicos (compatibilidad y claridad)
    public function getId() { return $this->id; }
    public function setTitle($v) { $this->title = $v; }
    public function getTitle() { return $this->title; }
    public function setDefaultUrl($v) { $this->default_url = $v; }
    public function getDefaultUrl() { return $this->default_url; }
    public function setIcon($v) { $this->icon = $v; }
    public function getIcon() { return $this->icon; }
    public function setColor($v) { $this->color = $v; }
    public function getColor() { return $this->color; }
    public function setNota($v) { $this->nota = $v; }
    public function getNota() { return $this->nota; }
    public function setPosition($v) { $this->position = $v; }
    public function getPosition() { return $this->position; }
    public function setParentId($v) { $this->parent_id = $v; }
    public function getParentId() { return $this->parent_id; }
    public function setIsVisible($v) { $this->is_visible = $v; }
    public function getIsVisible() { return $this->is_visible; }
    public function setCodapl($v) { $this->codapl = $v; }
    public function getCodapl() { return $this->codapl; }
    public function setTipo($v) { $this->tipo = $v; }
    public function getTipo() { return $this->tipo; }
}
