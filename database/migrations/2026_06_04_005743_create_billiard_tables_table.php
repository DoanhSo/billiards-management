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
        Schema::create('billiard_tables', function (Blueprint $table) {
            $table->id();

            $table->string('table_number')->unique();
            $table->enum('table_type', [
                'POOL',
                'SNOOKER',
                'CAROM'
            ]);

            $table->decimal('price_per_hour', 10, 2);

            $table->enum('status', [
                'AVAILABLE',
                'RESERVED',
                'PLAYING',
                'MAINTENANCE'
            ])->default('AVAILABLE');

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billiard_tables');
    }
};
