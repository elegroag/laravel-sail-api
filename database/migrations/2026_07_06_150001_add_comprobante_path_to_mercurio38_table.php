<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('mercurio38') || Schema::hasColumn('mercurio38', 'comprobante_path')) {
            return;
        }

        DB::statement('ALTER TABLE `mercurio38` ADD COLUMN `comprobante_path` VARCHAR(255) NULL AFTER `ruuid`');
    }

    public function down(): void
    {
        if (! Schema::hasTable('mercurio38') || ! Schema::hasColumn('mercurio38', 'comprobante_path')) {
            return;
        }

        DB::statement('ALTER TABLE `mercurio38` DROP COLUMN `comprobante_path`');
    }
};
