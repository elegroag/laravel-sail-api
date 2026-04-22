<?php

if (! function_exists('capitalize')) {
    /**
     * capitalize
     * dar formato a las palabras que estan en mayusculas
     *
     * @param  mixed  $string
     * @return string
     */
    function capitalize($string)
    {
        $exp = explode(' ', strtolower($string));
        $parts = '';
        foreach ($exp as $row) {
            if (strlen(trim($row)) > 0) {
                $parts .= ' ' . ucfirst($row);
            }
        }

        return trim($parts);
    }
}

if (!function_exists('encode_utf8')) {
    /**
     * Corrige errores de codificación y normaliza texto a UTF-8.
     * Acepta cadenas, arreglos o colecciones.
     *
     * @param  mixed  $data   Texto, arreglo o colección.
     * @param  bool|null  $lower  Convierte a minúsculas si es true.
     * @return mixed
     */
    function encode_utf8($data, $lower = null)
    {
        // 🔁 Si es una colección (Laravel o similar), la convertimos a arreglo
        if (is_object($data) && method_exists($data, 'toArray')) {
            $data = $data->toArray();
        }
        // 🔁 Si es un arreglo, lo procesamos recursivamente
        if (is_array($data)) {
            return array_map(function ($value) use ($lower) {
                return encode_utf8($value, $lower);
            }, $data);
        }
        // ⚙️ Si no es string, lo retornamos tal cual (números, null, bool, etc.)
        if (!is_string($data)) {
            return $data;
        }
        // 🧩 Detectar codificación y convertir a UTF-8 si es necesario
        $encoding = mb_detect_encoding($data, ['UTF-8', 'ISO-8859-1', 'ASCII', 'UTF-7'], true);

        switch ($encoding) {
            case 'ASCII':
                $data = mb_convert_encoding($data, 'UTF-8', 'ASCII');
                break;
            case 'ISO-8859-1':
                $data = mb_convert_encoding($data, 'UTF-8', 'ISO-8859-1');
                break;
            case 'UTF-7':
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-7');
                break;
                // Si ya es UTF-8 o no detectado, se deja igual
        }
        // 🔡 Convertir a minúsculas si se solicita
        if ($lower) {
            $data = mb_strtolower($data, 'UTF-8');
        }
        return $data;
    }
}


if (! function_exists('mask_email')) {
    function mask_email($email)
    {
        $em = explode('@', $email);
        $name = implode('@', array_slice($em, 0, count($em) - 1));
        $len = floor(strlen($name) / 2);

        return substr($name, 0, $len) . str_repeat('*', $len) . '@' . end($em);
    }
}

if (! function_exists('validar_email')) {
    function validar_email($str)
    {
        return (! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,10}$/ix", $str)) ? false : true;
    }
}

if (! function_exists('validar_clave')) {
    function validar_clave($str)
    {
        return (! preg_match('/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/', $str)) ? false : true;
    }
}

if (!function_exists('utf8n')) {
    /**
     * Corrige problemas de codificación comunes en textos mal convertidos a UTF-8.
     * Usa encode_utf8() como base.
     *
     * @param  mixed  $string
     * @return mixed
     */
    function utf8n($string)
    {
        // Reutiliza el método centralizado para la normalización UTF-8
        $normalized = encode_utf8($string);
        // 🔤 Corrige caracteres malformados comunes (doble codificación)
        if (is_string($normalized)) {
            $replacements = [
                'Ã±' => 'ñ',
                'Ã‘' => 'Ñ',
                'Ã“' => 'Ó',
                'Ã“' => 'Ó',
                'Ã¡' => 'á',
                'Ã©' => 'é',
                'Ãí' => 'í',
                'Ã³' => 'ó',
                'Ãú' => 'ú',
                'Ã¼' => 'ü',
                'Ã'  => 'í', // casos ambiguos
            ];

            $normalized = str_replace(array_keys($replacements), array_values($replacements), $normalized);
        }
        return $normalized;
    }
}

