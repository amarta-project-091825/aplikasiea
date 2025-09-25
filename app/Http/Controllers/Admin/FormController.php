<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormFieldType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

$fieldTypes = FormFieldType::all();

class FormController extends Controller
{
   public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $forms = Form::when($q, fn($w) => $w->where('name', 'like', "%{$q}%")
                                        ->orWhere('slug','like',"%{$q}%"))
                    ->orderBy('_id','desc')
                    ->paginate(10)
                    ->withQueryString();

        return view('admin.forms.index', compact('forms','q'));
    }

    public function create()
    {
        $types = FormFieldType::orderBy('name')->get();
        return view('admin.forms.create', compact('types'));
    }

    public function store(Request $request)
    {
        $fields = json_decode($request->fields, true) ?? [];
        $request->merge(['fields' => $fields]);

        $validated = $request->validate([
            'name'        => ['required','string','max:150'],
            'slug'        => ['nullable','string','max:160'],
            'description' => ['nullable','string','max:1000'],
            'is_active'   => ['nullable','boolean'],
            // fields dikirim sebagai JSON (lihat view builder)
            'fields'      => ['required','array','min:1'],
            'fields.*.label'    => ['required','string','max:150'],
            'fields.*.name'     => ['required','string','max:150'],
            'fields.*.type'     => ['required','in:text,number,textarea,select,radio,checkbox,date,file,email,tel'],
            'fields.*.required' => ['boolean'],
        ]);

        $slug = $validated['slug'] ?: Str::slug($validated['name']);
        // pastikan unik
        if (Form::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::random(4);
        }

        $form = Form::create([
            'name'        => $validated['name'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
            'is_active'   => (bool)($validated['is_active'] ?? false),
            'fields'      => $validated['fields'],
        ]);

        return redirect()->route('admin.forms.edit', $form->_id)->with('status', 'Form created.');
    }

    public function edit(Form $form)
    {
        $types = FormFieldType::orderBy('name')->get();
        return view('admin.forms.edit', compact('form', 'types'));
    }

    public function update(Request $request, Form $form)
    {
        $fields = json_decode($request->fields, true) ?? [];
        $request->merge(['fields' => $fields]);

        $validated = $request->validate([
            'name'        => ['required','string','max:150'],
            'slug'        => ['required','string','max:160'],
            'description' => ['nullable','string','max:1000'],
            'is_active'   => ['nullable','boolean'],
            'fields'      => ['required','array','min:1'],
            'fields.*.label'    => ['required','string','max:150'],
            'fields.*.name'     => ['required','string','max:150'],
            'fields.*.type'     => ['required','in:text,number,textarea,select,radio,checkbox,date,file,email,tel'],
            'fields.*.required' => ['boolean'],
        ]);

        // pastikan slug unik (kecuali dirinya)
        if (Form::where('slug', $validated['slug'])->where('_id','!=',$form->_id)->exists()) {
            return back()->withErrors(['slug' => 'Slug sudah dipakai form lain.'])->withInput();
        }

        $form->update([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active'   => (bool)($validated['is_active'] ?? false),
            'fields'      => $validated['fields'],
        ]);

        return redirect()->route('admin.forms.edit', $form->_id)->with('status', 'Form updated.');
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return redirect()->route('admin.forms.index')->with('status', 'Form deleted.');
    }
}
