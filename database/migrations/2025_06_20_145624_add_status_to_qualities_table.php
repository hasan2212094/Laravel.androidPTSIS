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
            $table->integer('status')->default(0)->after('responds'); // 0 = pending
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('qualities', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
