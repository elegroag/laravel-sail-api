<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('mercurio41') || Schema::hasColumn('mercurio41', 'comprobante_path')) {
            return;
        }

        DB::statement('ALTER TABLE `mercurio41` ADD COLUMN `comprobante_path` VARCHAR(255) NULL AFTER `ruuid`');
    }

    public function down(): void
    {
        if (! Schema::hasTable('mercurio41') || ! Schema::hasColumn('mercurio41', 'comprobante_path')) {
            return;
        }

        DB::statement('ALTER TABLE `mercurio41` DROP COLUMN `comprobante_path`');
    }
};
