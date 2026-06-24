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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Thêm FK users.role_id → roles.id sau khi bảng roles đã được tạo.
        // Cần làm ở đây vì migration users (0001_01_01) chạy trước migration này.
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop FK trên users trước khi drop bảng roles
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });

        Schema::dropIfExists('roles');
    }
};
