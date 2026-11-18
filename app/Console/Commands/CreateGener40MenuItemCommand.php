<?php

namespace App\Console\Commands;

use App\Models\MenuItem;
use Illuminate\Console\Command;

class CreateGener40MenuItemCommand extends Command
{
    protected $signature = 'cajas:menu-gener40
                            {--title=Acciones de módulos : Título del ítem de menú}
                            {--parent= : ID del menú padre (opcional)}
                            {--codapl=CA : Código de aplicación}
                            {--icon=list : Icono del ítem}
                            {--color=#7c3aed : Color del ítem}';

    protected $description = 'Crea el MenuItem del CRUD Gener40 solo si aún no existe';

    public function handle(): int
    {
        $codapl = trim((string) $this->option('codapl')) ?: 'CA';
        $controller = 'Gener40Controller';
        $action = 'index';
        $url = 'cajas/gener40/index';

        $existente = MenuItem::query()
            ->where('codapl', $codapl)
            ->where('controller', $controller)
            ->where('action', $action)
            ->first();

        if ($existente) {
            $this->info("El menú ya existe (id={$existente->id}): {$existente->title}");

            return self::SUCCESS;
        }

        $parentId = $this->option('parent');
        if ($parentId !== null && $parentId !== '') {
            $parentId = (int) $parentId;
            if (! MenuItem::query()->whereKey($parentId)->exists()) {
                $this->error("No existe el menú padre con id={$parentId}.");

                return self::FAILURE;
            }
        } else {
            $parentId = null;
        }

        $item = MenuItem::create([
            'title' => trim((string) $this->option('title')) ?: 'Acciones de módulos',
            'default_url' => $url,
            'icon' => trim((string) $this->option('icon')) ?: null,
            'color' => trim((string) $this->option('color')) ?: null,
            'nota' => 'CRUD de acciones de módulos (gener40)',
            'parent_id' => $parentId,
            'codapl' => $codapl,
            'controller' => $controller,
            'action' => $action,
        ]);

        $this->info("Menú creado (id={$item->id}): {$item->title}");
        $this->line("URL: {$item->default_url}");
        $this->line("Controller@Action: {$item->controller}@{$item->action}");
        if ($parentId) {
            $this->line("Parent: {$parentId}");
        } else {
            $this->warn('Sin parent_id: asígnalo luego en cajas/menu si debe quedar bajo un grupo.');
        }

        return self::SUCCESS;
    }
}
