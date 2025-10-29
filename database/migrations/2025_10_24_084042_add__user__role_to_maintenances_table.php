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
        Schema::table('maintenances', function (Blueprint $table) {
           $table->unsignedBigInteger('user_id_by')->nullable();
           $table->unsignedBigInteger('user_id_to')->nullable();
           $table->unsignedBigInteger('role_by')->nullable();
          $table->unsignedBigInteger('role_to')->nullable();

        // Jika perlu foreign key:
        $table->foreign('user_id_by')->references('id')->on('users')->onDelete('set null');
        $table->foreign('user_id_to')->references('id')->on('users')->onDelete('set null');
        $table->foreign('role_by')->references('id')->on('roles')->onDelete('set null');
        $table->foreign('role_to')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            //
        });
    }
};
