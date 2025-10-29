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
        Schema::create('maintenances_image_dones', function (Blueprint $table) {
            $table->id();
             $table->foreignId('maintenance_id')->constrained('maintenances')->onDelete('cascade');
             $table->string('image_path_done');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances_image_dones');
    }
};
