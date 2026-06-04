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
        Schema::create('table_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('billiard_table_id')
                ->constrained('billiard_tables')
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('start_time');

            $table->dateTime('end_time')
                ->nullable();

            $table->decimal('total_hours', 5, 2)
                ->default(0);

            $table->decimal('table_price', 10, 2)
                ->default(0);

            $table->enum('status', [
                'PLAYING',
                'FINISHED'
            ])->default('PLAYING');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_sessions');
    }
};
