<?php

namespace App\Services;

use App\Exceptions\DebugException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\Paginator as SimplePaginator;
use Illuminate\Support\Collection;
use App\Services\Srequest;

class Tag
{

    public static function image(...$data)
    {
        $params = get_params_destructures($data);

        $attributes = "";
        if (isset($params['src'])) {
            $attributes .= 'src="../img/' . $params['src'] . '"';
        } else {
            if (isset($params[0])) {
                $attributes .= " src=\"../img/$params[0]\"";
            }
        }

        if (isset($params['alt'])) {
            $attributes .= 'alt="' . $params['alt'] . '"';
        }

        if (isset($params['class'])) {
            $attributes .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes .= 'style="' . $params['style'] . '" ';
        }

        if (isset($params['onclick'])) {
            $attributes .= 'onClick="' . $params['onclick'] . '" ';
        }

        if (isset($params['width'])) {
            $attributes .= 'width="' . $params['width'] . '" ';
        }

        if (isset($params['ondblclick'])) {
            $attributes .= 'ondblclick="' . $params['ondblclick'] . '" ';
        }



        return '<img ' . $attributes . ' />';
    }

    public static function selectStatic(Srequest $params)
    {
        $attributes = '';
        if ($params->getParam('name') != '') {
            $attributes .= " name=\"{$params->getParam('name')}\"";
        }

        if ($params->getParam('id') != '') {
            $attributes .= " id=\"{$params->getParam('id')}\"";
        }

        if ($params->getParam('class') != '') {
            $attributes .= 'class="' . $params->getParam('class') . '" ';
        }

        if ($params->getParam('style') != '') {
            $attributes .= 'style="' . $params->getParam('style') . '" ';
        }

        if ($params->getParam('onchange') != '') {
            $attributes .= 'onchange="' . $params->getParam('onchange') . '" ';
        }

        if ($params->getParam('event') != '') {
            $attributes .= 'data-toggle="' . $params->getParam('event') . '" ';
        }

        if ($params->getParam('readonly') != '') {
            $attributes .= 'readonly ';
        }

        if ($params->getParam('disabled') != '') {
            $attributes .= 'disabled ';
        }

        if ($params->getParam('dummyText') != '') {
            $options = '<option value="@">' . $params->getParam('dummyText') . '</option>';
        } else {
            $options = '';
        }

        if ($params->getParam('options') != '') {
            foreach ($params->getParam('options') as $key => $value) {
                if (isset($data['value']) && $data['value'] == $key) {
                    $options .= '<option value="' . $key . '" selected>' . $value . '</option>';
                } else {
                    $options .= '<option value="' . $key . '">' . $value . '</option>';
                }
            }
        }

        return '<select ' . $attributes . '>' . $options . '</select>';
    }

    public static function form(...$data)
    {
        $attributes = '';
        $params = get_params_destructures($data);
        if (isset($params['id'])) {
            $attributes .= " id=\"$params[id]\"";
        } else {
            if (isset($params[0])) {
                $attributes .= " id=\"$params[0]\"";
            }
        }

        if (isset($params['class'])) {
            $attributes .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes .= 'style="' . $params['style'] . '" ';
        }

        if (isset($params['action'])) {
            $attributes .= 'action="' . $params['action'] . '" ';
        }

        if (isset($params['method'])) {
            $attributes .= 'method="' . $params['method'] . '" ';
        }

        if (isset($params['enctype'])) {
            $attributes .= 'enctype="' . $params['enctype'] . '" ';
        }

        if (isset($params['autocomplete'])) {
            $attributes .= 'autocomplete="' . $params['autocomplete'] . '" ';
        }

        return '<form ' . $attributes . '>';
    }

    public static function endForm()
    {
        return '</form>';
    }

    public static function textField(...$data)
    {
        $attributes = '';
        $params = get_params_destructures($data);
        if (isset($params['id'])) {
            $attributes .= " id=\"$params[id]\"";
        }

        if (isset($params['class'])) {
            $attributes .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes .= 'style="' . $params['style'] . '" ';
        }

        if (isset($params['name'])) {
            $attributes .= 'name="' . $params['name'] . '" ';
        } else {
            if (isset($params[0])) {
                $attributes .= " name=\"$params[0]\"";
            }
        }

        if (isset($params['value'])) {
            $attributes .= 'value="' . $params['value'] . '" ';
        }

        return '<input ' . $attributes . ' type="text"/>';
    }

    public static function passwordField(...$data)
    {
        $attributes = '';
        $params = get_params_destructures($data);
        if (isset($params['id'])) {
            $attributes .= " id=\"$params[id]\"";
        }

        if (isset($params['class'])) {
            $attributes .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes .= 'style="' . $params['style'] . '" ';
        }

        if (isset($params['name'])) {
            $attributes .= 'name="' . $params['name'] . '" ';
        } else {
            if (isset($params[0])) {
                $attributes .= " name=\"$params[0]\"";
            }
        }

        if (isset($params['value'])) {
            $attributes .= 'value="' . $params['value'] . '" ';
        }

        return '<input ' . $attributes . ' type="password"/>';
    }

