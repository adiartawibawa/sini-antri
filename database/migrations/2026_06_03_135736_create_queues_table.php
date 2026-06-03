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
            $table->uuid('uuid')->primary()->comment('Token unik untuk URL tiket pengunjung');
            $table->string('queue_number', 10)->comment('Nomor antrian, misal: A001');
            $table->integer('queue_order')->comment('Urutan numerik untuk sorting');
            $table->string('visitor_name');
            $table->string('purpose')->nullable()->comment('Keperluan kunjungan');
            $table->enum('status', ['waiting', 'called', 'serving', 'completed', 'skipped'])
                ->default('waiting');
            $table->foreignUuid('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'queue_order']);
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
