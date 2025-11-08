<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use App\Models\LaporanSelesai;

class FormSubmissionTableController extends Controller
{
    public function index(Request $request)
    {
        
        $formId = $request->get('form_id');

        if (!$formId) {
            // default: pakai form pertama
            $firstForm = Form::orderBy('name')->first();
            if ($firstForm) {
                return redirect()->route('admin.submission.table', ['form_id' => $firstForm->_id]);
            }
            // kalau tidak ada form sama sekali
            return view('admin.submission-table.index', [
                'forms' => collect(),
                'submissions' => collect(),
                'columns' => [],
                'currentForm' => null,
            ]); 
        }

        $forms = Form::orderBy('name')->get();
        $currentForm = $forms->where('_id', $formId)->first();

        if (!$currentForm) {
            abort(404, 'Form tidak ditemukan');
        }

         $formLaporanId = '68ddd553e341db5b990dcb92'; // ganti kalau beda

        if ($formId == $formLaporanId) {
            // Ambil dari collection laporan_selesai
            $query = LaporanSelesai::where('form_id', $formId)
                ->orderBy('_id', 'desc');
        } else {
            // Ambil dari form_submissions seperti biasa
            $query = FormSubmission::with('form')
                ->where('form_id', $formId)
                ->orderBy('_id', 'desc');
        }

        $submissions = $query->paginate(15)->appends($request->only('form_id'));

        $columns = $this->extractColumns($submissions);

        foreach ($submissions as $submission) {
            $submission->decoded_data = is_string($submission->data)
                ? json_decode($submission->data, true)
                : $submission->data;
        }

        return view('admin.submission-table.index', compact('forms', 'currentForm', 'submissions', 'columns'));
    }

    public function edit($id)
    {
        $submission = FormSubmission::with('form')->findOrFail($id);
        $form = $submission->form;

        $formFields = collect($form->fields ?? [])->pluck('name')->toArray();
        $submissionFields = array_keys($submission->data ?? []);
        $allFields = collect(array_unique(array_merge($formFields, $submissionFields)));

        return view('admin.submission-table.edit', compact('submission', 'form', 'allFields'));
    }
    
    public function update(Request $request, $id)
    {
        $submission = FormSubmission::with('form')->findOrFail($id);
        $form = $submission->form;

        $data = $submission->data ?? [];
        $files = $submission->files ?? [];

        // Update setiap field form biasa
        foreach ($form->fields as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';

            if ($type === 'checkbox') {
                $data[$name] = $request->input($name, []);
            } elseif ($type === 'file') {
                if ($request->hasFile($name)) {
                    $uploaded = $request->file($name);
                    $files[$name] = [
                        'name' => $uploaded->getClientOriginalName(),
                        'mime' => $uploaded->getMimeType(),
                        'size' => $uploaded->getSize(),
                        'data' => 'data:' . $uploaded->getClientMimeType() . ';base64,' . base64_encode(file_get_contents($uploaded->getRealPath())),
                    ];
                    $data[$name] = $uploaded->getClientOriginalName();
                }
            } else {
                $data[$name] = $request->input($name);
            }
        }

        // ✅ SIMPAN DATA KOORDINAT MAP DRAWER JUGA
        if ($request->has('koordinat_latlng')) {
            $raw = $request->input('koordinat_latlng');

            if (!empty($raw)) {
                // Decode & simpan dalam bentuk array
                $decoded = json_decode($raw, true);
                $data['koordinat_latlng'] = $decoded ?: $raw; 
            } else {
                // Jika kosong → hapus koordinat
                unset($data['koordinat_latlng']);
            }
        }

        // Simpan
        $submission->data = $data;
        $submission->files = $files;
        $submission->save();

        return redirect()
            ->route('admin.submission.table', ['form_id' => $submission->form_id])
            ->with('success', 'Submission berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $submission = FormSubmission::findOrFail($id);
        $submission->delete();

        return redirect()->route('admin.submission.table', ['form_id' => $submission->form_id ?? null])
            ->with('success', 'Data berhasil dihapus.');
    }

    private function extractColumns($submissions): array
    {
        $columns = [];
        foreach ($submissions as $s) {
            $columns = array_unique(array_merge(
                $columns,
                array_keys($s->data ?? []),
                array_keys($s->files ?? [])
            ));
        }
        return $columns;
    }

    public function byForm($formId)
    {
        return redirect()->route('admin.submission.table', ['form_id' => $formId]);
    }

    public function batchDestroy(Request $request)
{
    $ids = $request->input('ids', []);
    if (empty($ids)) {
        return back()->with('error', 'Tidak ada data yang dipilih.');
    }

    $deletedCount = FormSubmission::whereIn('_id', $ids)->delete();

    return back()->with('success', $deletedCount . ' submission berhasil dihapus.');
}

}
