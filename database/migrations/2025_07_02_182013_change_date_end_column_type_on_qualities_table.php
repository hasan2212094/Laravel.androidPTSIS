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
            $table->dateTime('date_end')->nullable()->change(); // Ubah tipe kolom (bukan tambah kolom)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qualities', function (Blueprint $table) {
            $table->date('date_end')->nullable()->change(); // Kembalikan ke tipe semula jika di-rollback
        });
    }
};
