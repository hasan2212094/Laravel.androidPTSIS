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
              $table->dropForeign(['no_wo']);
            
            // 2️⃣ Baru hapus kolomnya
            $table->dropColumn('no_wo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qualities', function (Blueprint $table) {
           $table->string('no_wo')->nullable();
        });
    }
};
