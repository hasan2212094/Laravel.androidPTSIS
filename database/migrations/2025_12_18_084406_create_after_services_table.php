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
        Schema::create('after_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id_by')->nullable();
            $table->unsignedBigInteger('user_id_to')->nullable();
            $table->unsignedBigInteger('role_by')->nullable()->change();
            $table->unsignedBigInteger('role_to')->nullable()->change();
            $table->string('client');
            $table->string('jenis_kendaraan');
            $table->string('no_polisi');
            $table->string('no_rangka');
            $table->string('produk');
            $table->boolean('waranti');
            $table->date('date_start')->nullable()->change();
            $table->date('date_end')->nullable()->change();
            $table->longText('keterangan')->nullable()->change();
            $table->integer('status_pekerjaan')->default(0);
            $table->longText('comment_progress')->nullable()->change();
            $table->longText('comment_done')->nullable()->change();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('after_services');
    }
};
