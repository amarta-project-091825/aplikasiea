<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormSubmissionTableController extends Controller
{
    public function index(Request $request)
    {
        // Ambil daftar form untuk dropdown filter
        $forms = Form::orderBy('name')->get();

        // Query dasar submissions
        $query = FormSubmission::with('form')->orderBy('_id', 'desc');

        // Jika ada filter form_id dari dropdown, apply
        if ($request->filled('form_id')) {
            $query->where('form_id', $request->get('form_id'));
        }

        // Paginate dan bawa query string agar paging tetap membawa filter
        $submissions = $query->paginate(15)->appends($request->only('form_id'));

        // Ambil kolom dinamis dari kumpulan submissions saat ini
        $columns = $this->extractColumns($submissions);

        return view('admin.submission-table.index', compact('forms', 'submissions', 'columns'));
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

        $submission->data = $data;
        $submission->files = $files;
        $submission->save();

        // redirect kembali ke halaman yang sama, bawa form_id agar tetap pada filter
        return redirect()
            ->route('admin.submission.table', ['form_id' => $submission->form_id])
            ->with('success', 'Submission berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $submission = FormSubmission::findOrFail($id);
        $submission->delete();

        return redirect()->route('admin.submission.table')->with('success', 'Data berhasil dihapus.');
    }

    // optional helper untuk ambil kolom
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

    /**
     * Optional helper route: langsung buka index dengan filter form_id.
     * Kalau kamu butuh route /admin/submission-table/form/{formId} panggil method ini.
     */
    public function byForm($formId)
    {
        return redirect()->route('admin.submission.table', ['form_id' => $formId]);
    }
}
