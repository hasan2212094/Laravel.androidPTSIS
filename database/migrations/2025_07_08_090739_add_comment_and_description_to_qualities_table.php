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
            $table->text('comment_done')->nullable()->after('status'); // tambahkan setelah kolom status
            $table->text('description_progress')->nullable()->after('comment_done');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qualities', function (Blueprint $table) {
             $table->dropColumn('comment_done');
            $table->dropColumn('description_progress');
        });
    }
};
