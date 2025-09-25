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
    return view('admin.submission-table.edit', compact('submission', 'form'));
    }

    public function update(Request $request, $id)
    {
        $submission = FormSubmission::findOrFail($id);

        $data = $submission->data ?? [];
        foreach ($data as $key => $value) {
            $data[$key] = $request->input($key, $value);
        }

        $submission->data = $data;
        $submission->save();

        return redirect()->route('admin.submission.table')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $submission = FormSubmission::findOrFail($id);
        $submission->delete();

        return redirect()->route('admin.submission.table')->with('success', 'Data berhasil dihapus.');
    }
}
