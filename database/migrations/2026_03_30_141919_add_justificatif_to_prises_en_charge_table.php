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
        Schema::table('prises_en_charge', function (Blueprint $table) {
            $table->string('justificatif_path', 500)->nullable()->after('type_prise');
        });
    }

    public function down(): void
    {
        Schema::table('prises_en_charge', function (Blueprint $table) {
            $table->dropColumn('justificatif_path');
        });
    }
};
