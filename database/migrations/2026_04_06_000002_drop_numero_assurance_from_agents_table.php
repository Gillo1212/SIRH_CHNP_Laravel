<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('agents', 'numero_assurance')) {
            Schema::table('agents', function (Blueprint $table) {
                $table->dropColumn('numero_assurance');
            });
        }
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->string('numero_assurance', 50)->nullable();
        });
    }
};
