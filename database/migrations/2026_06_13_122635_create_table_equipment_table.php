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
        Schema::create('table_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billiard_table_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->integer('broken_quantity')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_equipment');
    }
};
