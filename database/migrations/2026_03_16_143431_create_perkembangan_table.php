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
        Schema::create('perkembangan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_siswa')->nullable();
            $table->string('kelas')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('nilai_motorik_halus', 10, 0)->nullable();
            $table->decimal('nilai_motorik_kasar', 10, 0)->nullable();
            $table->decimal('nilai_bahasa', 10, 0)->nullable();
            $table->decimal('nilai_sosial_kemandirian', 10, 0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perkembangan');
    }
};