    public static function submitButton(...$data)
    {
        $attributes = '';
        $params = get_params_destructures($data);
        if (isset($params['id'])) {
            $attributes .= " id=\"$params[id]\"";
        } else {
            if (isset($params[0])) {
                $attributes .= " id=\"$params[0]\"";
            }
        }

        if (isset($params['class'])) {
            $attributes .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes .= 'style="' . $params['style'] . '" ';
        }

        if (isset($params['name'])) {
            $attributes .= 'name="' . $params['name'] . '" ';
        }

        if (isset($params['value'])) {
            $attributes .= 'value="' . $params['value'] . '" ';
        }

        return '<input ' . $attributes . ' type="submit"/>';
    }

    public static function textUpperField(...$data)
    {
        $attributes = '';
        $params = get_params_destructures($data);
        if (isset($params['id'])) {
            $attributes .= " id=\"$params[id]\"";
        }

        if (isset($params['class'])) {
            $attributes .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes .= 'style="' . $params['style'] . '" ';
        }

        if ($params['readonly'] != '') {
            $attributes .= 'readonly ';
        }

        if (isset($params['name'])) {
            $attributes .= 'name="' . $params['name'] . '" ';
        } else {
            if (isset($params[0])) {
                $attributes .= " name=\"$params[0]\"";
            }
        }

        if (isset($params['value'])) {
            $attributes .= 'value="' . $params['value'] . '" ';
        }

        return '<input ' . $attributes . ' type="text"/>';
    }

    public static function hiddenField($params)
    {
        $attributes = '';
        $params = get_params_destructures($params);
        if (isset($params['id'])) {
            $attributes .= " id=\"$params[id]\"";
        }

        if (isset($params['class'])) {
            $attributes .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes .= 'style="' . $params['style'] . '" ';
        }

        if (isset($params['name'])) {
            $attributes .= 'name="' . $params['name'] . '" ';
        } else {
            if (isset($params[0])) {
                $attributes .= " name=\"$params[0]\"";
            }
        }

        if (isset($params['value'])) {
            $attributes .= 'value="' . $params['value'] . '" ';
        }

        return '<input ' . $attributes . ' type="hidden"/>';
    }

    public static function uploadImage($params)
    {
        $attributes = '';
        $params = get_params_destructures($params);
        if (isset($params['id'])) {
            $attributes .= " id=\"$params[id]\"";
        }

        if (isset($params['class'])) {
            $attributes .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes .= 'style="' . $params['style'] . '" ';
        }

        if (isset($params['name'])) {
            $attributes .= 'name="' . $params['name'] . '" ';
        } else {
            if (isset($params[0])) {
                $attributes .= " name=\"$params[0]\"";
            }
        }

        if (isset($params['value'])) {
            $attributes .= 'value="' . $params['value'] . '" ';
        }

        return '<input ' . $attributes . ' type="file"/>';
    }

    public static function numericField(...$data)
    {
        $attributes = '';
        $params = get_params_destructures($data);
        if (isset($params['id'])) {
            $attributes .= " id=\"$params[id]\"";
        }

        if (isset($params['class'])) {
            $attributes .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes .= 'style="' . $params['style'] . '" ';
        }

        if (isset($params['name'])) {
            $attributes .= 'name="' . $params['name'] . '" ';
        } else {
            if (isset($params[0])) {
                $attributes .= " name=\"$params[0]\"";
            }
        }

        if (isset($params['value'])) {
            $attributes .= 'value="' . $params['value'] . '" ';
        }

        $code = "<input type='numeric' {$attributes} />";
        return $code;
    }

    /**
     * Compatibilidad legacy: asigna valores a inputs en vistas antiguas.
     * En entorno Blade moderno no es necesario; se deja como no-op.
     */
    public static function displayTo($name, $value)
    {
        // No-op para compatibilidad; en Blade se debe usar old($name, $value)
        return true;
    }

    public static function moneyField(...$data)
    {
        $attributes_money = '';
        $attributes_hidden = '';
        $params = get_params_destructures($data);
        if (isset($params['id'])) {
            $attributes_money .= " id=\"$params[id]\"";
            $attributes_hidden .= " id=\"hide-$params[id]\"";
        }

        if (isset($params['class'])) {
            $attributes_money .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes_money .= 'style="' . $params['style'] . '" ';
        }

        if (isset($params['name'])) {
            $attributes_money .= 'name="' . $params['name'] . '" ';
        } else {
            if (isset($params[0])) {
                $attributes_money .= " name=\"$params[0]\"";
            }
        }

        if (isset($params['value'])) {
            $attributes_money .= 'value="' . $params['value'] . '" ';
            $attributes_hidden .= 'value="' . $params['value'] . '" ';
        }

        $attributes_money .= 'data-toggle="input-money"';

        $code = "<input type='text' {$attributes_money} /><input style='display: none;' type='numeric' {$attributes_hidden} />";
        return $code;
    }

