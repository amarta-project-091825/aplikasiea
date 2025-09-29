<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormSubmissionTableController extends Controller
{
    public function index()
    {
        $submissions = FormSubmission::orderBy('_id', 'desc')->paginate(15);

        $columns = [];
        foreach ($submissions as $s) {
            $columns = array_unique(array_merge($columns, array_keys($s->data ?? [])));
        }

        return view('admin.submission-table.index', compact('submissions', 'columns'));
    }

    public function edit($id)
    {
        $submission = FormSubmission::with('form')->findOrFail($id);
        $form = $submission->form;

        // Ambil semua nama field dari definisi form
        $formFields = collect($form->fields)->pluck('name')->toArray();

        // Ambil semua key dari data submission
        $submissionFields = array_keys($submission->data ?? []);

        // Gabungkan biar field lama & baru semuanya tampil
        $allFields = collect(array_unique(array_merge($formFields, $submissionFields)));

        return view('admin.submission-table.edit', compact('submission', 'form', 'allFields'));
    }

    public function update(Request $request, $id)
{
    $submission = FormSubmission::with('form')->findOrFail($id);

    $form = $submission->form;
    $data = $submission->data ?? [];
    $files = $submission->files ?? [];

    foreach ($form->fields as $field) {
        $name = $field['name'];
        $type = $field['type'] ?? 'text';

        if ($type === 'checkbox') {
            // pastikan checkbox selalu array
            $data[$name] = $request->input($name, []);
        }
        elseif ($type === 'file') {
            if ($request->hasFile($name)) {
                $uploaded = $request->file($name);

                // Replace file lama
                $files[$name] = [
                    'name' => $uploaded->getClientOriginalName(),
                    'mime' => $uploaded->getMimeType(),
                    'size' => $uploaded->getSize(),
                    'data' => 'data:' . $uploaded->getMimeType() . ';base64,' . base64_encode(file_get_contents($uploaded->getRealPath())),
                ];

                // opsional: simpan nama file ke data juga
                $data[$name] = $uploaded->getClientOriginalName();
            }
        }
        else {
            $data[$name] = $request->input($name);
        }
    }

    $submission->data = $data;
    $submission->files = $files;
    $submission->save();

    return redirect()
        ->route('admin.submission.table')
        ->with('success', 'Submission berhasil diperbarui.');
}


    public function destroy($id)
    {
        $submission = FormSubmission::findOrFail($id);
        $submission->delete();

        return redirect()->route('admin.submission.table')->with('success', 'Data berhasil dihapus.');
    }
}
