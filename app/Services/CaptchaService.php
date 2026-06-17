<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CaptchaService
{
    public const SESSION_KEY = 'cajas_captcha';

    public const SESSION_TS_KEY = 'cajas_captcha_ts';

    public const SESSION_KEY_PREFIX = 'cajas_captcha_';

    public const CHARSET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public static function generate(?int $length = null): string
    {
        $length = $length ?: (int) config('captcha.length', 5);
        $length = max(3, min(8, $length));

        $chars = self::CHARSET;
        $maxIndex = strlen($chars) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, $maxIndex)];
        }

        Session::put(self::SESSION_KEY, $code);
        Session::put(self::SESSION_TS_KEY, time());

        return $code;
    }

    public static function current(): ?string
    {
        return Session::get(self::SESSION_KEY);
    }

    public static function verify(?string $input): bool
    {
        if ($input === null || $input === '') {
            self::flush();

            return false;
        }

        $stored = Session::get(self::SESSION_KEY);
        $ts = Session::get(self::SESSION_TS_KEY);

        $ttlMinutes = (int) config('captcha.ttl_minutes', 5);
        $expired = $ts === null || (time() - (int) $ts) > ($ttlMinutes * 60);

        self::flush();

        if ($expired || $stored === null) {
            return false;
        }

        return hash_equals(strtoupper($stored), strtoupper(trim($input)));
    }

    public static function flush(): void
    {
        Session::forget(self::SESSION_KEY);
        Session::forget(self::SESSION_TS_KEY);
    }
}
