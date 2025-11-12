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
        Schema::create('messengers', function (Blueprint $table) {
             $table->id();
             $table->unsignedBigInteger('user_id_by')->nullable(); // pengirim
             $table->unsignedBigInteger('user_id_to')->nullable();
             $table->string('title')->nullable(); 
             $table->text('message')->nullable(); 
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messengers');
    }
};
