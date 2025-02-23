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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            // $table->string('name');
            // $table->string('title');
            // $table->text('description');
            // $table->date('date');
            // $table->text('description_end');
            // $table->date('date_end');
            $table->foreignId('user_id_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_by')->constrained('roles')->onDelete('cascade');
            $table->foreignId('user_id_to')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_to')->constrained('roles')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->date('date_start');
            $table->boolean('level_urgent');
            $table->boolean('status');
            $table->string('image');
            $table->text('finish_note');
            $table->date('date_end');
            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
