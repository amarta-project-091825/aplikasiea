<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_status', function (Blueprint $table) {
            $table->id();
            $table->string('nama_status'); // contoh: Pending, Ditolak, Ditindaklanjuti, Selesai
            $table->timestamps();
        });

        // Seed awal status
        DB::table('laporan_status')->insert([
            ['nama_status' => 'Pending', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status' => 'Ditolak', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status' => 'Ditindaklanjuti', 'created_at' => now(), 'updated_at' => now()],
            ['nama_status' => 'Selesai', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_status');
    }
};
