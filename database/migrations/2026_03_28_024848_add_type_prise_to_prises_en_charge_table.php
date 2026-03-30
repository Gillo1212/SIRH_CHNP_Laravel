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
            $table->string('type_prise', 100)->nullable()->after('ayant_droit');
            $table->boolean('exceptionnelle')->default(false)->after('type_prise');
            $table->unsignedBigInteger('validee_par')->nullable()->after('exceptionnelle');
            $table->foreign('validee_par')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('prises_en_charge', function (Blueprint $table) {
            $table->dropForeign(['validee_par']);
            $table->dropColumn(['type_prise', 'exceptionnelle', 'validee_par']);
        });
    }
};
