<?php

use Illuminate\Database\Migrations\Migration;

require_once __DIR__.'/support/CreatesAuditoriaSolicitudTable.php';

return new class extends Migration
{
    public function up(): void
    {
        (new CreatesAuditoriaSolicitudTable)->up('mercurio47');
    }

    public function down(): void
    {
        (new CreatesAuditoriaSolicitudTable)->down('mercurio47');
    }
};
