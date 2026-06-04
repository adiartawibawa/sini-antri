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
        Schema::table('queue_settings', function (Blueprint $table) {
            $table->integer('max_queue_limit')->default(0)->comment('0 means no limit');
            $table->boolean('is_system_open')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queue_settings', function (Blueprint $table) {
            $table->dropColumn(['max_queue_limit', 'is_system_open']);
        });
    }
};
