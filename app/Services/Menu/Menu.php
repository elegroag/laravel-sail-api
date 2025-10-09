<?php

namespace App\Services\Menu;

use App\Models\Adapter\DbBase;

class Menu
{
    private $user;

    private $currentUrl;

    private $breadcrumbs = [];

    private $menuItems;

    private $db;

    private $codapl;

    private $pageTitle;

    private $path;

    public function __construct($codapl)
    {
        $this->codapl = $codapl;
        if (session()->has('user')) {
            $this->user = session()->all();
        }
        $this->db = DbBase::rawConnect();
        $this->initialize();
    }

    private function initialize()
    {
        if (! $this->user) {
            return null;
        }
        $this->menuItems = '';
        $this->path = env('APP_URL').':'.env('APP_PORT');
    }

    private function getMenuItems($parentId)
    {
        switch (session('tipo')) {
            case 'T':
            case 'O':
            case 'I':
            case 'F':
                $menu_tipo = 'T';
                break;
            case 'E':
                $menu_tipo = 'E';
                break;
            case 'P':
                $menu_tipo = 'P';
                break;
            default:
                $menu_tipo = 'P';
                break;
        }
        $estado_afiliado = session('estado_afiliado');

        if ($estado_afiliado == 'I') {
            $menu_tipo = 'P';
        }

        $query = "SELECT * FROM menu_items 
        WHERE is_visible = TRUE AND 
        codapl='{$this->codapl}' AND 
        tipo = '{$menu_tipo}' ";

        if ($parentId === null) {
            $query .= ' AND parent_id IS NULL';
        } else {
            $query .= ' AND parent_id = '.intval($parentId);
        }
        $query .= ' ORDER BY position ASC';
        $sql = $this->db->inQueryAssoc($query);

        return $sql;
    }

    private function normalizeTitle($title)
    {
        return str_replace(' ', '_', $title);
    }

    private function buildMenuItem($menu, $isParent = false)
    {
        $title = $this->normalizeTitle($menu['title']);
        $this->currentUrl = request()->path();

        $isActive = ($menu['default_url'] == $this->currentUrl);
        if ($isActive) {
            // Crumb activo único (sin hijos)
            $this->breadcrumbs[] = [
                'icon' => $menu['icon'] ?? null,
                'title' => $menu['title'] ?? '',
                'is_active' => true,
                'url' => ($menu['default_url']) ? $this->path.'/'.$menu['default_url'] : '#',
            ];
            $this->pageTitle = $menu['title'];
        }

        $icon = "<i class='{$menu['icon']} {$menu['color']}'></i>";
        $linkText = "<span class='nav-link-text'>{$menu['title']}</span>";

        if ($isParent) {
            $childItems = $this->getMenuItems($menu['id']);
            if (count($childItems) > 0) {
                return $this->buildParentMenuItem($menu, $title, $icon, $linkText, $childItems);
            }
        }

        return $this->buildSingleMenuItem($menu, $title, $icon, $linkText, $isActive);
    }

    private function buildParentMenuItem($menu, $title, $icon, $linkText, $childItems)
    {
        $isActive = false;
        $childHtml = '';

        foreach ($childItems as $child) {
            $childActive = ($child['default_url'] == $this->currentUrl);
            if ($childActive) {
                $isActive = true;
                // Agregar breadcrumb de padre como no activo
                $this->breadcrumbs[] = [
                    'icon' => $menu['icon'] ?? null,
                    'title' => $menu['title'] ?? '',
                    'is_active' => false,
                    'url' => ($menu['default_url']) ? $this->path.'/'.$menu['default_url'] : '#',
                ];
                // Agregar breadcrumb del hijo como activo
                $this->breadcrumbs[] = [
                    'icon' => $child['icon'] ?? null,
                    'title' => $child['title'] ?? '',
                    'is_active' => true,
                    'url' => ($child['default_url']) ? $this->path.'/'.$child['default_url'] : '#',
                ];
                $this->pageTitle = $menu['title'];
            }

            $childHtml .= $this->buildChildMenuItem($child, $childActive);
        }

        $activeClass = $isActive ? 'active' : '';
        $showClass = $isActive ? 'show' : '';

        return "
            <li class='nav-item'>
                <a class='nav-link {$activeClass}' href='#{$title}' data-bs-toggle='collapse' role='button' aria-expanded='false' aria-controls='{$title}'>
                    {$icon}
                    {$linkText}
                </a>
                <div class='collapse {$showClass}' id='{$title}'>
                    <ul class='nav nav-sm flex-column'>
                        {$childHtml}
                    </ul>
                </div>
            </li>";
    }

    private function buildChildMenuItem($child, $isActive)
    {
        $activeClass = $isActive ? 'active' : '';
        $title = strtolower(str_replace(' ', '_', $child['title']));

        return "
            <li class='nav-item'>
                <a data-id='{$title}' href='{$this->path}/".$child['default_url']."'
                   class='nav-link {$activeClass}'>
                    {$child['title']}
                </a>
            </li>";
    }

    private function buildSingleMenuItem($menu, $title, $icon, $linkText, $isActive)
    {
        $activeClass = $isActive ? 'active' : '';

        return "
            <li class='nav-item'>
                <a class='nav-link {$activeClass}' href='{$this->path}/".$menu['default_url']."'>
                    {$icon}
                    {$linkText}
                </a>
            </li>";
    }

    public function mainMenu()
    {
        $parentMenuItems = $this->getMenuItems(null);

        foreach ($parentMenuItems as $menu) {
            $this->menuItems .= $this->buildMenuItem($menu, true);
        }

        return [$this->menuItems, $this->breadcrumbs, $this->pageTitle];
    }

    public static function showMenu($codapl)
    {
        $menu = new Menu($codapl);

        return $menu->mainMenu();
    }
}
