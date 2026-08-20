<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */
    'app_mode' => env('APP_MODE', 'production'),

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode for End Users (Mercurio)
    |--------------------------------------------------------------------------
    |
    | When "maintenance_endusers" is true, requests to /mercurio/* and /api/*
    | are answered with a "fuera de servicio" page (HTML for Inertia, JSON
    | 503 for API consumers). Cajas admins (sessions with "tipfun") and any
    | other paths (/web/*, /cajas/*) are unaffected.
    |
    | "maintenance_endusers_message" overrides the default Spanish message.
    |
    */

    'maintenance_endusers' => (bool) env('APP_MAINTENANCE_ENDUSERS', false),

    'maintenance_endusers_message' => env(
        'MAINTENANCE_ENDUSERS_MESSAGE',
        'El sistema se encuentra en estado de actualización y mantenimiento.<br/>Con el fin de corregir errores y actualizar a versiones más seguras y óptimas que buscan la satisfacción de sus usuarios.'
    ),

    'dominio' => env('APP_DOMINIO', 'http://localhost'),

    'port' => env('APP_PORT', '443'),

    'api_mode' => env('API_MODE', 'production'),

    'host_api_user' => env('HOST_API_USER', '2'),
    'host_api_password' => env('HOST_API_PASSWORD', null),

    'integration' => env('APP_INTEGRATION', null),

    'api_flask_user' => env('API_FLASK_USER', null),
    'api_flask_password' => env('API_FLASK_PASSWORD', null),
    'api_whatsapp_user' => env('API_WHATSAPP_USER', null),
    'api_whatsapp_password' => env('API_WHATSAPP_PASSWORD', null),

    'host_portal_dev' => env('HOST_PORTAL_DEV', null),
    'host_portal_pro' => env('HOST_PORTAL_PRO', null),

    'use_cli' => env('USE_CLI', false),
    'portal' => env('PORTAL', null),
    'portal_key' => env('PORTAL_KEY'),
    'encriptation' => env('API_ENCRYPTION'),

    'sftp_sisu' => [
        'host' => env('SFTP_SISU_HOST', ''),
        'username' => env('SFTP_SISU_USERNAME', ''),
        'password' => env('SFTP_SISU_PASSWORD', ''),
        'privateKey' => env('SFTP_SISU_PRIVATE_KEY', ''),
        'privateKeyPassphrase' => env('SFTP_SISU_PRIVATE_KEY_PASSPHRASE', ''),
        'port' => env('SFTP_SISU_PORT', 22),
        'timeout' => env('SFTP_SISU_TIMEOUT', 30),
        'storage' => env('SFTP_SISU_STORAGE', ''),
    ],
    'epayco' => [
        'mode' => env('EPAYCO_MODE'),
        'public_key' => env('EPAYCO_PUBLIC_KEY'),
        'private_key' => env('EPAYCO_PRIVATE_KEY'),
        // Version del checkout: 1 = Standard (checkout.js), 2 = Smart Checkout (checkout-v2.js)
        'checkout_version' => (string) env('EPAYCO_CHECKOUT_VERSION', '1'),
        // Base de la API Apify (login + creacion de sesion Smart Checkout v2)
        'apify_url' => env('EPAYCO_APIFY_URL', 'https://apify.epayco.co'),
        // Credenciales del panel ePayco para validar x_signature (webhook confirmation).
        // Acepta EPAYCO_CUSTOMER_ID o su alias EPAYCO_P_CUST_ID_CLIENTE.
        'customer_id' => env('EPAYCO_CUSTOMER_ID', env('EPAYCO_P_CUST_ID_CLIENTE')),
        'p_key' => env('EPAYCO_P_KEY'),
        // Verificación TLS al consultar secure.epayco.co (default true).
        // Solo poner false en entornos dev con CA/proxy problemáticos.
        'verify_ssl' => filter_var(env('EPAYCO_HTTP_VERIFY_SSL', true), FILTER_VALIDATE_BOOLEAN),
    ],
];
