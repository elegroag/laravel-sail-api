<?php

return [
    'site_key' => env('CAPTCHA_GOOGLE_SITE_KEY'),
    'secret_key' => env('CAPTCHA_GOOGLE_SECRET_KEY'),
    'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
];
