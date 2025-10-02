<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use App\Models\StatusLaporan;
use Illuminate\Http\Request;

class ValidasiLaporanController extends Controller
{
    // Daftar semua laporan masuk
    public function index()
    {
        $laporan = LaporanMasyarakat::with('status')->orderBy('created_at','desc')->paginate(15);
        return view('admin.laporan-validasi.index', compact('laporan'));
    }

    // Form validasi per laporan
    public function edit($id)
    {
        $laporan = LaporanMasyarakat::findOrFail($id);
        $statusList = StatusLaporan::all();

        // data disimpan JSON, kita decode biar bisa ditampilkan
        $laporan->decoded = is_string($laporan->data) ? json_decode($laporan->data, true) : $laporan->data;

        return view('admin.laporan-validasi.edit', compact('laporan', 'statusList'));
    }

    // Update status laporan
    public function update(Request $request, $id)
    {
        $request->validate([
    'status_id' => 'required',
]);


        $laporan = LaporanMasyarakat::findOrFail($id);
        $laporan->status_id = $request->status_id;
        $laporan->save();

        return redirect()->route('admin.laporan-validasi.index')->with('success', 'Status laporan berhasil diperbarui.');
    }
}
