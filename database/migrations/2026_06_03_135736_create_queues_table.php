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
        Schema::create('queues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('uuid', 36)->unique(); // token URL tiket pengunjung
            $table->string('queue_number', 10);
            $table->integer('queue_order');
            $table->string('visitor_name', 100);
            $table->string('purpose', 255)->nullable();
            $table->enum('status', ['waiting', 'called', 'serving', 'completed', 'skipped'])
                ->default('waiting');
            $table->foreignUuid('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'queue_order'], 'idx_status_order');
            $table->index('uuid', 'idx_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
