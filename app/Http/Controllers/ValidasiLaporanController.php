<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use App\Models\StatusLaporan;
use Illuminate\Http\Request;

class ValidasiLaporanController extends Controller
{
    public function index()
    {
        $laporan = LaporanMasyarakat::with('status')->orderBy('created_at','desc')->paginate(15);
        return view('admin.laporan-validasi.index', compact('laporan'));
    }

    public function edit($id)
    {
        $laporan = LaporanMasyarakat::findOrFail($id);
        $statusList = StatusLaporan::all();

        // data disimpan JSON, kita decode biar bisa ditampilkan
        $laporan->decoded = is_string($laporan->data) ? json_decode($laporan->data, true) : $laporan->data;

        return view('admin.laporan-validasi.edit', compact('laporan', 'statusList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required',
        ]);

        $laporan = LaporanMasyarakat::findOrFail($id);

        // cek apakah status berubah
        if ($laporan->status_id != $request->status_id) {
            $laporan->status_id = $request->status_id;
            $laporan->save();

            // Simpan ke history
            \App\Models\LaporanStatusHistory::create([
                'laporan_id' => $laporan->_id,
                'status_id' => $request->status_id,
                'changed_at' => now(),
            ]);
        }

        // cek apakah status nya selesai
        $status = \App\Models\StatusLaporan::find($request->status_id);
        if ($status && $status->label === 'Selesai') {
            // ambil semua history
            $history = \App\Models\LaporanStatusHistory::where('laporan_id', $laporan->_id)->get();

            // pindahkan ke laporan_selesai
            \App\Models\LaporanSelesai::create([
                'form_id' => $laporan->form_id,
                'data' => $laporan->data,
                'status_history' => $history->map(function($h) {
                    return [
                        'status_id' => $h->status_id,
                        'status_label' => optional($h->status)->label,
                        'changed_at' => $h->changed_at,
                    ];
                })->toArray(),
                'created_at' => $laporan->created_at,
                'updated_at' => now(),
            ]);

            // hapus laporan + history
            $laporan->delete();
            \App\Models\LaporanStatusHistory::where('laporan_id', $id)->delete();
        }

        return redirect()->route('admin.laporan-validasi.index')->with('success', 'Status laporan berhasil diperbarui.');
    }

 
    public function destroy($id)
        {
            $laporan = LaporanMasyarakat::findOrFail($id);
            $laporan->delete();

            return redirect()->route('admin.laporan-validasi.index')
                            ->with('success', 'Laporan berhasil dihapus.');
        }

}
