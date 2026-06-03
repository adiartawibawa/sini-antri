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
        Schema::create('queue_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('prefix', 5)->default('A')->comment('Prefix nomor antrian');
            $table->integer('avg_service_minutes')->default(5)->comment('Estimasi menit per antrian');
            $table->boolean('reset_daily')->default(true)->comment('Reset nomor setiap hari');
            $table->integer('current_counter')->default(0)->comment('Counter nomor saat ini');
            $table->date('last_reset_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_settings');
    }
};