if (! function_exists('sanetizar')) {
    function sanetizar($string)
    {
        $string = trim($string);
        $string = str_replace(
            ['á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'],
            ['a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'],
            $string
        );

        $string = str_replace(
            ['é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'],
            ['e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'],
            $string
        );

        $string = str_replace(
            ['í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'],
            ['i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'],
            $string
        );

        $string = str_replace(
            ['ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'],
            ['o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'],
            $string
        );

        $string = str_replace(
            ['ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'],
            ['u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'],
            $string
        );

        $string = str_replace(
            ['ñ', 'Ñ', 'ç', 'Ç'],
            ['n', 'N', 'c', 'C'],
            $string
        );

        $string = str_replace(
            [
                '¨',
                'º',
                '-',
                '~',
                '·',
                '$',
                '%',
                '&',
                '/',
                '°',
                '(',
                ')',
                '?',
                "'",
                '¡',
                '¿',
                '[',
                '^',
                '<code>',
                ']',
                '+',
                '}',
                '{',
                '¨',
                '´',
                '>',
                '< ',
                ';',
                ',',
                ':',
            ],
            '',
            $string
        );

        return $string;
    }
}

if (! function_exists('sanetizar_input')) {
    function sanetizar_input($string)
    {
        $string = trim($string);
        $string = str_replace(
            [
                '¨',
                'º',
                '-',
                '~',
                '·',
                '$',
                '%',
                '&',
                '/',
                '°',
                '(',
                ')',
                '?',
                "'",
                '¡',
                '¿',
                '[',
                '^',
                '<code>',
                ']',
                '+',
                '}',
                '{',
                '¨',
                '´',
                '>',
                '< ',
                ';',
                ',',
                ':',
            ],
            '',
            $string
        );

        return $string;
    }
}

if (! function_exists('sanetizar_date')) {
    function sanetizar_date($string)
    {
        $string = trim($string);
        $string = str_replace(
            [
                'b',
                'c',
                'd',
                'e',
                'f',
                'g',
                'h',
                'i',
                'j',
                'k',
                'l',
                'n',
                'o',
                'q',
                'r',
                's',
                't',
                'u',
                'v',
                'w',
                'x',
                'y',
                'z',
                'B',
                'C',
                'D',
                'E',
                'F',
                'G',
                'H',
                'I',
                'J',
                'K',
                'L',
                'N',
                'O',
                'Q',
                'R',
                'S',
                'T',
                'U',
                'V',
                'W',
                'X',
                'Y',
                'Z',
                '¨',
                'º',
                '~',
                '·',
                '$',
                '%',
                '&',
                '°',
                '(',
                ')',
                '?',
                "'",
                '¡',
                '¿',
                '[',
                '^',
                '<code>',
                ']',
                '+',
                '}',
                '{',
                '¨',
                '´',
                '>',
                '< ',
                ';',
                ',',
            ],
            '',
            $string
        );

        return $string;
    }
}

