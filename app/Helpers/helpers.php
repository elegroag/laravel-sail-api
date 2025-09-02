<?php

// app/Helpers/helpers.php

if (!function_exists('format_price')) {
    /**
     * Formatea un número como un precio con el símbolo de la moneda.
     *
     * @param float $amount La cantidad a formatear.
     * @return string El precio formateado.
     */
    function format_price(float $amount): string
    {
        // Puedes hacer esta lógica tan compleja como necesites
        return '$' . number_format($amount, 2);
    }
}

if (!function_exists('get_initials')) {
    /**
     * Obtiene las iniciales de un nombre completo.
     *
     * @param string $name El nombre completo.
     * @return string Las iniciales.
     */
    function get_initials(string $name): string
    {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        return $initials;
    }
}