<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mouvements', function (Blueprint $table) {
            // Traçabilité signature DRH — distinct de la validation (Intégrité CID)
            $table->unsignedBigInteger('signe_par')->nullable()->after('decision_generee');
            $table->foreign('signe_par')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('date_signature')->nullable()->after('signe_par');
        });
    }

    public function down(): void
    {
        Schema::table('mouvements', function (Blueprint $table) {
            $table->dropForeign(['signe_par']);
            $table->dropColumn(['signe_par', 'date_signature']);
        });
    }
};
