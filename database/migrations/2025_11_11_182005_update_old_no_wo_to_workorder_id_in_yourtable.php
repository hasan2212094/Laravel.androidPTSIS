<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('qualities', function (Blueprint $table) {
            // 1️⃣ hapus kolom lama
            if (Schema::hasColumn('qualities', 'old_no_wo')) {
                $table->dropColumn('old_no_wo');
            }

            // 2️⃣ tambahkan kolom relasi baru
            $table->foreignId('workorder_id')
                ->nullable()
                ->constrained('workorders')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('qualities', function (Blueprint $table) {
            // rollback ke kolom lama kalau perlu
            $table->string('old_no_wo')->nullable();
            $table->dropConstrainedForeignId('workorder_id');
        });
    }
};
