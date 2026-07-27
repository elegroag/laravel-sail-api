<?php

use Illuminate\Database\Migrations\Migration;

require_once __DIR__.'/support/CreatesAuditoriaSolicitudTable.php';

return new class extends Migration
{
    public function up(): void
    {
        (new CreatesAuditoriaSolicitudTable)->up('mercurio31');
    }

    public function down(): void
    {
        (new CreatesAuditoriaSolicitudTable)->down('mercurio31');
    }
};
