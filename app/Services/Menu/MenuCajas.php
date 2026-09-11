<?php

namespace App\Services\Menu;

use Illuminate\Support\Facades\DB;

class MenuCajas
{
    private $currentUrl;

    private $breadcrumbs = [];

    private $menuItems;

    private $codapl;

    private $tipfun;

    private $pageTitle;

    private $path;

    public function __construct($codapl)
    {
        $this->codapl = $codapl;
        $this->tipfun = session('tipfun');
        $this->initialize();
    }

    private function initialize()
    {
        $this->menuItems = '';
        if (config('app.env') === 'local') {
            $this->path = config('app.dominio').':'.config('app.port');
        } else {
            $this->path = config('app.dominio');
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function selectAssoc(string $sql): array
    {
        return json_decode(json_encode(DB::select($sql)), true) ?? [];
    }

    private function getMenuItems($parentId)
    {
        $query = "SELECT menu_items.*, menu_tipos.tipo, menu_tipos.is_visible, menu_tipos.position 
        FROM menu_items 
        INNER JOIN menu_tipos ON menu_tipos.menu_item = menu_items.id
        INNER JOIN menu_permissions mp ON mp.menu_item = menu_items.id AND 
        mp.tipfun = '{$this->tipfun}' AND mp.can_view = 1
        WHERE 
        menu_items.codapl='{$this->codapl}' AND 
        menu_tipos.is_visible = TRUE
        ";

        if ($parentId === null) {
            $query .= ' AND menu_items.parent_id IS NULL';
        } else {
            $query .= ' AND menu_items.parent_id = '.intval($parentId);
        }
        $query .= ' ORDER BY menu_tipos.position ASC';
        return $this->selectAssoc($query);
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
        $menu = new MenuCajas($codapl);

        return $menu->mainMenu();
    }

    public static function getTree(string $codapl): array
    {
        if (! session('tipfun')) {
            return [];
        }

        $menu = new MenuCajas($codapl);

        return $menu->buildTree();
    }

    private function buildTree(): array
    {
        $this->currentUrl = request()->path();
        $parentMenuItems = $this->getMenuItems(null);
        $tree = [];

        foreach ($parentMenuItems as $menu) {
            $tree[] = $this->buildTreeItem($menu, true);
        }

        return $tree;
    }

    private function buildTreeItem(array $menu, bool $isParent = false): array
    {
        $isActive = ($menu['default_url'] ?? '') === $this->currentUrl;
        $children = [];

        if ($isParent) {
            $childItems = $this->getMenuItems($menu['id']);

            foreach ($childItems as $child) {
                $childActive = ($child['default_url'] ?? '') === $this->currentUrl;
                if ($childActive) {
                    $isActive = true;
                }

                $children[] = $this->buildTreeItem($child, false);
            }
        }

        $item = [
            'id' => (int) $menu['id'],
            'title' => $menu['title'] ?? '',
            'href' => $this->normalizeHref($menu['default_url'] ?? null),
            'icon' => $menu['icon'] ?? null,
            'color' => $menu['color'] ?? null,
            'isActive' => $isActive,
        ];

        if (count($children) > 0) {
            $item['children'] = $children;
        }

        return $item;
    }

    private function normalizeHref(?string $defaultUrl): string
    {
        if (! $defaultUrl) {
            return '#';
        }

        return str_starts_with($defaultUrl, '/') ? $defaultUrl : '/'.$defaultUrl;
    }
}
