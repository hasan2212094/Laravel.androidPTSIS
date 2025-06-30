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
        Schema::table('qualities', function (Blueprint $table) {
            $table->dropForeign(['role_by']);
            $table->dropForeign(['role_to']);
            $table->dropColumn(['role_by', 'role_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qualities', function (Blueprint $table) {
            $table->unsignedBigInteger('role_by')->nullable();
            $table->unsignedBigInteger('role_to')->nullable();

            $table->foreign('role_by')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('role_to')->references('id')->on('roles')->onDelete('set null');
        });
    }
};
