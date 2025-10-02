<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;

class LaporanMasyarakatController extends Controller
{
    // tampilkan form publik berdasarkan slug form builder
   public function create()
    {
        $form = Form::where('slug', 'pengaduan_laporan')->firstOrFail();
        $fields = $form->fields; // sudah array, langsung dipakai

        return view('laporan.create', compact('form', 'fields'));
    }

    public function store(Request $request)
    {
        $form = Form::where('slug', 'pengaduan_laporan')->firstOrFail();
        $fields = $form->fields;

        $data = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';

            if ($type === 'file') {
                if ($request->hasFile($name)) {
                    $uploaded = $request->file($name);

                    // validasi ukuran max 1MB
                    if ($uploaded->getSize() > 1024 * 1024) {
                        return back()->withErrors([$name => 'Ukuran file terlalu besar (maksimal 1MB).']);
                    }

                    $data[$name] = [
                        'name' => $uploaded->getClientOriginalName(),
                        'mime' => $uploaded->getMimeType(),
                        'size' => $uploaded->getSize(),
                        'data' => 'data:' . $uploaded->getClientMimeType() . ';base64,' . base64_encode(file_get_contents($uploaded->getRealPath())),
                    ];
                }
            } else {
                $data[$name] = $request->input($name);
            }
        }

        // simpan ke database
        LaporanMasyarakat::create([
            'form_id'   => $form->_id,   // relasi ke form builder
            'data'      => $data,
            'status_id' => 1,            // default Pending
        ]);

        return redirect()->route('laporan.create')->with('success', 'Laporan berhasil dikirim. Menunggu validasi admin.');
    }
}
