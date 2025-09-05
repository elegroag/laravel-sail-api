<?php

namespace App\Services;

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

    public static function selectStatic(...$data)
    {
        $attributes = '';
        $params = get_params_destructures($data);
        if (isset($params[0])) {
            $attributes .= " name=\"$params[0]\" id=\"$params[0]\"";
        }

        if (isset($params['id'])) {
            $attributes .= " id=\"$params[id]\"";
        }

        if (isset($params['class'])) {
            $attributes .= 'class="' . $params['class'] . '" ';
        }

        if (isset($params['style'])) {
            $attributes .= 'style="' . $params['style'] . '" ';
        }

        if (isset($params['onchange'])) {
            $attributes .= 'onchange="' . $params['onchange'] . '" ';
        }

        if (isset($params['event'])) {
            $attributes .= 'data-toggle="' . $params['event'] . '" ';
        }

        if (isset($params['dummyText'])) {
            $options = '<option value="@">' . $params['dummyText'] . '</option>';
        } else {
            $options = '';
        }

        if (isset($params['options'])) {
            foreach ($params['options'] as $key => $value) {
                if (isset($params['value']) && $params['value'] == $key) {
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
}
