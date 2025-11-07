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
        Schema::create('electricals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id_by')->nullable();
            $table->unsignedBigInteger('user_id_to')->nullable();
            $table->unsignedBigInteger('role_by')->nullable()->change();
            $table->unsignedBigInteger('role_to')->nullable()->change();
            $table->foreignId('workorder_id')->nullable()->constrained('workorders')->nullOnDelete();
            $table->string('jenis_Pekerjaan');
            $table->string('qty');
            $table->string('spekifikasi');
            $table->date('date_start')->nullable()->change();
            $table->date('date_end')->nullable()->change();
            $table->string('keterangan');
            $table->integer('status_pekerjaan')->default(0);
            $table->string('comment_done')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricals');
    }
};
