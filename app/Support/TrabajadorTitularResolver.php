<?php

namespace App\Support;

use Illuminate\Support\Collection;

class TrabajadorTitularResolver
{
    /**
     * @param  Collection<int, array<string, mixed>>|array<int, array<string, mixed>>  $mercurio31Records
     */
    public static function buildIndex(Collection|array $mercurio31Records): array
    {
        $index = [];

        foreach ($mercurio31Records as $record) {
            $cedtra = trim((string) ($record['cedtra'] ?? ''));
            if ($cedtra === '') {
                continue;
            }

            $index[$cedtra] = trim((string) ($record['nombre'] ?? ''));
        }

        return $index;
    }

    public static function resolve(string $cedtra, array $index): string
    {
        $cedtra = trim($cedtra);
        if ($cedtra === '') {
            return '';
        }

        return $index[$cedtra] ?? 'NO ENCONTRADO';
    }
}
