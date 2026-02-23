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
         Schema::table('electricals', function (Blueprint $table) {
        $table->longText('keterangan')->nullable()->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('electricals', function (Blueprint $table) {
        $table->string('keterangan', 255)->nullable()->change();
    });
    }
};
