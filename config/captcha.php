<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Longitud del codigo captcha
    |--------------------------------------------------------------------------
    |
    | Numero de caracteres que se renderizaran en la imagen PNG del captcha.
    | Rango permitido: 3 a 8. Default: 5.
    |
    */
    'length' => (int) env('CAPTCHA_LENGTH', 5),

    /*
    |--------------------------------------------------------------------------
    | Tiempo de vida del captcha (minutos)
    |--------------------------------------------------------------------------
    |
    | El codigo expira si pasa este tiempo sin haber sido verificado.
    |
    */
    'ttl_minutes' => (int) env('CAPTCHA_TTL_MINUTES', 5),

    /*
    |--------------------------------------------------------------------------
    | Nivel de ruido grafico
    |--------------------------------------------------------------------------
    |
    | Controla cuantas lineas y puntos aleatorios se dibujan sobre la imagen.
    | Valores: low, medium, high. Default: medium.
    |
    */
    'noise_level' => env('CAPTCHA_NOISE_LEVEL', 'medium'),

    /*
    |--------------------------------------------------------------------------
    | Usar fuente TrueType
    |--------------------------------------------------------------------------
    |
    | Si esta habilitado y el archivo de fuente existe, se usa TTF para
    | dibujar el texto. Si no, se usa la fuente interna de GD.
    |
    */
    'use_ttf' => filter_var(env('CAPTCHA_USE_TTF', true), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Ruta a la fuente TrueType
    |--------------------------------------------------------------------------
    */
    'ttf_path' => env('CAPTCHA_TTF_PATH', '/usr/share/fonts/google-carlito-fonts/Carlito-Bold.ttf'),

    /*
    |--------------------------------------------------------------------------
    | Dimensiones de la imagen
    |--------------------------------------------------------------------------
    */
    'width' => (int) env('CAPTCHA_WIDTH', 140),

    'height' => (int) env('CAPTCHA_HEIGHT', 44),

];
