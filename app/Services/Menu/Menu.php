<?php
namespace App\Services\Menu;

use App\Models\Adapter\DbBase;
use App\Models\Mercurio01;

class Menu
{
    private $user;
    private $currentUrl;
    private $breadcrumbs;
    private $menuItems;
    private $typeheadItems;
    private $tipo;
    private $db;

    public function __construct()
    {
        if (session()->has('documento')) {
            $this->user = session()->all();
        }
        $this->db = DbBase::rawConnect();
        $this->initialize();
    }

    private function initialize()
    {
        if (!$this->user) {
            return null;
        }
        $this->tipo = session()->get('tipo');
        $this->currentUrl = request()->path();
        $this->breadcrumbs = "";
        $this->menuItems = "";
        $this->typeheadItems = [];
    }

    private function getMenuItems($parentId)
    {
        $menu_tipo = 'P';
        if ($this->tipo == "T") $menu_tipo = 'T';
        if ($this->tipo == "E") $menu_tipo = 'E';
        if ($this->tipo == "P") $menu_tipo = 'P';
        if ($this->tipo == "I") $menu_tipo = 'T';
        if ($this->tipo == "O") $menu_tipo = 'T';
        if ($this->tipo == "F") $menu_tipo = 'T';

        $query = "SELECT * FROM menu_items WHERE is_visible = TRUE AND codapl='ME' AND tipo = '" . $menu_tipo . "'";
        if ($parentId === null) {
            $query .= " AND parent_id IS NULL";
        } else {
            $query .= " AND parent_id = " . intval($parentId);
        }
        $query .= " ORDER BY position ASC";
        return $this->db->inQueryAssoc($query);
    }

    private function normalizeTitle($title)
    {
        return str_replace(" ", "_", $title);
    }

    private function buildMenuItem($menu, $isParent = false)
    {
        $title = $this->normalizeTitle($menu['title']);
        $isActive = ($menu['default_url'] == $this->currentUrl);

        if ($isActive) {
            $this->breadcrumbs = "<li class='breadcrumb-item active' aria-current='page'>{$menu['title']}</li>";
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
        $childHtml = "";

        foreach ($childItems as $child) {
            $childActive = ($child['default_url'] == $this->currentUrl);
            if ($childActive) {
                $isActive = true;
                $this->breadcrumbs .= "<li class='breadcrumb-item active'>{$child['title']}</li>";
            }

            $childHtml .= $this->buildChildMenuItem($child, $childActive);
            $this->addTypeheadItem($menu['title'], $child);
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
                <a data-id='{$title}' href='" . route($child['default_url']) . "'
                   class='nav-link {$activeClass}'>
                    {$child['title']}
                </a>
            </li>";
    }

    private function buildSingleMenuItem($menu, $title, $icon, $linkText, $isActive)
    {
        $activeClass = $isActive ? 'active' : '';
        $this->addTypeheadItem("Principal", $menu);

        return "
            <li class='nav-item'>
                <a class='nav-link {$activeClass}' href='" . route($menu['default_url']) . "'>
                    {$icon}
                    {$linkText}
                </a>
            </li>";
    }

    private function addTypeheadItem($division, $item)
    {
        $this->typeheadItems[] = [
            "division" => $division,
            "detalle" => $item['title'],
            "nota" => $item['nota'],
            "url" => route($item['default_url'])
        ];
    }

    public function mainMenu()
    {
        $parentMenuItems = $this->getMenuItems(null);

        foreach ($parentMenuItems as $menu) {
            $this->menuItems .= $this->buildMenuItem($menu, true);
        }

        return [$this->menuItems, $this->breadcrumbs, $this->typeheadItems];
    }

    public static function showMenu()
    {
        $menu = new Menu();
        return $menu->mainMenu();
    }
}
