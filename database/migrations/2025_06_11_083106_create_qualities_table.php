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
        Schema::create('qualities', function (Blueprint $table) {
            $table->id();
            $table->string('project');
            $table->string('no_wo');
            $table->string('description');
            $table->string('responds');
            $table->string('image')->nullable();
            $table->date('date')->nullable(); // Jika pakai tanggal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualities');
    }
};
