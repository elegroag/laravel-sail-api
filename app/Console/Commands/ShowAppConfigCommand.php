<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowAppConfigCommand extends Command
{
    protected $signature = 'config:show-app
                            {--section= : Filtrar por prefijo de clave (ej. epayco, sftp_sisu)}';

    protected $description = 'Muestra los valores resueltos de config/app.php con secretos enmascarados';

    /**
     * Fragmentos de nombre de clave que se enmascaran (case-insensitive).
     *
     * @var list<string>
     */
    protected array $sensitiveFragments = [
        'password',
        'secret',
        'token',
        'private',
        'passphrase',
        'encriptation',
        'encryption',
    ];

    public function handle(): int
    {
        $app = config('app');

        if (! is_array($app) || $app === []) {
            $this->error('No se pudo leer config(app).');

            return self::FAILURE;
        }

        $rows = $this->flatten($app);
        $section = $this->option('section');

        if (is_string($section) && trim($section) !== '') {
            $prefix = rtrim(trim($section), '.').'.';
            $rows = array_values(array_filter(
                $rows,
                static fn (array $row): bool => str_starts_with($row[0], $prefix) || $row[0] === rtrim($prefix, '.')
            ));
        }

        if ($rows === []) {
            $this->warn('No hay claves que coincidan con el filtro.');

            return self::SUCCESS;
        }

        $this->info('Configuración app.* (secretos enmascarados)');
        $this->line('Fuente: config/app.php → config(\'app\')');
        $this->newLine();

        $this->table(['Clave', 'Valor'], $rows);

        $this->newLine();
        $this->comment('Los valores con key/password/secret/token/private/passphrase quedan enmascarados.');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<array{0: string, 1: string}>
     */
    protected function flatten(array $data, string $prefix = ''): array
    {
        $rows = [];

        foreach ($data as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                if ($value === []) {
                    $rows[] = [$path, $this->formatScalar($path, [])];

                    continue;
                }

                $rows = array_merge($rows, $this->flatten($value, $path));

                continue;
            }

            $rows[] = [$path, $this->formatScalar($path, $value)];
        }

        return $rows;
    }

    protected function formatScalar(string $path, mixed $value): string
    {
        $leaf = strtolower((string) str($path)->afterLast('.'));

        if ($this->isSensitive($leaf, $path)) {
            return $this->mask($value);
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        if (is_array($value)) {
            return '[]';
        }

        if (is_scalar($value)) {
            $string = (string) $value;

            return $string === '' ? '(vacío)' : $string;
        }

        return get_debug_type($value);
    }

    protected function isSensitive(string $leaf, string $path): bool
    {
        if ($leaf === 'key' || str_ends_with($leaf, '_key') || str_ends_with($leaf, 'key')) {
            // "key", "portal_key", "private_key", "p_key", "public_key", "privateKey"
            return true;
        }

        $haystack = strtolower($path);

        foreach ($this->sensitiveFragments as $fragment) {
            if (str_contains($leaf, $fragment) || str_contains($haystack, $fragment)) {
                return true;
            }
        }

        return false;
    }

    protected function mask(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '(vacío)';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (! is_scalar($value)) {
            return '****';
        }

        $string = (string) $value;
        $len = strlen($string);

        if ($len <= 4) {
            return '****';
        }

        return '****'.substr($string, -4).' (len='.$len.')';
    }
}
