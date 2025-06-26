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
            $table->integer('status_relevan')->default(0)->after('responds');
            $table->text('comment')->nullable();
            $table->text('description_relevan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qualities', function (Blueprint $table) {
            $table->dropColumn(['status_relevan', 'comment', 'description_relevan']);
        });
    }
};
