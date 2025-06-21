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
            if (Schema::hasColumn('qualities', 'no_wo')) {
                $table->renameColumn('no_wo', 'old_no_wo');
            }
        });

        // Step 2: Tambah foreign key baru
        Schema::table('qualities', function (Blueprint $table) {
            $table->foreignId('no_wo')->nullable()->constrained('workorders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
          Schema::table('qualities', function (Blueprint $table) {
            $table->dropForeign(['no_wo']);
            $table->dropColumn('no_wo');
            $table->string('no_wo');

            if (Schema::hasColumn('qualities', 'old_no_wo')) {
                $table->dropColumn('old_no_wo');
            }
        });
    }
};
