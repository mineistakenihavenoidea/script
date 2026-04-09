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
        Schema::create('domain_perkembangans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('domain')->nullable();
            $table->string('kelompok_usia')->nullable();
            $table->string('indikator')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_perkembangans');
    }
};
