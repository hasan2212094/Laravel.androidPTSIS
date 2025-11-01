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
        Schema::create('fabrikasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id_by')->nullable();
            $table->unsignedBigInteger('user_id_to')->nullable();
            $table->unsignedBigInteger('role_by')->nullable();
            $table->unsignedBigInteger('role_to')->nullable();
            $table->string('jenis_Pekerjaan');
            $table->foreignId('workorder_id')->nullable()->constrained('workorders')->nullOnDelete();
            $table->string('qty');
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->string('keterangan');
            $table->integer('status_pekerjaan')->default(0);
            $table->string('comment_done');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabrikasis');
    }
};