if (! function_exists('mimeType')) {

    function mimeType($type)
    {
        $data = [
            'html' => 'text/html',
            'htm' => 'text/html',
            'shtml' => 'text/html',
            'css' => 'text/css',
            'xml' => 'text/xml',
            'gif' => 'image/gif',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'js' => 'application/javascript',
            'atom' => 'application/atom+xml',
            'rss' => 'application/rss+xml',
            'mml' => 'text/mathml',
            'txt' => 'text/plain',
            'jad' => 'text/vnd.sun.j2me.app-descriptor',
            'wml' => 'text/vnd.wap.wml',
            'htc' => 'text/x-component',
            'png' => 'image/png',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
            'wbmp' => 'image/vnd.wap.wbmp',
            'ico' => 'image/x-icon',
            'jng' => 'image/x-jng',
            'bmp' => 'image/x-ms-bmp',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'webp' => 'image/webp',
            'woff' => 'application/font-woff',
            'jar' => 'application/java-archive',
            'war' => 'application/java-archive',
            'ear' => 'application/java-archive',
            'json' => 'application/json',
            'hqx' => 'application/mac-binhex40',
            'doc' => 'application/msword',
            'pdf' => 'application/pdf',
            'ps' => 'application/postscript',
            'eps' => 'application/postscript',
            'ai' => 'application/postscript',
            'rtf' => 'application/rtf',
            'm3u8' => 'application/vnd.apple.mpegurl',
            'xls' => 'application/vnd.ms-excel',
            'eot' => 'application/vnd.ms-fontobject',
            'ppt' => 'application/vnd.ms-powerpoint',
            'wmlc' => 'application/vnd.wap.wmlc',
            'kml' => 'application/vnd.google-earth.kml+xml',
            'kmz' => 'application/vnd.google-earth.kmz',
            '7z' => 'application/x-7z-compressed',
            'cco' => 'application/x-cocoa',
            'jardiff' => 'application/x-java-archive-diff',
            'jnlp' => 'application/x-java-jnlp-file',
            'run' => 'application/x-makeself',
            'pl' => 'application/x-perl',
            'pm' => 'application/x-perl',
            'prc' => 'application/x-pilot',
            'pdb' => 'application/x-pilot',
            'rar' => 'application/x-rar-compressed',
            'rpm' => 'application/x-redhat-package-manager',
            'sea' => 'application/x-sea',
            'swf' => 'application/x-shockwave-flash',
            'sit' => 'application/x-stuffit',
            'tcl' => 'application/x-tcl',
            'tk' => 'application/x-tcl',
            'der' => 'application/x-x509-ca-cert',
            'pem' => 'application/x-x509-ca-cert',
            'crt' => 'application/x-x509-ca-cert',
            'xpi' => 'application/x-xpinstall',
            'xhtml' => 'application/xhtml+xml',
            'xspf' => 'application/xspf+xml',
            'zip' => 'application/zip',
            'epub' => 'application/epub+zip',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'kar' => 'audio/midi',
            'mp3' => 'audio/mpeg',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/x-m4a',
            'ra' => 'audio/x-realaudio',
            '3gpp' => 'video/3gpp',
            '3gp' => 'video/3gpp',
            'ts' => 'video/mp2t',
            'mp4' => 'video/mp4',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mov' => 'video/quicktime',
            'webm' => 'video/webm',
            'flv' => 'video/x-flv',
            'm4v' => 'video/x-m4v',
            'mng' => 'video/x-mng',
            'asx' => 'video/x-ms-asf',
            'asf' => 'video/x-ms-asf',
            'wmv' => 'video/x-ms-wmv',
            'avi' => 'video/x-msvideo',
        ];

        return $data["{$type}"];
    }
}

if (! function_exists('d')) {
    /**
     * Depura e imprime una o más variables sin terminar la ejecución del script.
     *
     * @param  mixed  ...$vars  Las variables a inspeccionar.
     * @return void
     */
    function d($vars)
    {
        $isCli = (php_sapi_name() == 'cli');
        if ($isCli) {
            echo "\033[96m" . "dump--------------------\n" . "\033[0m"; // Cian
            echo "\033[97m";
            var_dump($vars);
            echo "\033[0m";
            echo "\033[96m" . "end--------------------\n" . "\033[0m";
        } else {
            echo '<pre style="background-color: #e0f7fa; border: 1px solid #b2ebf2; padding: 10px; margin: 10px; border-radius: 4px; overflow-x: auto; font-family: monospace; font-size: 14px; line-height: 1.5;">';
            echo '<span style="color: #00BCD4;">dump--------------------</span><br>'; // Cian
            echo '<code style="color: #333;">';
            var_dump($vars);
            echo '</code>';
            echo '<span style="color: #00BCD4;"><br>end--------------------</span>';
            echo '</pre>';
        }
    }
}

if (! function_exists('is_ajax')) {
    function is_ajax()
    {
        if (
            isset($header['Authorization']) ||
            (
                empty($_SERVER['HTTP_X_REQUESTED_WITH']) == false &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        ) {
            return true;
        } else {
            return false;
        }
    }
}

if (! function_exists('public_url')) {
    function public_url($resource = '')
    {
        if ($resource == '') {
            return config('app.url') . '/public/';
        } else {
            return config('app.url') . '/public/' . trim($resource);
        }
    }
}

if (! function_exists('get_mes_name')) {
    /**
     * Retorna el nombre del mes en español a partir de un número de mes (1-12 o cadena "01"-"12").
     *
     * @param  int|string  $month
     * @return string
     */
    function get_mes_name($month)
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];
        $idx = intval($month);

        return isset($meses[$idx]) ? $meses[$idx] : '';
    }
}