    public static function linkTo(...$data)
    {
        $attributes = '';
        $params = get_params_destructures($data);
        if (isset($params['id'])) {
            $attributes .= " id=\"$params[id]\"";
        }

        if (isset($params['class'])) {
            $attributes .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes .= 'style="' . $params['style'] . '" ';
        }

        if (isset($params['type'])) {
            $attributes .= 'type="' . $params['type'] . '" ';
        }

        if (isset($params['href'])) {
            $attributes .= 'href="' . $params['href'] . '" ';
        } else {
            if (isset($params[0])) {
                $attributes .= " href=\"$params[0]\"";
            }
        }

        $content = '';
        if (isset($params['content'])) {
            $content = $params['content'];
        }

        return '<a ' . $attributes . ' >' . $content . '</a>';
    }

    public static function addressField(...$data)
    {
        $params = get_params_destructures($data);
        $name = '';
        if (isset($params['name'])) {
            $name = $params['name'];
        } else {
            $name = $params[0];
        }
        $value = '';
        if (isset($params['value'])) {
            $value = $params['value'];
            unset($params['value']);
        }

        $code = "<div class='input-group'>";
        $code .= "<div class='input-group-prepend'>";
        $code .= " ";

        if (isset($params['event'])) {
            $code .= "<button class='btn btn-sm btn-icon btn-primary' type='button' data-name='{$name}' data-toggle=\"address\"><i class='fas fa-pen'></i></button>";
        } else {
            $code .= "<button class='btn btn-sm btn-icon btn-primary' type='button' onclick=\"openAddress('{$name}')\"><i class='fas fa-pen'></i></button>";
        }

        $code .= "</div>";
        $code .= "<input type='text' name='{$name}' id='{$name}' value='$value' readonly ";

        foreach ($params as $key => $value) {
            if (!is_numeric($key)) {
                $code .= "$key='$value' ";
            }
        }
        $code .= " />";
        $code .= "</div>\r\n";

        return $code;
    }

    public static function paginate($collectModel, $pageNumber = null, $cantidadPages = 10)
    {
        // Normaliza página
        if ($pageNumber === null || !is_numeric($pageNumber) || (int)$pageNumber < 1) {
            $pageNumber = 1;
        } else {
            $pageNumber = (int)$pageNumber;
        }

        // Helper de salida compatible
        $toStd = function ($items, int $total, int $perPage, int $currentPage) {
            $page = new \stdClass();
            $page->items = $items;
            $page->num_rows = $total;
            $page->first = 1;
            $page->current = $currentPage;
            $page->total_pages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            if ($page->total_pages < 1) $page->total_pages = 1;
            $page->before = ($currentPage > 1) ? ($currentPage - 1) : 1;
            $page->last = $page->total_pages;
            $page->next = ($currentPage < $page->last) ? ($currentPage + 1) : $currentPage;
            return $page;
        };

        // 1) Si es un Builder de Eloquent/Query, usar paginate nativo
        if ($collectModel instanceof EloquentBuilder || $collectModel instanceof QueryBuilder) {
            $paginator = $collectModel->paginate($cantidadPages, ['*'], 'page', $pageNumber);
            return self::fromLengthAwarePaginator($paginator);
        }

        // 2) Si ya es un LengthAwarePaginator
        if ($collectModel instanceof LengthAwarePaginatorContract) {
            return self::fromLengthAwarePaginator($collectModel);
        }

        // 3) Si es un SimplePaginator
        if ($collectModel instanceof SimplePaginator) {
            $items = $collectModel->items();
            // SimplePaginator no conoce el total real; aproximamos con páginas
            $perPage = $collectModel->perPage();
            // Se asume que no hay total; calculamos con la cantidad actual para mantener compatibilidad
            $total = count($items) + ($perPage * ($pageNumber - 1));
            return $toStd($items, $total, $perPage, $pageNumber);
        }

        // 4) Si es una Collection
        if ($collectModel instanceof Collection) {
            $total = $collectModel->count();
            $items = $collectModel->forPage($pageNumber, $cantidadPages)->values();
            return $toStd($items, $total, $cantidadPages, $pageNumber);
        }

        // 5) Si es un array plano
        if (is_array($collectModel)) {
            $total = count($collectModel);
            $start = $cantidadPages * ($pageNumber - 1);
            $items = array_slice($collectModel, $start, $cantidadPages);
            return $toStd($items, $total, $cantidadPages, $pageNumber);
        }

        // 6) Último recurso: tratar como colección vacía
        return $toStd([], 0, $cantidadPages, $pageNumber);
    }

    /**
     * Convierte un LengthAwarePaginator de Laravel al formato esperado por los controladores existentes
     */
    private static function fromLengthAwarePaginator(LengthAwarePaginatorContract $paginator)
    {
        $page = new \stdClass();
        // Items puede ser Collection; mantenemos tal cual para compatibilidad
        $page->items = $paginator->items();
        $page->num_rows = (int)$paginator->total();
        $page->first = 1;
        $page->current = (int)$paginator->currentPage();
        $page->total_pages = (int)$paginator->lastPage();
        if ($page->total_pages < 1) $page->total_pages = 1;
        $page->before = ($page->current > 1) ? ($page->current - 1) : 1;
        $page->last = $page->total_pages;
        $page->next = ($page->current < $page->last) ? ($page->current + 1) : $page->current;
        return $page;
    }
}
