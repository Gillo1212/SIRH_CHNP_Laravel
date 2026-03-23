<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->foreignId('id_service')
                  ->nullable()
                  ->after('photo')
                  ->constrained('services', 'id_service')
                  ->onDelete('set null');

            $table->foreignId('id_division')
                  ->nullable()
                  ->after('id_service')
                  ->constrained('divisions', 'id_division')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropForeign(['id_service']);
            $table->dropForeign(['id_division']);
            $table->dropColumn(['id_service', 'id_division']);
        });
    }
};
