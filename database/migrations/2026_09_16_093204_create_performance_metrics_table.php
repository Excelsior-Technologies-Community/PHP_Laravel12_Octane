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
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();

            $table->string('method', 10);
            $table->string('path', 500);
            $table->unsignedSmallInteger('status_code')->nullable();

            $table->decimal('duration_ms', 12, 3);
            $table->unsignedBigInteger('memory_usage_bytes')->nullable();
            $table->unsignedBigInteger('peak_memory_usage_bytes')->nullable();

            $table->boolean('is_slow')->default(false);
            $table->boolean('is_error')->default(false);

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index('duration_ms');
            $table->index('is_slow');
            $table->index('is_error');
            $table->index('status_code');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
    }
};