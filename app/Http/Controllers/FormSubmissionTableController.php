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
        $submission = FormSubmission::findOrFail($id);

    // Ambil semua input kecuali _token, _method
    $inputData = $request->except(['_token', '_method']);

    // Proses: kalau ada checkbox, pastikan tetap array (kalau unchecked bisa null → kita jadikan [])
    $form = $submission->form;
    foreach ($form->fields as $field) {
        $name = $field['name'];
        if (($field['type'] ?? '') === 'checkbox') {
            $inputData[$name] = $request->input($name, []); // default []
        }
    }

    // Simpan ke submission
    $submission->data = $inputData;
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
