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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->enum('langue', ['fr', 'en'])->default('fr');
            $table->enum('theme', ['light', 'dark', 'system'])->default('system');
            $table->boolean('notifications_email')->default(true);
            $table->boolean('notifications_systeme')->default(true);
            $table->integer('items_par_page')->default(15);
            $table->string('format_date', 10)->default('d/m/Y');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
