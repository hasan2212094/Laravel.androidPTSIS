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
        Schema::table('after_services', function (Blueprint $table) {
         $table->dateTime('date_start')->nullable()->after('status_pekerjaan');
         $table->dateTime('date_end')->nullable()->after('date_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('after_services', function (Blueprint $table) {
              $table->dropColumn(['date_start', 'date_end']);
        });
    }
};
