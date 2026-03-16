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
        Schema::create('rekom_foreigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_siswa')->nullable();
            $table->string('nama_rekomendasi')->nullable();
            $table->string('jenis_rekomendasi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekom_foreigns');
    }
};
