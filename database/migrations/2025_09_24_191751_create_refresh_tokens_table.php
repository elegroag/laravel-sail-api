<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();
            $table->integer('documento');
            $table->integer('coddoc');
            $table->string('token'); // hash del refresh token
            $table->timestamp('expires_at');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->unique(['documento', 'coddoc', 'token']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
