<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_masyarakat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id'); // relasi ke form builder
            $table->json('data'); // hasil submit form builder
            $table->unsignedBigInteger('status_id')->default(1); // default ke Pending
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('laporan_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_masyarakat');
    }
};
