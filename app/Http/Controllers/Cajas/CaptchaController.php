<?php

namespace App\Http\Controllers\Cajas;

use App\Http\Controllers\Controller;
use App\Services\CaptchaService;
use Illuminate\Http\Response;

class CaptchaController extends Controller
{
    public function image(): Response
    {
        if (! extension_loaded('gd')) {
            abort(500, 'La extension GD no esta habilitada en el servidor.');
        }

        $code = CaptchaService::generate();

        $width = (int) config('captcha.width', 140);
        $height = (int) config('captcha.height', 44);

        $image = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($image, 245, 245, 245);
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        $this->drawNoise($image, $width, $height);

        $this->drawText($image, $code, $width, $height);

        ob_start();
        imagepng($image);
        $binary = ob_get_clean();
        imagedestroy($image);

        return response($binary, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    protected function drawNoise(\GdImage $image, int $width, int $height): void
    {
        $level = (string) config('captcha.noise_level', 'medium');
        $lines = match ($level) {
            'low' => 3,
            'high' => 10,
            default => 6,
        };
        $dots = match ($level) {
            'low' => 30,
            'high' => 120,
            default => 70,
        };

        for ($i = 0; $i < $lines; $i++) {
            $color = imagecolorallocate($image, random_int(120, 200), random_int(120, 200), random_int(120, 200));
            imageline(
                $image,
                random_int(0, $width),
                random_int(0, $height),
                random_int(0, $width),
                random_int(0, $height),
                $color
            );
        }

        for ($i = 0; $i < $dots; $i++) {
            $color = imagecolorallocate($image, random_int(150, 220), random_int(150, 220), random_int(150, 220));
            imagesetpixel($image, random_int(0, $width - 1), random_int(0, $height - 1), $color);
        }
    }

    protected function drawText(\GdImage $image, string $code, int $width, int $height): void
    {
        $useTtf = (bool) config('captcha.use_ttf', true);
        $ttfPath = (string) config('captcha.ttf_path', '');

        $length = strlen($code);
        $slot = (int) floor($width / max(1, $length));

        for ($i = 0; $i < $length; $i++) {
            $char = $code[$i];
            $color = imagecolorallocate($image, random_int(20, 90), random_int(20, 90), random_int(80, 160));

            $x = $slot * $i + (int) floor($slot / 4);
            $yBase = (int) floor($height / 2);

            if ($useTtf && $ttfPath !== '' && is_readable($ttfPath)) {
                $size = random_int(18, 22);
                $y = $yBase + (int) floor($size / 2.5);
                imagettftext($image, $size, random_int(-12, 12), $x, $y, $color, $ttfPath, $char);
            } else {
                $y = $yBase - 8;
                imagestring($image, 5, $x, $y, $char, $color);
            }
        }
    }
}
