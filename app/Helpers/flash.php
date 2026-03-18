<?php

use Illuminate\Support\Facades\Session;

if (! function_exists('set_flashdata')) {
    /**
     * capitalize
     * dar formato a las palabras que estan en mayusculas
     *
     * @param  string  $item
     * @param  mixed  $data
     * @param  bool|null  $persiste
     * @return void
     */
    function set_flashdata(string $item, string|array $data, ?bool $persiste = null)
    {
        if ($persiste) {
            $messages = session('PERSISTE', []);
            $messages["{$item}"] = $data;
            Session::put('PERSISTE', $messages);
        } else {
            if (in_array($item, ['error', 'success', 'notify'], true)) {
                session()->flash($item, $data);
            } else {
                $messages = session('FLASH', []);
                $messages["{$item}"] = $data;
                session()->flash('FLASH', $messages);
            }
        }
    }
}

if (! function_exists('set_flashnow')) {
    function set_flashnow(string $item, string|array $data)
    {
        $messages = session('FLASH_NOW', []);
        $messages["{$item}"] = $data;
        session()->now('FLASH_NOW', $messages);

        if (in_array($item, ['error', 'success', 'notify'], true)) {
            session()->now($item, $data);
        }
    }
}

if (! function_exists('get_flashdata')) {
    function get_flashdata(?bool $destroy = null)
    {
        $messages = session('FLASH', []);
        foreach (['error', 'success', 'notify'] as $key) {
            $val = session($key);
            if (! empty($val) && ! isset($messages[$key])) {
                $messages[$key] = $val;
            }
        }

        if ($destroy) {
            Session::forget('FLASH');
            foreach (['error', 'success', 'notify'] as $key) {
                Session::forget($key);
            }
        }

        return $messages;
    }
}

if (! function_exists('get_flashnow')) {
    function get_flashnow(?bool $destroy = null)
    {
        $messages = session('FLASH_NOW', []);
        foreach (['error', 'success', 'notify'] as $key) {
            $val = session($key);
            if (! empty($val) && ! isset($messages[$key])) {
                $messages[$key] = $val;
            }
        }

        if ($destroy) {
            Session::forget('FLASH_NOW');
            foreach (['error', 'success', 'notify'] as $key) {
                Session::forget($key);
            }
        }

        return $messages;
    }
}

if (! function_exists('get_flashdata_item')) {
    function get_flashdata_item(string $item, ?bool $destroy = null)
    {
        $messages_flash = session('FLASH', []);
        $message_persiste = session('PERSISTE', []);

        if ($destroy) {
            Session::forget('FLASH');
            Session::forget('PERSISTE');
        }

        if (in_array($item, ['error', 'success', 'notify'], true)) {
            $direct = session($item);
            Session::forget($item);

            if (! empty($direct)) {
                return $direct;
            }
        }

        $msj = isset($messages_flash["{$item}"]) ? $messages_flash["{$item}"] : false;
        if (empty($msj)) {
            $msj = isset($message_persiste["{$item}"]) ? $message_persiste["{$item}"] : false;
        }

        return $msj;
    }
}
